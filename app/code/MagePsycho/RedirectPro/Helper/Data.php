<?php

namespace MagePsycho\RedirectPro\Helper;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $mode;
    private $temp;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MagePsycho\RedirectPro\Logger\Logger
     */
    protected $customLogger;

    /**
     * @var \MagePsycho\RedirectPro\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var Url
     */
    protected $urlHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \MagePsycho\RedirectPro\Logger\Logger $customLogger,
        \MagePsycho\RedirectPro\Helper\Config $configHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        Url $urlHelper
    ) {
        $this->customLogger            = $customLogger;
        $this->configHelper            = $configHelper;
        $this->customerSession         = $customerSession;
        $this->storeManager            = $storeManager;
        $this->customerUrl             = $customerUrl;
        $this->moduleList              = $moduleList;
        $this->urlHelper               = $urlHelper;
        parent::__construct($context);

        $this->_initialize();
    }

    protected function _initialize()
    {
        $field = base64_decode('ZG9tYWluX3R5cGU=');
        if ($this->configHelper->getConfigValue(base64_decode('bWFnZXBzeWNob19yZWRpcmVjdHByby9nZW5lcmFsLw==') . $field) == 1) {
            $key        = base64_decode('cHJvZF9saWNlbnNl');
            $this->mode = base64_decode('cHJvZHVjdGlvbg==');
        } else {
            $key        = base64_decode('ZGV2X2xpY2Vuc2U=');
            $this->mode = base64_decode('ZGV2ZWxvcG1lbnQ=');
        }
        $this->temp = $this->configHelper->getConfigValue(base64_decode('bWFnZXBzeWNob19yZWRpcmVjdHByby9nZW5lcmFsLw==') . $key);
    }

    public function getMessage()
    {
        $message = base64_decode(
            'WW91IGFyZSB1c2luZyB1bmxpY2Vuc2VkIHZlcnNpb24gb2YgJ0N1c3RvbSBSZWRpcmVjdCBQcm8nIGV4dGVuc2lvbiBmb3IgZG9tYWluOiB7e0RPTUFJTn19LiBQbGVhc2UgZW50ZXIgYSB2YWxpZCBMaWNlbnNlIEtleSBmcm9tIFN0b3JlcyAmcmFxdW87IENvbmZpZ3VyYXRpb24gJnJhcXVvOyBNYWdlUHN5Y2hvICZyYXF1bzsgQ3VzdG9tIFJlZGlyZWN0IFBybyAmcmFxdW87IEdlbmVyYWwgU2V0dGluZ3MgJnJhcXVvOyBMaWNlbnNlIEtleS4gSWYgeW91IGRvbid0IGhhdmUgb25lLCBwbGVhc2UgcHVyY2hhc2UgYSB2YWxpZCBsaWNlbnNlIGZyb20gWyB3d3cubWFnZXBzeWNoby5jb20gXSBvciB5b3UgY2FuIGRpcmVjdGx5IGVtYWlsIHRvIFsgaW5mb0BtYWdlcHN5Y2hvLmNvbSBdLg=='
        );
        $message = str_replace('{{DOMAIN}}', $this->getDomain(), $message);

        return $message;
    }

    public function getDomain()
    {
        $domain     = $this->_urlBuilder->getBaseUrl();
        $baseDomain = $this->urlHelper->getBaseDomain($domain);

        return $baseDomain;
    }

    public function checkEntry($domain, $serial)
    {
        $salt = sha1(base64_decode('bTItbG9naW5yZWRpcmVjdHBybw=='));
        if (sha1($salt . $domain . $this->mode) == $serial) {
            return true;
        }

        return false;
    }

    public function isValid()
    {
        $temp = $this->temp;
        if (!$this->checkEntry($this->getDomain(), $temp)) {
            return false;
        }

        return true;
    }

    public function isActive($storeId = null)
    {
        return $this->configHelper->isActive($storeId);
    }

    public function isFxnSkipped()
    {
        if (!$this->isActive()) {
            return true;
        }
        return false;
    }

    public function getConfigHelper()
    {
        return $this->configHelper;
    }

    public function getExtensionVersion()
    {
        $moduleCode = 'MagePsycho_RedirectPro';
        $moduleInfo = $this->moduleList->getOne($moduleCode);
        return $moduleInfo['setup_version'];
    }

    public function getDomainFromSystemConfig()
    {
        $websiteCode = $this->_getRequest()->getParam('website');
        $storeCode   = $this->_getRequest()->getParam('store');
        $xmlPath     = 'web/unsecure/base_url';
        if (!empty($storeCode)) {
            $domain = $this->scopeConfig->getValue(
                $xmlPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeCode
            );
        } else if (!empty($websiteCode)) {
            $domain = $this->scopeConfig->getValue(
                $xmlPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $websiteCode
            );
        } else {
            $domain = $this->scopeConfig->getValue(
                $xmlPath
            );
        }
        return $domain;
    }

    /**
     * Logging Utility
     *
     * @param $message
     * @param bool|false $useSeparator
     */
    public function log($message, $useSeparator = false)
    {
        if ($this->configHelper->isActive()
            && $this->configHelper->getDebugStatus()
        ) {
            if ($useSeparator) {
                $this->customLogger->customLog(str_repeat('=', 100));
            }

            $this->customLogger->customLog($message);
        }
    }

    public function getRedirectToParamUrl()
    {
        $redirectToParamUrl = '';
        if ($redirectToParam = $this->getConfigHelper()->getRedirectToParam()) {
            $redirectToParamUrl = $this->_getRequest()->getParam($redirectToParam);
        }
        return $redirectToParamUrl;
    }

    public function getLoginRedirectionUrl($groupId = null)
    {
        if (empty($groupId)) {
            $groupId = $this->getCurrentGroupId();
        }

        $redirectionUrl = $this->getLoginUrlByGroup($groupId);
        if (empty($redirectionUrl)) {
            $redirectionUrl = $this->getConfigHelper()->getDefaultLoginUrl();
        }
        $this->log('getLoginRedirectionUrl()::raw::' . $redirectionUrl);

        if (!empty($redirectionUrl)) {
            $redirectionUrl = $this->_processUrlParams($redirectionUrl);
        }

        // default Url
        if (empty($redirectionUrl)) {
            $redirectionUrl    = $this->customerUrl->getAccountUrl();
        }
        $this->log('getLoginRedirectionUrl()::final::' . $redirectionUrl);
        return $redirectionUrl;
    }
        
    public function getCurrentGroupId()
    {
        $customer = $this->customerSession->getCustomer();
        return $customer->getGroupId();
    }

    public function getLoginUrlByGroup($groupId)
    {
        $groupToLoginData   = $this->getConfigHelper()->getGroupLoginUrl();
        $groupToLoginUrls   = $this->_prepareGroupWiseData($groupToLoginData);
        $redirectUrl        = isset($groupToLoginUrls[$groupId]) ? $groupToLoginUrls[$groupId] : '';
        return $redirectUrl;
    }

    /**
     * @param $dbData
     *
     * @return array
     */
    protected function _prepareGroupWiseData($dbData)
    {
        if (empty($dbData)) {
            return [];
        }

        $prepareData = unserialize($dbData);
        if (!is_array($prepareData)) {
            $prepareData = [];
        }

        return $prepareData;
    }

    /**
     * Parse redirection params and prepare the final url
     *
     * @param $redirectionUrl
     *
     * @return mixed|string
     */
    protected function _processUrlParams($redirectionUrl)
    {
        // these variables represent a complete url
        if (stripos($redirectionUrl, '{{assigned_base_url}}') !== false) { //Assigned base url
            $redirectionUrl    = str_replace('{{assigned_base_url}}', $this->getAssignedBaseUrl(), $redirectionUrl);
        }
        if (stripos($redirectionUrl, '{{referer}}') !== false) { //Referer
            $redirectionUrl    = str_replace('{{referer}}', $this->customerSession->getBeforeAuthUrlClrp(), $redirectionUrl);
        }

        if (stripos($redirectionUrl, '{{redirect_to}}') !== false) { //Redirect to using query param
            $redirectionUrl = str_replace('{{redirect_to}}', $this->customerSession->getRedirectParamUrl(), $redirectionUrl);
        }
        
        //convert relative to absolute url
        $redirectionUrl = $this->convertRelToAbsoulteUrl($redirectionUrl);
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();
            if (stripos($redirectionUrl, '{{user_name}}') !== false) { //User Full Name
                $redirectionUrl = str_replace('{{user_name}}', $customer->getName(), $redirectionUrl);
            }
            if (stripos($redirectionUrl, '{{user_email}}') !== false) { //User Email
                $redirectionUrl = str_replace('{{user_email}}', $customer->getEmail(), $redirectionUrl);
            }
            if (stripos($redirectionUrl, '{{user_id}}') !== false) { //User Id
                $redirectionUrl = str_replace('{{user_id}}', $customer->getId(), $redirectionUrl);
            }
            if (stripos($redirectionUrl, '{{user_group_id}}') !== false) { //User Group Id
                $redirectionUrl    = str_replace('{{user_group_id}}', $customer->getGroupId(), $redirectionUrl);
            }
        }

        $redirectionUrl = trim($redirectionUrl, '/');
        return $redirectionUrl;
    }

    /**
     * Get assigned website base url of a customer
     *
     * @return mixed
     */
    public function getAssignedBaseUrl()
    {
        $assignedBaseUrl = $this->getBaseUrl();
        if ($this->customerSession->isLoggedIn()) {
            $customer           = $this->customerSession->getCustomer();
            $website            = $this->storeManager->getWebsite($customer->getWebsiteId());
            $assignedBaseUrl    = $website->getDefaultStore()->getBaseUrl();
        }
        return $assignedBaseUrl;
    }

    public function isAbsoluteUrl($url)
    {
        return stripos($url, 'http://') !== false || stripos($url, 'https://') !== false;
    }

    public function convertRelToAbsoulteUrl($relUrl)
    {
        if ($this->isAbsoluteUrl($relUrl)) {
            return $relUrl;
        }
        return $this->getBaseUrl() . ltrim($relUrl, '/');
    }

    public function getAccountRedirectionUrl($groupId = null)
    {
        if (empty($groupId)) {
            $groupId = $this->getCurrentGroupId();
        }

        $redirectionUrl = $this->getAccountUrlByGroup($groupId);
        if (empty($redirectionUrl)) {
            $redirectionUrl = $this->getConfigHelper()->getDefaultAccountUrl();
        }
        $this->log('getAccountRedirectionUrl()::raw::' . $redirectionUrl);

        if (!empty($redirectionUrl)) {
            $redirectionUrl = $this->_processUrlParams($redirectionUrl);
        }

        // default Url
        if (empty($redirectionUrl)) {
            $redirectionUrl    = $this->customerUrl->getAccountUrl();
        }
        $this->log('getAccountRedirectionUrl()::final::' . $redirectionUrl);
        return $redirectionUrl;
    }

    public function getAccountUrlByGroup($groupId)
    {
        $groupToAccountData   = $this->getConfigHelper()->getGroupAccountUrl();
        $groupToAccountUrls   = $this->_prepareGroupWiseData($groupToAccountData);
        $redirectUrl          = isset($groupToAccountUrls[$groupId]) ? $groupToAccountUrls[$groupId] : '';
        return $redirectUrl;
    }

    public function getLogoutRedirectionUrl($groupId = null)
    {
        if (empty($groupId)) {
            $groupId = $this->getCurrentGroupId();
        }

        $redirectionUrl = $this->getLogoutUrlByGroup($groupId);
        if (empty($redirectionUrl)) {
            $redirectionUrl = $this->getConfigHelper()->getDefaultLogoutUrl();
        }
        $this->log('getLogoutRedirectionUrl()::raw::' . $redirectionUrl);

        if (!empty($redirectionUrl)) {
            $redirectionUrl = $this->_processUrlParams($redirectionUrl);
        }

        // default Url
        if (empty($redirectionUrl)) {
            $redirectionUrl = $this->getBaseUrl();
        }
        $this->log('getLogoutRedirectionUrl()::final::' . $redirectionUrl);
        return $redirectionUrl;
    }

    public function getLogoutUrlByGroup($groupId)
    {
        $groupToLogoutData   = $this->getConfigHelper()->getGroupLogoutUrl();
        $groupToLogoutUrls   = $this->_prepareGroupWiseData($groupToLogoutData);
        $redirectUrl        = isset($groupToLogoutUrls[$groupId]) ? $groupToLogoutUrls[$groupId] : '';
        return $redirectUrl;
    }

    public function getNewsletterRedirectionUrl()
    {
        $redirectionUrl = $this->getConfigHelper()->getNewsletterUrl();
        $this->log('getNewsletterRedirectionUrl()::raw::' . $redirectionUrl);

        if (!empty($redirectionUrl)) {
            $redirectionUrl = $this->_processUrlParams($redirectionUrl);
        }
        $this->log('getNewsletterRedirectionUrl()::final::' . $redirectionUrl);
        return $redirectionUrl;
    }

    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_WEB,
            true
        );
    }

    public function getCustomLogoutMessage()
    {
        $message = $this->getConfigHelper()->getLogoutMessage();
        $this->log('getCustomLogoutMessage()::raw::' . $message);

        if (!empty($message)) {
            $message = sprintf(
                $message,
                $this->getConfigHelper()->getLogoutDelay()
            );
        }
        $this->log('getCustomLogoutMessage()::final::' . $message);
        return $message;
    }

    public function getAccountSuccessMessage($groupId = null)
    {
        if (empty($groupId)) {
            $groupId = $this->getCurrentGroupId();
        }

        $message = $this->getAccountMessageByGroup($groupId);
        if (empty($message)) {
            $message = $this->getConfigHelper()->getDefaultAccountMessage();
        }
        $this->log('getAccountSuccessMessage()::raw::' . $message);

        $message = sprintf($message, $this->storeManager->getStore()->getFrontendName());
        $this->log('getAccountSuccessMessage()::final::' . $message);
        return $message;
    }

    public function getAccountMessageByGroup($groupId)
    {
        $groupToAccountData         = $this->getConfigHelper()->getGroupAccountMessage();
        $groupToAccountMessages     = $this->_prepareGroupWiseData($groupToAccountData);
        $message                    = isset($groupToAccountMessages[$groupId]) ? $groupToAccountMessages[$groupId] : '';
        return $message;
    }
}