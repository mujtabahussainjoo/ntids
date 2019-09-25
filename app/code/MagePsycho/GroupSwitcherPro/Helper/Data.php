<?php

namespace MagePsycho\GroupSwitcherPro\Helper;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $mode, $temp;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $customLogger;

    /**
     * @var \MagePsycho\GroupSwitcherPro\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \MagePsycho\GroupSwitcherPro\Model\System\Config\Source\CustomerGroup
     */
    private $customerGroupSource;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var Url
     */
    private $urlHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \MagePsycho\GroupSwitcherPro\Logger\Logger $customLogger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepositoryInterface,
        \Magento\Customer\Model\Session $customerSession,
        \MagePsycho\GroupSwitcherPro\Helper\Config $configHelper,
        \MagePsycho\GroupSwitcherPro\Model\System\Config\Source\CustomerGroup $customerGroupSource,
        \Magento\Framework\App\State $appState,
        Url $urlHelper
    ) {
        $this->moduleList                  = $moduleList;
        $this->productMetadata             = $productMetadata;
        $this->customLogger                = $customLogger;
        $this->configHelper                = $configHelper;
        $this->customerGroupSource         = $customerGroupSource;
        $this->customerSession             = $customerSession;
        $this->customerRepository          = $customerRepositoryInterface;
        $this->groupRepository             = $groupRepositoryInterface;
        $this->appState                    = $appState;
        $this->urlHelper                   = $urlHelper;

        parent::__construct($context);
        $this->_initialize();
    }

    protected function _initialize()
    {
        $field = base64_decode('ZG9tYWluX3R5cGU=');
        if ($this->configHelper->getConfigValue('magepsycho_groupswitcherpro/general/' . $field) == 1) {
            $key        = base64_decode('cHJvZF9saWNlbnNl');
            $this->mode = base64_decode('cHJvZHVjdGlvbg==');
        } else {
            $key        = base64_decode('ZGV2X2xpY2Vuc2U=');
            $this->mode = base64_decode('ZGV2ZWxvcG1lbnQ=');
        }
        $this->temp = $this->configHelper->getConfigValue('magepsycho_groupswitcherpro/general/' . $key);
    }

    public function getMessage()
    {
        $message = base64_decode(
            'WW91IGFyZSB1c2luZyB1bmxpY2Vuc2VkIHZlcnNpb24gb2YgJ0dyb3VwIFNlbGVjdG9yIFBybycgZXh0ZW5zaW9uIGZvciBkb21haW46IHt7RE9NQUlOfX0uIFBsZWFzZSBlbnRlciBhIHZhbGlkIExpY2Vuc2UgS2V5IGZyb20gU3RvcmVzICZyYXF1bzsgQ29uZmlndXJhdGlvbiAmcmFxdW87IE1hZ2VQc3ljaG8gJnJhcXVvOyBHcm91cCBTZWxlY3RvciBQcm8gJnJhcXVvOyBHZW5lcmFsIFNldHRpbmdzICZyYXF1bzsgTGljZW5zZSBLZXkuIElmIHlvdSBkb24ndCBoYXZlIG9uZSwgcGxlYXNlIHB1cmNoYXNlIGEgdmFsaWQgbGljZW5zZSBmcm9tIDxhIGhyZWY9Imh0dHA6Ly93d3cubWFnZXBzeWNoby5jb20iIHRhcmdldD0iX2JsYW5rIj53d3cubWFnZXBzeWNoby5jb208L2E+IG9yIHlvdSBjYW4gZGlyZWN0bHkgZW1haWwgdG8gPGEgaHJlZj0ibWFpbHRvOmluZm9AbWFnZXBzeWNoby5jb20iPmluZm9AbWFnZXBzeWNoby5jb208L2E+Lg=='
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
        $salt = sha1(base64_decode('bTItZ3JvdXBzZWxlY3Rvcg=='));
        if(sha1($salt . $domain . $this->mode) == $serial) {
            return true;
        }

        return false;
    }

    public function isValid()
    {
        if ($this->hasBundleExtensions()) {
            return true;
        }

        return $this->checkEntry($this->getDomain(), $this->temp);
    }

    public function isActive()
    {
        return $this->configHelper->isActive();
    }

    public function isFxnSkipped()
    {
        if (   ($this->configHelper->isActive() && !$this->isValid())
            || !$this->configHelper->isActive()
        ) {
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

    /**
     * Checks if the extension is bundled with others
     *
     * @return bool
     */
    public function hasBundleExtensions()
    {
        if ($this->moduleList->has('MagePsycho_RedirectPro')
            || $this->moduleList->has('MagePsycho_StoreRestrictionPro')
        ) {
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
        $moduleCode = 'MagePsycho_GroupSwitcherPro';
        $moduleInfo = $this->moduleList->getOne($moduleCode);
        return $moduleInfo['setup_version'];
    }

    public function getMageVersion()
    {
        return $this->productMetadata->getVersion();
    }

    public function getMageEdition()
    {
        return $this->productMetadata->getEdition();
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
                $this->customLogger->mpLog(str_repeat('=', 100));
            }

            $this->customLogger->mpLog($message);
        }
    }

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

    public function getCodeByGroup($groupId)
    {
        $groupToCodeData = $this->configHelper->getGroupCodeData();
        $groupToCodes    = $this->_prepareGroupWiseData($groupToCodeData);
        $groupCode       = isset( $groupToCodes[$groupId] ) ? $groupToCodes[$groupId] : '';
        return $groupCode;
    }

    public function getGroupCodes()
    {
        $groupToCodeData = $this->configHelper->getGroupCodeData();
        return $this->_prepareGroupWiseData($groupToCodeData);
    }

    /**
     * Checks & returns Group Id from Valid Code
     *
     * @param $groupCode
     *
     * @return mixed
     */
    public function checkIfGroupCodeIsValid($groupCode)
    {
        $groupCodesArray    = $this->getGroupCodes();
        $matchedGroupId     = false;
        foreach ($groupCodesArray as $groupId => $groupCodes) {
            $matchGroupCodes = $groupCodes;
            if (!is_array($groupCodes)) {
                $matchGroupCodes = array($groupCodes);
            }

            if (!empty($groupCode)
                && in_array($groupCode, $matchGroupCodes)
            ) {
                $matchedGroupId = $groupId;
                break;
            }
        }
        return $matchedGroupId;
    }

    /**
     * Check if request is valid for group code
     *
     * @return bool
     */
    public function skipGroupCodeSelectorFxn()
    {
        return    $this->isFxnSkipped()
               || $this->getConfigHelper()->getGroupSelectionType() != \MagePsycho\GroupSwitcherPro\Model\System\Config\Source\SelectorType::SELECTOR_TYPE_GROUP_CODE
               || $this->isAdminArea()
               || $this->isApiRequest();
    }

    public function isApiRequest()
    {
        return $this->_getRequest()->getControllerModule() == 'Mage_Api';
    }

    public function isAdminArea()
    {
        return $this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
    }

    /**
     * Prepare group code for table array field of system configuration
     *
     * @return array|mixed
     */
    public function getGroupSelectOptions()
    {
        $dbCustomerGroups = $this->configHelper->getAllowedCustomerGroups();
        $dbCustomerGroups = explode(',', $dbCustomerGroups);
        $customerGroups   = $this->customerGroupSource->getCustomerGroups();
        $groupOptions     = [];
        foreach ($dbCustomerGroups as $dbGroupId) {
            $groupOptions[] = [
                'label' => isset($customerGroups[$dbGroupId])
                            ? $customerGroups[$dbGroupId]
                            : '',
                'value' => $dbGroupId
            ];
        }

        array_unshift($groupOptions, ['label' => '', 'value' => '']);
        return $groupOptions;
    }

    public function isValidCustomerForEdit()
    {
        $customer = $this->customerSession->getCustomer();
        if (!$customer) {
            return false;
        }

        $groupSelectorType          = $this->getConfigHelper()->getGroupSelectionType();
        $groupId = $customer->getGroupId();
        if ($groupSelectorType == \MagePsycho\GroupSwitcherPro\Model\System\Config\Source\SelectorType::SELECTOR_TYPE_GROUP_CODE) {
            $groupCodes             = $this->getGroupCodes();
            $allowedCustomerGroup   = is_array($groupCodes) ? array_keys($groupCodes) : [];
            if ( ! empty($allowedCustomerGroup)
                 && in_array($groupId, $allowedCustomerGroup)
            ) {
                return true;
            }
        } elseif ($groupSelectorType == \MagePsycho\GroupSwitcherPro\Model\System\Config\Source\SelectorType::SELECTOR_TYPE_DROPDOWN) {
            $allowedCustomerGroup = $this->getConfigHelper()->getAllowedCustomerGroups();
            $dbGroups             = explode(',', $allowedCustomerGroup);
            if (in_array($groupId, $dbGroups)) {
                return true;
            }
        }
        return false;
    }

    public function loadCustomer($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    public function getGroupCodeById($groupId)
    {
        return $this->groupRepository->getById($groupId)
            ? $this->groupRepository->getById($groupId)->getCode()
            : '';
    }
}