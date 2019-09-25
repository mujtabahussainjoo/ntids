<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api;

/**
 * Interface AddressResolverInterface
 * @package MageWorx\ShippingRules\Api
 *
 * Inside we use a regular Zones (not an Pop-up Zones)
 */
interface AddressResolverInterface
{
    /**
     * Get array of the regions by country_id (used as key)
     *
     * @return mixed[]
     */
    public function getRegionJsonList();

    /**
     * Get visitors country id
     *
     * @return int
     */
    public function getCountryId();

    /**
     * Get visitors region id
     *
     * @return string
     */
    public function getRegionId();

    /**
     * Get visitors region (as string)
     *
     * @return string
     */
    public function getRegion();

    /**
     * Get visitors country name
     *
     * @return string
     */
    public function getCountryName();

    /**
     * Get visitors region code
     *
     * @return string
     */
    public function getRegionCode();
}
