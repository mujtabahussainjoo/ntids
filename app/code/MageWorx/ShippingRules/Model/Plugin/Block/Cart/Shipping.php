<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Plugin\Block\Cart;

use MageWorx\ShippingRules\Model\CheckoutConfigProvider;

class Shipping
{
    /**
     * @var CheckoutConfigProvider
     */
    protected $checkoutConfigProvider;

    public function __construct(
        CheckoutConfigProvider $checkoutConfigProvider
    ) {
        $this->checkoutConfigProvider = $checkoutConfigProvider;
    }

    /**
     * Adds to the default checkout config values (country_id and region_id) detected by GeoIp
     *
     * @param \Magento\Checkout\Block\Cart\Shipping $subject
     * @param array $config
     * @return array
     */
    public function afterGetCheckoutConfig($subject, $config)
    {
        $config = array_merge($config, $this->checkoutConfigProvider->getConfig());

        return $config;
    }
}
