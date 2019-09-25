<?php

namespace MagePsycho\StoreRestrictionPro\Helper;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $mode, $temp;

    /**
     * @var \MagePsycho\StoreRestrictionPro\Logger\Logger
     */
    protected $customLogger;

    /**
     * @var \MagePsycho\StoreRestrictionPro\Helper\Config
     */
    protected $configHelper;

    /**
     * @var Url
     */
    protected $urlHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \MagePsycho\StoreRestrictionPro\Logger\Logger $customLogger,
        \MagePsycho\StoreRestrictionPro\Helper\Config $configHelper,
        Url $urlHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->customLogger    = $customLogger;
        $this->configHelper    = $configHelper;
        $this->urlHelper       = $urlHelper;
        $this->storeManager    = $storeManager;
        $this->moduleList      = $moduleList;
        $this->customerSession = $customerSession;
        $this->customerUrl     = $customerUrl;
        $this->pageFactory     = $pageFactory;
        $this->productFactory  = $productFactory;

        parent::__construct($context);

        $this->_initialize();
    }

    protected function _initialize()
    {
        $field = base64_decode('ZG9tYWluX3R5cGU=');
        if ($this->configHelper->getConfigValue('magepsycho_storerestrictionpro/general/' . $field) == 1) {
            $key        = base64_decode('cHJvZF9saWNlbnNl');
            $this->mode = base64_decode('cHJvZHVjdGlvbg==');
        } else {
            $key        = base64_decode('ZGV2X2xpY2Vuc2U=');
            $this->mode = base64_decode('ZGV2ZWxvcG1lbnQ=');
        }
        $this->temp = $this->configHelper->getConfigValue('magepsycho_storerestrictionpro/general/' . $key);
    }

    public function getMessage()
    {
        $message = base64_decode(
            'WW91IGFyZSB1c2luZyB1bmxpY2Vuc2VkIHZlcnNpb24gb2YgJ1N0b3JlIFJlc3RyaWN0aW9uIFBybycgZXh0ZW5zaW9uIGZvciBkb21haW46IHt7RE9NQUlOfX0uIFBsZWFzZSBlbnRlciBhIHZhbGlkIExpY2Vuc2UgS2V5IGZyb20gU3RvcmVzICZyYXF1bzsgQ29uZmlndXJhdGlvbiAmcmFxdW87IE1hZ2VQc3ljaG8gJnJhcXVvOyBTdG9yZSBSZXN0cmljdGlvbiBQcm8gJnJhcXVvOyBHZW5lcmFsIFNldHRpbmdzICZyYXF1bzsgTGljZW5zZSBLZXkuIElmIHlvdSBkb24ndCBoYXZlIG9uZSwgcGxlYXNlIHB1cmNoYXNlIGEgdmFsaWQgbGljZW5zZSBmcm9tIDxhIGhyZWY9Imh0dHA6Ly93d3cubWFnZXBzeWNoby5jb20iIHRhcmdldD0iX2JsYW5rIj53d3cubWFnZXBzeWNoby5jb208L2E+IG9yIHlvdSBjYW4gZGlyZWN0bHkgZW1haWwgdG8gPGEgaHJlZj0ibWFpbHRvOmluZm9AbWFnZXBzeWNoby5jb20iPmluZm9AbWFnZXBzeWNoby5jb208L2E+Lg=='
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
        $salt = sha1(base64_decode('bTItc3RvcmVyZXN0cmljdGlvbnBybw=='));
        if(sha1($salt . $domain . $this->mode) == $serial) {
            return true;
        }

        return false;
    }

    public function isValid()
    {
        return $this->checkEntry($this->getDomain(), $this->temp);
    }

    public function isFxnSkipped()
    {
        if (($this->configHelper->isActive() && !$this->isValid()) || !$this->configHelper->isActive()) {
            return true;
        }
        return false;
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


    public function getConfigHelper()
    {
        return $this->configHelper;
    }

    public function getExtensionVersion()
    {
        $moduleCode = 'MagePsycho_StoreRestrictionPro';
        $moduleInfo = $this->moduleList->getOne($moduleCode);
        return $moduleInfo['setup_version'];
    }

    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_WEB,
            true
        );
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

    public function isActive()
    {
        return $this->configHelper->isActive();
    }

    public function checkPageUrl($moduleName, $controllerName, $actionName)
    {
        $request        = $this->_getRequest();
        $_moduleName     = strtolower($request->getModuleName());
        $_controllerName = strtolower($request->getControllerName());
        $_actionName     = strtolower($request->getActionName());
        if (strtolower($_moduleName) == strtolower($moduleName)
            && strtolower($_controllerName) == strtolower($controllerName)
            && strtolower($_actionName) == strtolower($actionName)
        ) {
            return true;
        }

        return false;
    }

    public function isLoginPage()
    {
        $loginPage     = $this->checkPageUrl('customer', 'account', 'login');
        $loginPostPage = $this->checkPageUrl('customer', 'account', 'loginPost');
        if ($loginPage || $loginPostPage) {
            return true;
        }

        return false;
    }

    public function isLogoutPage()
    {
        $logoutPage     = $this->checkPageUrl('customer', 'account', 'logout');
        $logoutPostPage = $this->checkPageUrl('customer', 'account', 'logoutSuccess');
        if ($logoutPage || $logoutPostPage) {
            return true;
        }

        return false;
    }

    public function isForgotPasswordPage()
    {
        $forgotPage            = $this->checkPageUrl('customer', 'account', 'forgotpassword');
        $forgotPostPage        = $this->checkPageUrl('customer', 'account', 'forgotpasswordpost');
        $resetpasswordPage     = $this->checkPageUrl('customer', 'account', 'resetpassword');
        $resetpasswordPostPage = $this->checkPageUrl('customer', 'account', 'resetpasswordpost');
        $changeForgottenPage   = $this->checkPageUrl('customer', 'account', 'changeforgotten');

        if ($forgotPage
            || $forgotPostPage
            || $resetpasswordPage
            || $resetpasswordPostPage
            || $changeForgottenPage
        ) {
            return true;
        }

        return false;
    }

    public function isAccountCreatePage()
    {
        $registrationPage = $this->isAccountRegistrationPage();
        $confirmPage      = $this->checkPageUrl('customer', 'account', 'confirm');
        $confirmationPage = $this->checkPageUrl('customer', 'account', 'confirmation');
        if (   $registrationPage
            || $confirmPage
            || $confirmationPage
        ) {
            // Tweak: if new account registration is disabled
            if ($this->isAccountRegistrationDisabled()) {
                return false;
            }

            return true;
        }
        return false;
    }

    public function isAccountRegistrationPage()
    {
        $accountCreate      = $this->checkPageUrl('customer', 'account', 'create');
        $accountCreatePost  = $this->checkPageUrl('customer', 'account', 'createpost');
        if ($accountCreate || $accountCreatePost) {
            return true;
        }

        return false;
    }

    public function is404ErrorPage()
    {
        $cmsIndexNoRoute            = $this->checkPageUrl('cms', 'index', 'noRoute');
        $cmsIndexDefaultNoRoute     = $this->checkPageUrl('cms', 'index', 'defaultNoRoute');
        if ($cmsIndexNoRoute || $cmsIndexDefaultNoRoute) {
            return true;
        }

        return false;
    }

    public function isCookiePage()
    {
        if ($this->checkPageUrl('core', 'index', 'noCookies')) {
            return true;
        }

        return false;
    }

    public function isApiRequest()
    {
        return $this->_getRequest()->getControllerModule() == 'Mage_Api';
    }

    public function isAjaxRequest()
    {
        return $this->_getRequest()->isXmlHttpRequest();
    }

    public function getLoginUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }

    public function getCurrentCustomerGroupId()
    {
        $customer = $this->customerSession->getCustomer();
        return $customer->getGroupId();
    }

    public function isHomepage()
    {
        if ($this->_getRequest()->getFullActionName() == 'cms_index_index') {
            return true;
        }
        return false;
    }

    public function getHomepageIdentifier()
    {
        return $this->configHelper->getConfigValue(\Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE);
    }

    public function getRestrictedLandingPage()
    {
        $redirectionType = $this->configHelper->getRestrictedRedirectionType();
        switch ($redirectionType) {
            case \MagePsycho\StoreRestrictionPro\Model\System\Config\Source\RedirectionType::REDIRECTION_TYPE_CMS:
                //@todo check if the CMS page belongs to the store or not
                $cmsIdentifier = trim($this->configHelper->getRestrictedRedirectionTypeCms(), '/');
                if ($cmsIdentifier == $this->getHomepageIdentifier()) {
                    $cmsIdentifier = ''; //remove /home from url
                }
                $landingUrl = $this->_urlBuilder->getUrl($cmsIdentifier);
                break;
            case \MagePsycho\StoreRestrictionPro\Model\System\Config\Source\RedirectionType::REDIRECTION_TYPE_CUSTOM:
                $customPage = trim($this->configHelper->getRestrictedRedirectionTypeCustom(), '/');
                $landingUrl = $this->_urlBuilder->getUrl($customPage);
                break;
            case \MagePsycho\StoreRestrictionPro\Model\System\Config\Source\RedirectionType::REDIRECTION_TYPE_LOGIN:
            default:
                $landingUrl = $this->getLoginUrl();
                break;
        }
        return $landingUrl;
    }


    /****************************************************************************************
     * REGISTRATION
     *****************************************************************************************/

    public function skipRestrictionByDefault()
    {
        /*$isCustomerNonLoggedInIndexPage = (
                !$this->customerSession->isLoggedIn()
                && $this->checkPageUrl('customer', 'account', 'index')
            )
            ? true
            : false; //@tweak for forgotpassword;*/

        if (
            $this->isFxnSkipped()
            //|| $isCustomerNonLoggedInIndexPage
            || $this->isLoginPage()
            || $this->isLogoutPage()
            || $this->isForgotPasswordPage()
            || $this->isAccountCreatePage()
            || $this->is404ErrorPage()
            || $this->isCookiePage()
            || $this->isApiRequest()
            || $this->isAjaxRequest()
        ) {
            return true;
        }
        return false;
    }

    public function isAccountRegistrationDisabled()
    {
        $registrationType = $this->configHelper->getNewAccountRegistrationOption();
        if ($registrationType == \MagePsycho\StoreRestrictionPro\Model\System\Config\Source\RegistrationOption::NEW_ACCOUNT_REGISTRATION_DISABLED) {
            return true;
        }

        return false;
    }

    public function shouldShowRegistrationDisabledMessage()
    {
        return $this->isAccountRegistrationDisabled() && $this->configHelper->getNewAcccountRegistrationShowDisabledMessage();
    }

    public function isCustomerGroupAllowedForRestrictedStore()
    {
        $currentCustomerGroupId = $this->getCurrentCustomerGroupId();
        $allowedCustomerGroups  = $this->configHelper->getRestrictedAllowedCustomerGroups();
        $this->log('$currentCustomerGroupId::' . $currentCustomerGroupId . ', $allowedCustomerGroups::' . implode(',', $allowedCustomerGroups));
        if (!empty($currentCustomerGroupId)
            && in_array($currentCustomerGroupId, $allowedCustomerGroups)
        ) {
            return true;
        }

        return false;
    }

    public function isRestrictedCmsPageAccessible()
    {
        $canAccess = false;
        $currentIdentifier = '';
        if ($this->isHomepage()) {
            $currentIdentifier = $this->getHomepageIdentifier();
            $this->log('Homepage::$currentIdentifier::' . $currentIdentifier);
        } else {
            $request = $this->_getRequest();
            $pageId  = $request->getParam(
                'page_id',
                $request->getParam('id', false)
            );
            if ($pageId) {
                $page = $this->pageFactory->create()->load($pageId);
                $currentIdentifier = $page->getIdentifier();
            }
            $this->log('Other CMS::$currentIdentifier::' . $currentIdentifier);
        }

        $allowedCmsPages   = $this->configHelper->getRestrictedAllowedCmsPages();
        if ($this->configHelper->getRestrictedRedirectionType() == \MagePsycho\StoreRestrictionPro\Model\System\Config\Source\RedirectionType::REDIRECTION_TYPE_CMS) {
            $cmsLandingPage    = $this->configHelper->getRestrictedRedirectionTypeCms();
            $allowedCmsPages = array_merge($allowedCmsPages, [$cmsLandingPage]);
        }
        $this->log('$allowedCmsPages::' . implode(', ', $allowedCmsPages));

        if (!empty($currentIdentifier)
            && in_array($currentIdentifier, $allowedCmsPages)
        ) {
            $canAccess = true;
        }
        return $canAccess;
    }

    public function isRestrictedCategoryPageAccessible()
    {
        $canAccess = false;
        $currentCategoryId = $this->_getRequest()->getParam('id');
        $this->log('$currentCategoryId::' . $currentCategoryId);
        $allowedCategories = $this->configHelper->getRestrictedAllowedCategoryPages();
        $this->log('$allowedCategories::' . implode(', ', $allowedCategories));
        if (in_array($currentCategoryId, $allowedCategories)) {
            $canAccess = true;
        }
        return $canAccess;
    }

    public function isRestrictedProductPageAccessible()
    {
        $canAccess          = false;
        $currentProductId   = $this->_getRequest()->getParam('id');
        $currentProductSku  = $this->productFactory->create()->load($currentProductId)->getSku();
        $this->log('$currentProductSku::' . $currentProductSku);
        $allowedProducts    = $this->configHelper->getRestrictedAllowedProductPages();
        $this->log('$allowedProducts::' . implode(',', $allowedProducts));
        if (in_array($currentProductSku, $allowedProducts)) {
            $canAccess = true;
        }
        return $canAccess;
    }

    public function isRestrictedModulePageAccessible()
    {
        $canAccess             = false;
        $request               = $this->_getRequest();
        $currentModuleName     = $request->getModuleName();
        $currentControllerName = $request->getControllerName();
        $currentActionName     = $request->getActionName();

        $this->log(
            '$currentModuleName::' . $currentModuleName .
            ', $currentControllerName::' . $currentControllerName .
            ', $currentActionName::' . $currentActionName
        );

        $allowedModules = $this->configHelper->getRestrictedAllowedModulePages();
        $this->log('$allowedModules::' . implode(', ', $allowedModules));
        foreach ($allowedModules as $_module) {
            $_module            = preg_replace('/\s+/', '', $_module);
            $_moduleArray      = explode('/', trim($_module, '/'));
            $_dbModuleName     = isset($_moduleArray[0]) ? $_moduleArray[0] : '';
            $_dbControllerName = isset($_moduleArray[1]) ? $_moduleArray[1] : 'index';
            $_dbActionName     = isset($_moduleArray[2]) ? $_moduleArray[2] : 'index';
            $this->log(
                '$_dbModuleName::' . $_dbModuleName .
                ', $_dbControllerName::' . $_dbControllerName .
                ', $_dbActionName::' . $_dbActionName
            );
            if ($_dbModuleName == $currentModuleName
                && ($_dbControllerName == $currentControllerName || $_dbControllerName == '*')
                && ($_dbActionName == $currentActionName || $_dbActionName == '*')
            ) {
                $canAccess = true;
                break;
            }
        }

        return $canAccess;
    }

}