<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api\Data;

interface RegionInterface
{
    /**
     * Retrieve region name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve default region name
     *
     * @return string
     */
    public function getDefaultName();

    /**
     * Retrieve region code
     *
     * @return string
     */
    public function getCode();

    /**
     * Retrieve corresponding country id
     *
     * @return string
     */
    public function getCountryId();

    /**
     * Retrieve region ID
     *
     * @return int
     */
    public function getRegionId();

    /**
     * Check is region active
     *
     * @return int
     */
    public function getIsActive();

    /**
     * Check is custom region
     *
     * @return int|bool
     */
    public function getIsCustom();
}
