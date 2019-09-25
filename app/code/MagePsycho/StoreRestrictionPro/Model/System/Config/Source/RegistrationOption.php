<?php

namespace MagePsycho\StoreRestrictionPro\Model\System\Config\Source;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RegistrationOption implements  \Magento\Framework\Option\ArrayInterface
{
    const NEW_ACCOUNT_REGISTRATION_ENABLED  = 1;
    const NEW_ACCOUNT_REGISTRATION_DISABLED = 0;

    protected $_options;

    public function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                [
                    'value' => self::NEW_ACCOUNT_REGISTRATION_ENABLED,
                    'label' => __('Enabled'),
                ],

                [
                    'value' => self::NEW_ACCOUNT_REGISTRATION_DISABLED,
                    'label' => __('Disabled'),
                ],
            );

        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, ['value' => '', 'label' => '']);
        }
        return $options;
    }

    public function getOptionsArray($withEmpty = true)
    {
        $options = array();
        foreach ($this->getAllOptions($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);
        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    public function toOptionHash($withEmpty = true)
    {
        return $this->getOptionsArray($withEmpty);
    }
}