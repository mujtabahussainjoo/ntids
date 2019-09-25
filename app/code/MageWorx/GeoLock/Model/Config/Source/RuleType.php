<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoLock\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class RuleType implements ArrayInterface
{
    const ALLOW = 1;
    const DENY = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ALLOW, 'label' => __('Allow')],
            ['value' => self::DENY, 'label' => __('Deny')],
        ];
    }
}
