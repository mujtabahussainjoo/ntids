<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Observer\Sales\Quote\Address;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CollectTotalsAfter implements ObserverInterface
{

    /**
     * Remove disabled shipping methods from collection
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {

        $event = $observer->getEvent();

        /** @var \Magento\Quote\Model\ShippingAssignment $shippingAssignment */
        $shippingAssignment = $event->getShippingAssignment();
        /** @var \Magento\Quote\Model\Shipping $shipping */
        $shipping = $shippingAssignment->getShipping();
        /** @var \Magento\Quote\Model\Quote\Address $shippingAddress */
        $shippingAddress = $shipping->getAddress();
        /** @var array of \Magento\Quote\Model\Quote\Address\Rate\Interceptor $rates */
        $rates = $shippingAddress->getAllShippingRates();
        /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $ratesCollection */
        $ratesCollection = $shippingAddress->getShippingRatesCollection();
        /** @var array $disabledShippingMethods */
        $disabledShippingMethods = $shippingAddress->getDisabledShippingMethods();

        if (!$disabledShippingMethods) {
            return $this;
        }

        /** @var \Magento\Quote\Model\Quote\Address\Rate $rate */
        foreach ($rates as $rate) {
            /** @var string like "carrier_method" $code */
            $code = $rate->getCode();
            if (!isset($disabledShippingMethods[$code])) {
                continue;
            }

            if (!$disabledShippingMethods[$code]) {
                $ratesCollection->removeItemByKey($rate->getId());
            }
        }

        return $this;
    }
}
