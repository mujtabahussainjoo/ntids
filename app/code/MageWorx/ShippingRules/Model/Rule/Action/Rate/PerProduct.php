<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Rule\Action\Rate;

class PerProduct extends AbstractRate
{

    /**
     * Calculate fixed amount
     *
     * @return AbstractRate
     */
    protected function fixed()
    {
        $productQty = 0;
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($this->validItems as $item) {
            if ($item->getParentItem()) {
                $qty = (float)$item->getParentItem()->getQty();
            } else {
                $qty = (float)$item->getQty();
            }
            $productQty += $qty;
        }

        $amountValue = $this->getAmountValue();
        $resultAmountValue = $amountValue * $productQty;
        $this->_setAmountValue($resultAmountValue);

        return $this;
    }

    /**
     * Calculate percent of amount
     *
     * @return AbstractRate
     */
    protected function percent()
    {
        $amountValue = $this->getAmountValue() ? $this->getAmountValue() / 100 : 0;
        $price = 0;
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($this->validItems as $item) {
            if ($item->getParentItem()) {
                $price += (float)$item->getParentItem()->getRowTotal();
            } else {
                $price += (float)$item->getRowTotal();
            }
        }
        $amount = $price * $amountValue;

        $this->_setAmountValue($amount);

        return $this;
    }
}
