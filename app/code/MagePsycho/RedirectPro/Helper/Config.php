<?php

namespace MagePsycho\RedirectPro\Helper;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Config
{
    /* General */
    const XML_PATH_ENABLED                      = 'magepsycho_redirectpro/general/enabled';
    const XML_PATH_DEBUG                        = 'magepsycho_redirectpro/general/debug';
    const XML_PATH_DOMAIN_TYPE                  = 'magepsycho_redirectpro/general/domain_type';

    /* Login */
    const XML_PATH_DEFAULT_LOGIN_URL            = 'magepsycho_redirectpro/login_settings/default_login_url';
    const XML_PATH_GROUP_LOGIN_URL              = 'magepsycho_redirectpro/login_settings/group_login_url';

    /* Logout */
    const XML_PATH_DEFAULT_LOGOUT_URL           = 'magepsycho_redirectpro/logout_settings/default_logout_url';
    const XML_PATH_GROUP_LOGOUT_URL             = 'magepsycho_redirectpro/logout_settings/group_logout_url';
    const XML_PATH_LOGOUT_REMOVE_INTER          = 'magepsycho_redirectpro/logout_settings/remove_logout_intermediate';
    const XML_PATH_LOGOUT_MESSAGE               = 'magepsycho_redirectpro/logout_settings/logout_custom_message';
    const XML_PATH_LOGOUT_DELAY                 = 'magepsycho_redirectpro/logout_settings/logout_delay_time';

    /* New Account */
    const XML_PATH_DEFAULT_ACCOUNT_URL          = 'magepsycho_redirectpro/account_settings/default_account_url';
    const XML_PATH_GROUP_ACCOUNT_URL            = 'magepsycho_redirectpro/account_settings/group_account_url';
    const XML_PATH_GROUP_ACCOUNT_TEMPLATE       = 'magepsycho_redirectpro/account_settings/group_account_template';
    const XML_PATH_DEFAULT_ACCOUNT_MESSAGE      = 'magepsycho_redirectpro/account_settings/default_account_message';
    const XML_PATH_GROUP_ACCOUNT_MESSAGE        = 'magepsycho_redirectpro/account_settings/group_account_message';

    /* Misc */
    const XML_PATH_NEWSLETTER_URL               = 'magepsycho_redirectpro/misc_settings/newsletter_url';
    const XML_PATH_REDIRECT_TO_PARAM            = 'magepsycho_redirectpro/misc_settings/redirect_to_param';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfigValue($xmlPath, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $xmlPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /****************************************************************************************
     * GENERIC
     *****************************************************************************************/
    public function isEnabled($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ENABLED, $storeId);
    }

    public function isActive($storeId = null)
    {
        return $this->isEnabled($storeId);
    }

    public function getDebugStatus($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DEBUG, $storeId);
    }

    /****************************************************************************************
     * LOGIN
     *****************************************************************************************/
    public function getDefaultLoginUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_LOGIN_URL, $storeId);
    }

    public function getGroupLoginUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_LOGIN_URL, $storeId);
    }


    /****************************************************************************************
     * LOGOUT
     *****************************************************************************************/
    public function getDefaultLogoutUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_LOGOUT_URL, $storeId);
    }

    public function getGroupLogoutUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_LOGOUT_URL, $storeId);
    }

    public function getRemoveLogoutIntermediate($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_LOGOUT_REMOVE_INTER, $storeId);
    }

    public function getLogoutMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_LOGOUT_MESSAGE, $storeId);
    }

    public function getLogoutDelay($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_LOGOUT_DELAY, $storeId);
    }

    /****************************************************************************************
     * NEW ACCOUNT
     *****************************************************************************************/
    public function getDefaultAccountUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_ACCOUNT_URL, $storeId);
    }

    public function getGroupAccountUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_ACCOUNT_URL, $storeId);
    }

    public function getGroupAccountTemplate($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_ACCOUNT_TEMPLATE, $storeId);
    }

    public function getDefaultAccountMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_ACCOUNT_MESSAGE, $storeId);
    }

    public function getGroupAccountMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_ACCOUNT_MESSAGE, $storeId);
    }

    /****************************************************************************************
     * MISC
     *****************************************************************************************/
    public function getNewsletterUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEWSLETTER_URL, $storeId);
    }

    public function getRedirectToParam($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_REDIRECT_TO_PARAM, $storeId);
    }

}