<?php

namespace MagePsycho\StoreRestrictionPro\Model\System\Config\Source;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RedirectionType implements  \Magento\Framework\Option\ArrayInterface
{
    const REDIRECTION_TYPE_LOGIN    = 1;
    const REDIRECTION_TYPE_CMS      = 2;
    const REDIRECTION_TYPE_CUSTOM   = 3;

    protected $_options;

    public function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                [
                    'value' => self::REDIRECTION_TYPE_LOGIN,
                    'label' => __('Login Page'),
                ],
                [
                    'value' => self::REDIRECTION_TYPE_CMS,
                    'label' => __('CMS Page'),
                ],
                [
                    'value' => self::REDIRECTION_TYPE_CUSTOM,
                    'label' => __('Custom Page'),
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