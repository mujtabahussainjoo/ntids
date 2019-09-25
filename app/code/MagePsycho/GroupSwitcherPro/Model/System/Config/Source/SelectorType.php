<?php

namespace MagePsycho\GroupSwitcherPro\Model\System\Config\Source;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SelectorType implements  \Magento\Framework\Option\ArrayInterface
{
    const SELECTOR_TYPE_DROPDOWN    = 1;
    const SELECTOR_TYPE_GROUP_CODE  = 2;

    protected $_options;

    public function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            $this->_options = [
                [
                    'value' => self::SELECTOR_TYPE_DROPDOWN,
                    'label' => __('Dropdown'),
                ],
                [
                    'value' => self::SELECTOR_TYPE_GROUP_CODE,
                    'label' => __('Group Code'),
                ],
            ];
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