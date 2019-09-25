<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Zone\Condition;

use Magento\Framework\DataObjectFactory;

/**
 * Class Combine
 *
 * @method Combine setType($string)
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager = null;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Address
     */
    protected $conditionAddress;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \MageWorx\ShippingRules\Model\Zone\Condition\Address $conditionAddress
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \MageWorx\ShippingRules\Model\Zone\Condition\Address $conditionAddress,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType('MageWorx\ShippingRules\Model\Zone\Condition\Combine');
        $this->eventManager = $eventManager;
        $this->conditionAddress = $conditionAddress;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressAttributes = $this->conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'MageWorx\ShippingRules\Model\Zone\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $conditions = [
            [
                'value' => '',
                'label' => __('Please choose a condition to add.'),
            ],
            [
                'value' => 'MageWorx\ShippingRules\Model\Zone\Condition\Combine',
                'label' => __('Conditions combination'),
            ],
            [
                'value' => $attributes,
                'label' => __('Address Attribute'),
            ],
        ];

        /** @var \Magento\Framework\DataObject $additional */
        $additional = $this->dataObjectFactory->create();

        $this->eventManager->dispatch(
            'shipping_zone_condition_combine',
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
