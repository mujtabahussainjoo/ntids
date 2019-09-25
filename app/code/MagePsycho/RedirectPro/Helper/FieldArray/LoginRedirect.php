<?php

namespace MagePsycho\RedirectPro\Helper\FieldArray;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Store\Model\Store;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LoginRedirect
{
    const FIELD_ARRAY_CUSTOMER_GROUP_ID     = 'customer_group_id';
    const FIELD_ARRAY_CUSTOMER_GROUP_VALUE  = 'group_login_url';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param GroupManagementInterface $groupManagement
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Math\Random $mathRandom,
        GroupManagementInterface $groupManagement
    ) {
        $this->scopeConfig      = $scopeConfig;
        $this->mathRandom       = $mathRandom;
        $this->groupManagement  = $groupManagement;
    }

    /**
     * Retrieve formatted value
     *
     * @param int|float|string|null $value
     * @return string|null
     */
    protected function fixValue($value)
    {
        return !empty($value) ? (string) $value : null;
    }

    /**
     * Generate a storable representation of a value
     *
     * @param int|float|string|array $value
     * @return string
     */
    protected function serializeValue($value)
    {
        if (is_numeric($value)) {
            $data = (float) $value;
            return (string) $data;
        } elseif (is_array($value)) {
            $data = [];
            foreach ($value as $groupId => $groupValue) {
                if (!array_key_exists($groupId, $data)) {
                    $data[$groupId] = $this->fixValue($groupValue);
                }
            }
            if (count($data) == 1 && array_key_exists($this->getAllCustomersGroupId(), $data)) {
                return (string) $data[$this->getAllCustomersGroupId()];
            }
            return serialize($data);
        } else {
            return '';
        }
    }

    /**
     * Create a value from a storable representation
     *
     * @param int|float|string $value
     * @return array
     */
    protected function unserializeValue($value)
    {
        if (is_numeric($value)) {
            return [$this->getAllCustomersGroupId() => $this->fixValue($value)];
        } elseif (is_string($value) && !empty($value)) {
            return unserialize($value);
        } else {
            return [];
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param string|array $value
     * @return bool
     */
    protected function isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists(self::FIELD_ARRAY_CUSTOMER_GROUP_ID, $row)
                || !array_key_exists(self::FIELD_ARRAY_CUSTOMER_GROUP_VALUE, $row)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $groupId => $groupValue) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = [
                self::FIELD_ARRAY_CUSTOMER_GROUP_ID => $groupId,
                self::FIELD_ARRAY_CUSTOMER_GROUP_VALUE => $this->fixValue($groupValue)
            ];
        }
        return $result;
    }

    /**
     * Decode value from used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function decodeArrayFieldValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists(self::FIELD_ARRAY_CUSTOMER_GROUP_ID, $row)
                || !array_key_exists(self::FIELD_ARRAY_CUSTOMER_GROUP_VALUE, $row)
            ) {
                continue;
            }
            $groupId          = $row[self::FIELD_ARRAY_CUSTOMER_GROUP_ID];
            $groupValue       = $this->fixValue($row[self::FIELD_ARRAY_CUSTOMER_GROUP_VALUE]);
            $result[$groupId] = $groupValue;
        }
        return $result;
    }

    /**
     * Retrieve field array value from config
     *
     * @param int $customerGroupId
     * @param null|string|bool|int|Store $store
     * @return string|null
     */
    public function getConfigValue($customerGroupId, $store = null)
    {
        $value = $this->scopeConfig->getValue(
            \MagePsycho\RedirectPro\Helper\Config::XML_PATH_GROUP_LOGIN_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $value = $this->unserializeValue($value);
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }
        $result = null;
        foreach ($value as $groupId => $groupValue) {
            if ($groupId == $customerGroupId) {
                $result = $groupValue;
                break;
            } elseif ($groupId == $this->getAllCustomersGroupId()) {
                $result = $groupValue;
            }
        }
        return $this->fixValue($result);
    }

    /**
     * Make value readable by \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param string|array $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->unserializeValue($value);
        if (!$this->isEncodedArrayFieldValue($value)) {
            $value = $this->encodeArrayFieldValue($value);
        }
        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param string|array $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }
        $value = $this->serializeValue($value);
        return $value;
    }

    /**
     * Return the all customer group id
     *
     * @return int
     */
    protected function getAllCustomersGroupId()
    {
        return $this->groupManagement->getAllCustomersGroup()->getId();
    }
}