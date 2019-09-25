<?php

namespace MagePsycho\StoreRestrictionPro\Helper;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Config
{
    /* Generic */
    const XML_PATH_ENABLED          = 'magepsycho_storerestrictionpro/general/enabled';
    const XML_PATH_DEBUG            = 'magepsycho_storerestrictionpro/general/debug';

    /* Registration/Activation */
    const XML_PATH_NEW_ACCOUNT_REGISTRATION_OPTION = 'magepsycho_storerestrictionpro/new_account_settings/new_account_registration_option';
    const XML_PATH_NEW_ACCOUNT_REGISTRATION_SHOW_DISABLED_MESSAGE = 'magepsycho_storerestrictionpro/new_account_settings/new_account_registration_enable_disabled_message';
    const XML_PATH_NEW_ACCOUNT_REGISTRATION_DISABLED_MESSAGE = 'magepsycho_storerestrictionpro/new_account_settings/new_account_registration_disabled_message';
    const XML_PATH_NEW_ACCOUNT_ACTIVATION_REQUIRED = 'magepsycho_storerestrictionpro/new_account_settings/new_account_activation_required';
    const XML_PATH_ACTIVATION_REQUIRED_CUSTOMER_GROUPS = 'magepsycho_storerestrictionpro/new_account_settings/activation_required_customer_groups';
    const XML_PATH_NEW_ACCOUNT_ACTIVATION_BY_DEFAULT_FRONTEND = 'magepsycho_storerestrictionpro/new_account_settings/new_account_activation_by_default_frontend';
    const XML_PATH_NEW_ACCOUNT_ACTIVATION_BY_DEFAULT_ADMIN = 'magepsycho_storerestrictionpro/new_account_settings/new_account_activation_by_default_admin';

    /* Restricted / Accessible */
    const XML_PATH_RESTRICTION_TYPE = 'magepsycho_storerestrictionpro/restricted_settings/restriction_type';
    const XML_PATH_RESTRICTED_ALLOWED_CUSTOMER_GROUPS = 'magepsycho_storerestrictionpro/restricted_settings/restricted_accessible/allowed_customer_groups';
    const XML_PATH_RESTRICTED_REDIRECTION_TYPE = 'magepsycho_storerestrictionpro/restricted_settings/restricted_accessible/redirection_type';
    const XML_PATH_RESTRICTED_REDIRECTION_TYPE_CMS = 'magepsycho_storerestrictionpro/restricted_settings/restricted_accessible/redirection_type_cms';
    const XML_PATH_RESTRICTED_REDIRECTION_TYPE_CUSTOM = 'magepsycho_storerestrictionpro/restricted_settings/restricted_accessible/redirection_type_custom';
    const XML_PATH_RESTRICTED_STORE_ERROR_MESSAGE = 'magepsycho_storerestrictionpro/restricted_settings/restricted_accessible/store_error_message';
    const XML_PATH_RESTRICTED_CUSTOMER_GROUP_ERROR_MESSAGE = 'magepsycho_storerestrictionpro/restricted_settings/restricted_accessible/customer_group_error_message';
    const XML_PATH_RESTRICTED_ALLOWED_CMS_PAGES = 'magepsycho_storerestrictionpro/restricted_settings/restricted_accessible/allowed_cms_pages';
    const XML_PATH_RESTRICTED_ALLOWED_CATEGORY_PAGES = 'magepsycho_storerestrictionpro/restricted_settings/restricted_accessible/allowed_category_pages';
    const XML_PATH_RESTRICTED_ALLOWED_PRODUCT_PAGES = 'magepsycho_storerestrictionpro/restricted_settings/restricted_accessible/allowed_product_pages';
    const XML_PATH_RESTRICTED_ALLOWED_MODULE_PAGES = 'magepsycho_storerestrictionpro/restricted_settings/restricted_accessible/allowed_module_pages';

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
     * REGISTRATION / ACTIVATION
     *****************************************************************************************/
    public function getNewAccountRegistrationOption($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCOUNT_REGISTRATION_OPTION, $storeId);
    }

    public function getNewAcccountRegistrationShowDisabledMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCOUNT_REGISTRATION_SHOW_DISABLED_MESSAGE, $storeId);
    }

    public function getNewAcccountRegistrationDisabledMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCOUNT_REGISTRATION_DISABLED_MESSAGE, $storeId);
    }

    /****************************************************************************************
     * RESTRICTED / ACCESSIBLE
     *****************************************************************************************/
    public function getRestrictionType($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTION_TYPE, $storeId);
    }

    public function getRestrictedAllowedCustomerGroups($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_RESTRICTED_ALLOWED_CUSTOMER_GROUPS, $storeId);
        return explode(',', $value);
    }

    public function getRestrictedRedirectionType($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTED_REDIRECTION_TYPE, $storeId);
    }

    public function getRestrictedRedirectionTypeCms($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTED_REDIRECTION_TYPE_CMS, $storeId);
    }

    public function getRestrictedRedirectionTypeCustom($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTED_REDIRECTION_TYPE_CUSTOM, $storeId);
    }

    public function getRestrictedStoreErrorMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTED_STORE_ERROR_MESSAGE, $storeId);
    }

    public function getRestrictedCustomerGroupErrorMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTED_CUSTOMER_GROUP_ERROR_MESSAGE, $storeId);
    }

    public function getRestrictedAllowedCmsPages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_RESTRICTED_ALLOWED_CMS_PAGES, $storeId);
        $value = preg_replace('/\s+/', '', $value);
        return explode(',', $value);
    }

    public function getRestrictedAllowedCategoryPages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_RESTRICTED_ALLOWED_CATEGORY_PAGES, $storeId);
        $value = preg_replace('/\s+/', '', $value);
        return explode(',', $value);
    }

    public function getRestrictedAllowedProductPages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_RESTRICTED_ALLOWED_PRODUCT_PAGES, $storeId);
        $value = preg_replace('/\s+/', '', $value);
        return explode(',', $value);
    }

    public function getRestrictedAllowedModulePages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_RESTRICTED_ALLOWED_MODULE_PAGES, $storeId);
        return explode("\n", $value);
    }

}