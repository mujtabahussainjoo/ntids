<?php

namespace MagePsycho\GroupSwitcherPro\Helper;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Config
{
    /* General */
    const XML_PATH_ENABLED                      = 'magepsycho_groupswitcherpro/general/enabled';
    const XML_PATH_DEBUG                        = 'magepsycho_groupswitcherpro/general/debug';
    const XML_PATH_DOMAIN_TYPE                  = 'magepsycho_groupswitcherpro/general/domain_type';

    const XML_PATH_ALLOWED_CUSTOMER_GROUPS      = 'magepsycho_groupswitcherpro/group/allowed_customer_groups';
    const XML_PATH_GROUP_IS_REQUIRED            = 'magepsycho_groupswitcherpro/group/group_is_required';
    const XML_PATH_GROUP_SELECTION_EDITABLE     = 'magepsycho_groupswitcherpro/group/group_selection_editable';
    const XML_PATH_GROUP_AVAILABLE_CHECKOUT     = 'magepsycho_groupswitcherpro/group/group_selection_checkout';
    const XML_PATH_GROUP_SELECTION_LABEL        = 'magepsycho_groupswitcherpro/group/group_selection_label';

    const XML_PATH_GROUP_SELECTION_TYPE         = 'magepsycho_groupswitcherpro/group/customer_group_selection_type';

    const XML_PATH_GROUP_CODE_DATA              = 'magepsycho_groupswitcherpro/group/group_codes';
    const XML_PATH_GROUP_CODE_ERROR_MESSAGE     = 'magepsycho_groupswitcherpro/group/group_code_error_message';

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
     * GROUP SETTINGS
     *****************************************************************************************/
    public function getGroupSelectionType($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_SELECTION_TYPE, $storeId);
    }

    public function getAllowedCustomerGroups($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ALLOWED_CUSTOMER_GROUPS, $storeId);
    }

    public function isGroupFieldRequired($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_IS_REQUIRED, $storeId);
    }

    public function isGroupSelectionEditable($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_SELECTION_EDITABLE, $storeId);
    }

    public function isEnabledForCheckout($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_AVAILABLE_CHECKOUT, $storeId);
    }

    public function getGroupSelectionLabel($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_SELECTION_LABEL, $storeId);
    }

    public function getGroupCodeData($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_CODE_DATA, $storeId);
    }

    public function getGroupCodeErrorMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_CODE_ERROR_MESSAGE, $storeId);
    }
}