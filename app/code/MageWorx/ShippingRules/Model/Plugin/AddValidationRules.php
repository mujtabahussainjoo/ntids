<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Plugin;

class AddValidationRules
{
    /**
     * @var \MageWorx\ShippingRules\Model\Carrier\AbstractForValidation
     */
    protected $abstractForValidation;

    /**
     * AddValidationRules constructor.
     * @param \MageWorx\ShippingRules\Model\Carrier\AbstractForValidation $abstractForValidation
     */
    public function __construct(
        \MageWorx\ShippingRules\Model\Carrier\AbstractForValidation $abstractForValidation
    ) {
        $this->abstractForValidation = $abstractForValidation;
    }

    /**
     * Add empty abstract carrier to load own validation rules on the checkout page and cart page
     *
     * Workaround for the magento crutch where it unset all layout updates of inactive methods,
     * and this method is private (WTF?!):
     * @see \Magento\Checkout\Block\Checkout\LayoutProcessor::processShippingChildrenComponents()
     *
     *
     * @see view/frontend/web/js/checkout/model/shipping-rates-validation-rules/abstract.js
     * @see view/frontend/web/js/checkout/model/shipping-rates-validator/abstract.js
     * @see view/frontend/web/js/checkout/view/shipping-rates-validation/abstract.js
     *
     * @param \Magento\Shipping\Model\Config $subject
     * @param array $result
     * @return array
     */
    public function afterGetActiveCarriers(\Magento\Shipping\Model\Config $subject, $result)
    {
        if (empty($result['mageworx-shipping'])) {
            $result['mageworx-shipping'] = $this->abstractForValidation;
        }

        return $result;
    }
}
