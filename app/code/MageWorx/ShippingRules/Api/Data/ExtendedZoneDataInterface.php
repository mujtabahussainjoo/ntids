<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api\Data;

/**
 * Interface ExtendedZoneInterface
 * @see \MageWorx\ShippingRules\Model\ExtendedZone
 */
interface ExtendedZoneDataInterface
{
    /**
     * Return priority of the current zone (sort order)
     *
     * @return int
     */
    public function getPriority();

    /**
     * @return boolean
     */
    public function getIsActive();

    /**
     * Unique zone name
     *
     * @return string
     */
    public function getName();

    /**
     * Zone description text
     *
     * @return string
     */
    public function getDescription();

    /**
     * Image path (relative). Used as zone preview on frontend
     *
     * @return string
     */
    public function getImage();

    /**
     * List of countries assigned to the zone
     *
     * @return mixed[]
     */
    public function getCountriesId();

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Retrieve zone ID
     *
     * @return int
     */
    public function getEntityId();
}
