<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api;

/**
 * Interface ExtendedZoneInterface
 * @package MageWorx\ShippingRules\Api
 * @see \MageWorx\ShippingRules\Model\ExtendedZone
 */
interface ExtendedZoneInterface
{
    /**
     * Get url to the image, if exist
     *
     * @return string
     */
    public function getImageUrl();

    /**
     * Get label for store
     *
     * @param null $storeId
     * @return string
     */
    public function getLabel($storeId = null);

    /**
     * Get corresponding store labels
     * where the key is store view id (int), value is label (string)
     *
     * @return mixed[]
     */
    public function getStoreLabels();
}
