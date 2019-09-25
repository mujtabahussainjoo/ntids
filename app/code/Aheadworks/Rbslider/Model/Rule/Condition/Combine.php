<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Context;

/**
 * Class Combine
 *
 * @package Aheadworks\Rbslider\Model\Rule\Condition
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var Product\AttributesFactory
     */
    private $attributeFactory;

    /**
     * @param Context $context
     * @param Product\AttributesFactory $attributesFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Product\AttributesFactory $attributesFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeFactory = $attributesFactory;
        $this->setType(Combine::class);
    }

    /**
     * Prepare child rules option list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = [
            [
                'value' => $this->getType(),
                'label' => __('Conditions Combination')
            ],
            $this->attributeFactory->create()->getNewChildSelectOptions(),
        ];

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * Return conditions
     *
     * @return array|mixed
     */
    public function getConditions()
    {
        if ($this->getData($this->getPrefix()) === null) {
            $this->setData($this->getPrefix(), []);
        }
        return $this->getData($this->getPrefix());
    }

    /**
     * Collect the valid attributes
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /**
             * @var Combine $condition
             */
            $condition->setAggregator($this->getAggregator());
            $condition->setTrue((bool)$this->getValue());
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
