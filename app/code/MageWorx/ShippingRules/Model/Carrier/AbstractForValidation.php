<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;

class AbstractForValidation extends AbstractCarrier
{
    /**
     * @var string
     */
    protected $_code = 'mageworx-shipping';

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return array
     * @api
     */
    public function collectRates(RateRequest $request)
    {
        return [];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     * @api
     */
    public function getAllowedMethods()
    {
        return [];
    }
}
