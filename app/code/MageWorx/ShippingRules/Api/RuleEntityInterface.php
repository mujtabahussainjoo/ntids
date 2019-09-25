<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api;

interface RuleEntityInterface
{
    /**
     * Get store specific error message
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $rate
     * @param null $storeId
     * @return mixed
     */
    public function getStoreSpecificErrorMessage(
        \Magento\Quote\Model\Quote\Address\RateResult\Method $rate,
        $storeId = null
    );
}
