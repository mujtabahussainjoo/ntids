<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\Config\Source;

use MageWorx\ShippingRules\Model\Carrier\Method\Rate as Rate;
use Magento\Framework\Data\OptionSourceInterface;

class MethodRatePrice implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Rate::PRICE_CALCULATION_OVERWRITE, 'label' => __('Overwrite')],
            ['value' => Rate::PRICE_CALCULATION_SUM, 'label' => __('Sum')],
        ];
    }
}
