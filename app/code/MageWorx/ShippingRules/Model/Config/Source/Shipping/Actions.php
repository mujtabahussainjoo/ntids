<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Config\Source\Shipping;

use Magento\Framework\Option\ArrayInterface;
use MageWorx\ShippingRules\Model\Rule;

class Actions implements ArrayInterface
{

    /**
     * Return array of available actions.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $actions = $this->toArray();
        $result = [['value' => '', 'label' => '']];

        foreach ($actions as $actionTypeCode => $actionTypes) {
            $result[$actionTypeCode] = ['label' => $actionTypeCode];
            foreach ($actionTypes as $actionCode => $actionLabel) {
                $result[$actionTypeCode]['value'][] = [
                    'label' => __($actionLabel),
                    'value' => $actionCode
                ];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $matrix = Rule::getCalculationMatrix();
        $actions = [
            'Fixed' => [],
            'Percent' => []
        ];

        foreach ($matrix as $key => $calcMethod) {
            if (mb_stripos($calcMethod, Rule::ACTION_CALCULATION_FIXED) !== false) {
                $actions['Fixed'][$key] = $calcMethod;
            } else {
                $actions['Percent'][$key] = $calcMethod;
            }
        }

        return $actions;
    }
}
