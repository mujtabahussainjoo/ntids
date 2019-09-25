<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use MageWorx\ShippingRules\Api\Data\MethodInterface;

class EstimatedDeliveryTimeDisplayType implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => MethodInterface::EDT_DISPLAY_TYPE_DAYS, 'label' => __('Days only (rounded)')],
            ['value' => MethodInterface::EDT_DISPLAY_TYPE_HOURS, 'label' => __('Hours only')],
            ['value' => MethodInterface::EDT_DISPLAY_TYPE_DAYS_AND_HOURS, 'label' => __('Days with Hours')],
        ];
    }
}
