<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\Config\Source;

use MageWorx\ShippingRules\Model\Carrier\Method\Rate as Rate;
use Magento\Framework\Data\OptionSourceInterface;

class MultipleRatesPrice implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Rate::MULTIPLE_RATES_PRICE_CALCULATION_MAX_PRIORITY,
                'label' => __('Use Rate with Max Priority')
            ],
            [
                'value' => Rate::MULTIPLE_RATES_PRICE_CALCULATION_MAX_PRICE,
                'label' => __('Use Rate with Max Price')
            ],
            [
                'value' => Rate::MULTIPLE_RATES_PRICE_CALCULATION_MIN_PRICE,
                'label' => __('Use Rate with Min Price')
            ],
            [
                'value' => Rate::MULTIPLE_RATES_PRICE_CALCULATION_SUM_UP,
                'label' => __('Sum Up All Rates')
            ]
        ];
    }
}
