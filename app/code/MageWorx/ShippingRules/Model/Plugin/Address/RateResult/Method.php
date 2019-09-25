<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\Plugin\Address\RateResult;

class Method
{

    /**
     * @var \MageWorx\ShippingRules\Helper\Data
     */
    protected $helper;

    /**
     * @param \MageWorx\ShippingRules\Helper\Data $helper
     */
    public function __construct(
        \MageWorx\ShippingRules\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Change default shipping methods title
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $subject
     * @param callable $proceed
     * @param string $key
     * @param null $index
     *
     * @return null
     */
    public function aroundGetData(
        \Magento\Quote\Model\Quote\Address\RateResult\Method $subject,
        callable $proceed,
        $key = '',
        $index = null
    ) {
        if (!$key) {
            return $proceed($key, $index);
        }

        if ($key !== 'method_title') {
            return $proceed($key, $index);
        }

        $carrierCode = $subject->getCarrier();
        $methodCode = $subject->getMethod();
        $code = $carrierCode . '_' . $methodCode;
        $possibleTitle = $this->helper->getMethodTitle($code);
        if ($possibleTitle) {
            return $possibleTitle;
        }

        return $proceed($key, $index);
    }
}