<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api\Data;

interface ZoneInterface
{
    /**
     * Retrieve zone name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve zone description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Retrieve zone ID
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Check is zone active
     *
     * @return int|bool
     */
    public function getIsActive();

    /**
     * Zones sort order (priority)
     *
     * @return int
     */
    public function getPriority();

    /**
     * Get created at date
     *
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * Get last updated date
     *
     * @return mixed
     */
    public function getUpdatedAt();

    /**
     * Get serialized zones conditions
     *
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * Default shipping method code
     *
     * @return string
     */
    public function getDefaultShippingMethod();
}
