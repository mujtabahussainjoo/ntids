<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Rule\Action\Rate;

class PerUnitOfWeight extends AbstractRate
{

    /**
     * Calculate fixed amount
     *
     * @return AbstractRate
     */
    protected function fixed()
    {
        $weight = $this->getWeight();

        $amountValue = $this->getAmountValue();
        $resultAmountValue = $amountValue * $weight;
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
        $rate = $this->getRate();
        $amountValue = $this->getAmountValue() ? $this->getAmountValue() / 100 : 0;
        $amount = (float)$rate->getPrice() * $amountValue;

        $weight = $this->getWeight();

        $resultAmountValue = $amount * $weight;
        $this->_setAmountValue($resultAmountValue);

        return $this;
    }

    /**
     * Get all items row weight
     * Note: $item->getRowWeight() works very strangely, use a regular weight & qty instead
     *
     * @return float
     */
    protected function getWeight()
    {
        $weight = 0;
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($this->validItems as $item) {
            $weight += (float)$item->getWeight() * (float)$item->getQty();
        }

        return (float)$weight;
    }
}
