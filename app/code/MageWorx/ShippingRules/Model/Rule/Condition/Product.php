<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\Rule\Condition;

use Magento\SalesRule\Model\Rule\Condition\Product as OriginalProductCondition;

/**
 * Class Product
 */
class Product extends OriginalProductCondition
{
    /**
     * Validate Product Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $abstractModel
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate(\Magento\Framework\Model\AbstractModel $abstractModel)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $abstractModel->getProduct();
        if (!$product instanceof \Magento\Catalog\Model\Product) {
            if (!$abstractModel->getProductId()) {
                return false;
            }
            $product = $this->productRepository->getById($abstractModel->getProductId());
        }

        //use parent product to get category id
        if ($abstractModel->getParentItem() && $this->getAttribute() == 'category_ids') {
            return $this->validateAttribute($abstractModel->getParentItem()->getProduct()->getAvailableInCategories());
        }

        $product->setData('quote_item_sku', $abstractModel->getSku());
        $abstractModel->setProduct($product);

        $result = parent::validate($abstractModel);
        /** @var \MageWorx\ShippingRules\Model\Rule $rule */
        $rule = $this->getRule();
        if ($rule instanceof \MageWorx\ShippingRules\Model\Rule) {
            $rule->logConditions($this->getAttribute(), $result);
        }

        return $result;
    }

    /**
     * Add special attributes
     *
     * @param array $attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['quote_item_sku'] = __('Cart Item SKU (including custom options SKUs)');
    }

    /**
     * Case and type insensitive comparison of values
     *
     * @param string|int|float $validatedValue
     * @param string|int|float $value
     * @param bool $strict
     * @return bool
     */
    protected function _compareValues($validatedValue, $value, $strict = true)
    {
        if ($strict && is_numeric($validatedValue) && is_numeric($value)) {
            return ($validatedValue + 1 - 1) == ($value + 1 - 1);
        } elseif ($strict && $validatedValue === null && is_numeric($value)) {
            return (int)$validatedValue == $value;
        } else {
            $validatePattern = preg_quote($validatedValue, '~');
            if ($strict) {
                $validatePattern = '^' . $validatePattern . '$';
            }
            return (bool)preg_match('~' . $validatePattern . '~iu', $value);
        }
    }
}
