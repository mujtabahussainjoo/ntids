<?php

namespace Potato\CheckoutNewsletter\Model\Source\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DisplayCheckbox
 */
class DisplayCheckbox implements OptionSourceInterface
{
    const YES_VALUE   = 1;
    const NO_VALUE  = 0;

    /**
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::YES_VALUE => __("Yes"),
            self::NO_VALUE => __("No, subscribe customers automatically")
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->getOptionArray();
        $result = [];
        foreach ($options as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }
        return $result;
    }
}
