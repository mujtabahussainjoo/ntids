<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Rule\Condition;

class Combine extends \Magento\SalesRule\Model\Rule\Condition\Combine
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \MageWorx\ShippingRules\Model\Rule\Condition\Address $conditionAddress
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \MageWorx\ShippingRules\Model\Rule\Condition\Address $conditionAddress,
        array $data = []
    ) {
        parent::__construct($context, $eventManager, $conditionAddress, $data);
        $this->setType('MageWorx\ShippingRules\Model\Rule\Condition\Combine');
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'MageWorx\ShippingRules\Model\Rule\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $conditions = [
            [
                'value' => '',
                'label' => __('Please choose a condition to add.'),
            ],
            [
                'value' => 'MageWorx\ShippingRules\Model\Rule\Condition\Product\Found',
                'label' => __('Product attribute combination'),
            ],
            [
                'value' => 'MageWorx\ShippingRules\Model\Rule\Condition\Product\Subselect',
                'label' => __('Products subselection'),
            ],
            [
                'value' => 'MageWorx\ShippingRules\Model\Rule\Condition\Combine',
                'label' => __('Conditions combination'),
            ],
            [
                'value' => $attributes,
                'label' => __('Cart Attribute'),
            ],
        ];

        $additional = new \Magento\Framework\DataObject();

        $this->_eventManager->dispatch(
            'shipping_rule_condition_combine',
            ['additional' => $additional]
        );

        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive(
                $conditions,
                $additionalConditions
            );
        }

        return $conditions;
    }
}
