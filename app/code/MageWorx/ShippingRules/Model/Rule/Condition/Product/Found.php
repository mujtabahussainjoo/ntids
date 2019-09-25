<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\Rule\Condition\Product;

use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context;
use MageWorx\ShippingRules\Model\Rule\Condition\Product as ProductCondition;

class Found extends \MageWorx\ShippingRules\Model\Rule\Condition\Product\Combine
{
    /**
     * @param Context $context
     * @param ProductCondition $ruleConditionProduct
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductCondition $ruleConditionProduct,
        array $data = []
    ) {
        parent::__construct($context, $ruleConditionProduct, $data);
        $this->setType('MageWorx\ShippingRules\Model\Rule\Condition\Product\Found');
    }

    /**
     * Validate
     *
     * @param AbstractModel $abstractModel
     * @return bool
     */
    public function validate(AbstractModel $abstractModel)
    {
        $found = false;
        $true = (bool)$this->getValue();
        $isAll = $this->getAggregator() === 'all';

        foreach ($abstractModel->getAllItems() as $item) {
            $found = $isAll;
            $conditions = $this->getConditions();

            /** @var \Magento\Rule\Model\Condition\AbstractCondition $condition */
            foreach ($conditions as $condition) {
                $validated = $condition->validate($item);
                if ($isAll && !$validated || !$isAll && $validated) {
                    $found = $validated;
                    break;
                }
            }

            if ($found && $true || !$true && $found) {
                break;
            }
        }

        if ($found && $true) {
            $result = true;
        } elseif (!$found && !$true) {
            $result = true;
        } else {
            $result = false;
        }

        /** @var \MageWorx\ShippingRules\Model\Rule $rule */
        $rule = $this->getRule();
        if ($rule instanceof \MageWorx\ShippingRules\Model\Rule) {
            $rule->logConditions('product_found', $result);
        }

        return $result;
    }

    /**
     * Return as html
     *
     * @return string
     */
    public function asHtml()
    {
        $typeElementHtml = $this->getTypeElement()->getHtml();
        $valueElementHtml = $this->getValueElement()->getHtml();
        $aggregatorElementHtml = $this->getAggregatorElement()->getHtml();
        $label = "If an item is %1 in the cart with %2 of these conditions true:";

        $html = $typeElementHtml . __(
            $label,
            $valueElementHtml,
            $aggregatorElementHtml
        );

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }


    /**
     * Load value options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption(
            [
                1 => __('FOUND'),
                0 => __('NOT FOUND')
            ]
        );

        return $this;
    }
}
