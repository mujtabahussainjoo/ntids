<?php

namespace MagePsycho\RedirectPro\Model\System\Config\Source;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LogoutDelayTime implements \Magento\Framework\Option\ArrayInterface
{
    protected $_options;

    public function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            $options = [];
            for ($i = 1; $i <= 5; $i++) {
                $second = ($i == 1) ? __('sec') : __('secs');
                $options[$i] = $i . ' ' . $second;
            }
            $this->_options = $options;
        }

        $options = $this->_options;

        if ($withEmpty) {
            array_unshift($options, array('value' => '', 'label' => ''));
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