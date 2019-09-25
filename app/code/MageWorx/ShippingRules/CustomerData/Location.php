<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use MageWorx\ShippingRules\Api\AddressResolverInterface;

/**
 * Class Location
 * @package MageWorx\ShippingRules\CustomerData
 *
 * Used to store and retrieve customers location data on the frontend.
 * @see MageWorx/ShippingRules/view/frontend/web/js/location.js
 */
class Location implements SectionSourceInterface
{
    /**
     * @var AddressResolverInterface
     */
    protected $addressResolver;

    /**
     * Location constructor.
     * @param AddressResolverInterface $addressResolver
     */
    public function __construct(
        AddressResolverInterface $addressResolver
    ) {
        $this->addressResolver = $addressResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $data = [
            'country_code' => $this->addressResolver->getCountryId(),
            'country' => $this->addressResolver->getCountryName(),
            'region_code' => $this->addressResolver->getRegionCode(),
            'region' => $this->addressResolver->getRegion(),
            'regionJsonList' => $this->addressResolver->getRegionJsonList()
        ];

        return $data;
    }
}
