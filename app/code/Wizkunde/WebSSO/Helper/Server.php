<?php
namespace Wizkunde\WebSSO\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Server extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $serverModel = null;
    private $serverModelFactory;
    private $customerModel;
    private $mappingFactory;
    private $triggerReload = false;
    private $stateHelper;

    private $logoBlock;
    private $cmsPage;
    private $remoteAddress;

    /**
     * @var array
     */
    private $autoDiscoveryMappings = array(
        'given_name' => 'firstname',
        'family_name' => 'lastname',
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'middlename' => 'middlename',
        'email' => 'email',
        'profile.email' => 'email',
        'street' => 'shipping_street',
        'city' => 'shipping_city',
        'postcode' => 'shipping_postcode',
        'telephone' => 'shipping_telephone',
        'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress' => 'email',
        'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname' => 'firstname',
        'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname' => 'lastname',
        'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/upn' => 'email',
        'urn:oid:2.5.4.42' => 'firstname',
        'urn:oid:0.9.2342.19200300.100.1.3' => 'email',
        'urn:oid:2.5.4.4' => 'lastname'
    );

    /**
     * Server constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Wizkunde\WebSSO\Model\ServerFactory $serverModelFactory
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param \Wizkunde\WebSSO\Model\MappingFactory $mappingFactory
     * @param \Magento\Theme\Block\Html\Header\Logo $logoBlock
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Wizkunde\WebSSO\Model\ServerFactory $serverModelFactory,
        \Magento\Customer\Model\Customer $customerModel,
        \Wizkunde\WebSSO\Model\MappingFactory $mappingFactory,
        \Magento\Theme\Block\Html\Header\Logo $logoBlock,
        \Magento\Cms\Model\Page $page,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\App\State $stateHelper
    ) {
    
        parent::__construct($context);

        $this->serverModelFactory = $serverModelFactory;
        $this->customerModel = $customerModel;
        $this->mappingFactory = $mappingFactory;
        $this->logoBlock = $logoBlock;
        $this->cmsPage = $page;
        $this->remoteAddress = $remoteAddress;
        $this->stateHelper = $stateHelper;
    }

    public function getServerInfo()
    {
        if ($this->getServerId() != false) {
            $this->serverModel = $this->serverModelFactory->create();
            $this->serverModel->load($this->getServerId());
            return $this->serverModel->getData();
        }

        return [];
    }

    private function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = []; //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * Return the mapped mapping data
     *
     * @param $userData
     * @return mixed
     */
    public function getMappings($userData)
    {
        $returnData = $userData;

        $this->serverModel = $this->serverModelFactory->create();
        $this->serverModel->load($this->getServerId());

        $this->createAndAutoDiscover($userData);

        if($this->triggerReload)
        {
            $this->triggerReload = false;
            $this->serverModel = $this->serverModelFactory->create();
            $this->serverModel->load($this->getServerId());
        }

        foreach ($this->serverModel->getData('mappings') as $mapping) {
            $externalValue = $this->getExternalValue($mapping, $returnData);

            if ($externalValue != '' || $mapping['transform'] == 'default') {
                $returnData[$mapping['internal']] = $this->transformMapping($mapping, $externalValue);
            }
        }

        return $returnData;
    }

    /**
     * @param $userData
     * @param string $concatString
     */
    public function createAndAutoDiscover($userData, $concatString = '')
    {
        foreach($userData as $key => $value)
        {
            if(is_string($key))
            {
                $found = false;
                $keyToSet = ($concatString != '') ? $concatString . '.' . $key : $key;

                foreach($this->serverModel->getData('mappings') as $mapping)
                {
                    if($mapping['external'] == $keyToSet) {
                        $found = true;
                    }
                }

                if(!$found)
                {
                    $this->triggerReload = true;

                    $externalData = array(
                        'value' => $keyToSet,
                        'transform' => ($concatString != '') ? 'split' : 'string',
                        'extra' => ''
                    );

                    // Create it
                    $mapping = $this->mappingFactory->create();
                    $mapping->setServerId($this->getServerId());
                    $mapping->setExternal(serialize($externalData));
                    $mapping->setName($key);

                    if(isset($this->autoDiscoveryMappings[$key]))
                    {
                        $mapping->setInternal($this->autoDiscoveryMappings[$key]);
                    }

                    $mapping->save();
                }
            } else if(is_array($key)) {
                $this->createAndAutoDiscover($value, $concatString . '.' . $key);
            }
        }
    }

    /**
     * @param $externalValue
     * @param $returnData
     * @return array|string
     */
    private function getExternalValue($externalValue, $returnData)
    {
        if ($externalValue['transform'] == 'split' &&
            strpos($externalValue['external'], '.') !== false) {
            $fields = explode('.', $externalValue['external']);
            $dataSet = $returnData;
            $fieldCount = count($fields);

            foreach ($fields as $i => $field) {
                if ($i == $fieldCount-1) {
                    if (isset($dataSet[$field])) {
                        return $dataSet[$field];
                    }
                } else {
                    if (isset($dataSet[$field])) {
                        $dataSet = $dataSet[$field];
                    }
                }
            }
        } else {
            if (isset($returnData[$externalValue['external']])) {
                return $returnData[$externalValue['external']];
            }
        }

        return '';
    }

    /**
     * Transform the incoming mapping
     *
     * @param $externalData
     * @param $value
     * @return string
     */
    private function transformMapping($externalData, $value = '')
    {
        switch ($externalData['transform']) {
            case 'default':
            case 'split':
                $value = ($value == '') ? $externalData['extra'] : $value;
                break;
            case 'password':
                $value = $this->customerModel->hashPassword($value);
                break;
            case 'before':
                $data = explode($externalData['extra'], $value);
                $value = array_shift($data);
                break;
            case 'after':
                $data = explode($externalData['extra'], $value);
                array_shift($data);
                $value = implode($externalData['extra'], $data);
                break;
            case 'preg':
                preg_match($externalData['extra'], $value, $matches, PREG_OFFSET_CAPTURE);
                $value = (isset($matches[0]) && is_array($matches[0])) ? current($matches[0]) : $value;
                break;
            default:
                break;
        }

        return $value;
    }

    /**
     * Get the metadata that is set in Magento configuration
     *
     * @return mixed
     */
    public function getServerId()
    {
        if ($this->_getRequest()->getParam('server') != null) {
            $serverIdentifier = strip_tags(urlencode($this->_getRequest()->getParam('server')));
        } else {
            if($this->stateHelper->getAreaCode() === \Magento\Framework\App\Area::AREA_ADMINHTML) {
                $serverIdentifier = $this->getConfig('wizkunde/websso/backend_server');
            } else {
                $serverIdentifier = $this->getConfig('wizkunde/websso/frontend_server');
            }
        }

        if (!isset($serverIdentifier) || $serverIdentifier == '') {
            return false;
        }

        return $serverIdentifier;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get config data frontend enabled
     *
     * @return mixed
     */
    public function checkFrontendEnabled()
    {
        return (bool)$this->getConfig('wizkunde/websso/enabled_frontend');
    }

    /**
     * Get config data backend enabled
     *
     * @return mixed
     */
    public function checkBackendEnabled()
    {
        return (bool)$this->getConfig('wizkunde/websso/enabled_backend');
    }

    /**
     * Get config data forced
     *
     * @return mixed
     */
    public function checkForcedLogin()
    {
        return (bool)$this->getConfig('wizkunde/firewall/forced');
    }

    /**
     * Check if IP or CMS Page are whitelisted
     *
     * @return bool
     */
    public function isWhitelisted()
    {
        if($this->isIpWhitelisted() || $this->isPageWhitelisted()) {
            return true;
        }

        return false;
    }

    /**
     * Whitelist IP Addresses
     *
     * @return bool
     */
    public function isIpWhitelisted()
    {
        $validIps = array();

        $ipWhitelist = $this->getConfig('wizkunde/firewall/ip_whitelist');

        if(strpos($ipWhitelist, ',') !== false) {
            $validIps = explode(',', $ipWhitelist);
        } else {
            $validIps[] = $ipWhitelist;
        }

        // If the IP is whitelisted, do not show the SSO page
        if(in_array($this->remoteAddress->getRemoteAddress(), $validIps)) {
            return true;
        }

        return false;
    }

    /**
     * Whitelist CMS Pages
     *
     * @return bool
     */
    public function isPageWhitelisted()
    {
        if ($this->cmsPage->getId() == false) {
            return true;
        }

        $cmsWhitelist = $this->getConfig('wizkunde/firewall/cms_whitelist');

        if($cmsWhitelist) {
            $cmsPages = explode(',', $cmsWhitelist);
            foreach ($cmsPages as $cmsPage) {
                $delimeterPosition = strrpos($cmsPage, '|');
                if ($delimeterPosition) {
                    $cmsPage = substr($cmsPage, 0, $delimeterPosition);
                }

                if ($cmsPage == $this->cmsPage->getIdentifier()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if the current page is a CMS page but not home
     *
     * @return bool
     */
    protected function isCmsPage()
    {
        if($this->logoBlock->isHomePage()) {
            return false;
        }

        return ($this->cmsPage->getId());
    }
}
