<?php
namespace Wizkunde\WebSSO\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Logger extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $logFactory;
    protected $serverHelper;
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Wizkunde\WebSSO\Model\LogFactory $logFactory
    ) {

        parent::__construct($context);

        $this->logFactory = $logFactory;
        $this->scopeConfig = $scopeConfig;
        $this->serverHelper = $serverHelper;
    }

    /**
     * Write a log to the database
     *
     * @param array $data
     * @param $identifier
     * @param $additionalInfo
     * @param bool $status
     */
    public function createLog(array $data, $identifier, $additionalInfo, $status = true)
    {
        if($this->mustLogStatus($status)) {
            $newLog = $this->logFactory->create();
            $newLog->setDate(date('Y-m-d H:i:s'));
            $newLog->setIdentifier($identifier);
            $newLog->setAdditionalInfo($additionalInfo);
            $newLog->setStatus($status === true ? 'success' : 'failed');
            $newLog->setServer($this->serverHelper->getServerInfo()['identifier']);
            $newLog->setMappings(json_encode($data));
            $newLog->save();
        }
    }

    /**
     * Verify if we actually need to log this status
     *
     * @param bool $status
     * @return bool
     */
    public function mustLogStatus($status = true)
    {
        if($this->scopeConfig->getValue('wizkunde/logging/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
        {
            return ($status == false || $this->scopeConfig->getValue('wizkunde/logging/severity', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 'all');
        }

        return false;
    }
}
