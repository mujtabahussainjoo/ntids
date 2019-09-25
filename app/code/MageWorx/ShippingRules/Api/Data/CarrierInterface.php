<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api\Data;

use Zend\Stdlib\DateTime;

interface CarrierInterface
{
    const ENTITY_ID_FIELD_NAME = 'carrier_id';

    /**
     * Retrieve carrier name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve carrier title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Retrieve carrier code
     *
     * @return string
     */
    public function getCarrierCode();

    /**
     * Retrieve corresponding model name\path
     *
     * @return string
     */
    public function getModel();

    /**
     * Retrieve carrier ID
     *
     * @return int
     */
    public function getCarrierId();

    /**
     * Check is carrier active
     *
     * @return int|bool
     */
    public function getActive();

    /**
     * sallowspecific
     *
     * @return int
     */
    public function getSallowspecific();

    /**
     * Carrier type
     *
     * @return string
     */
    public function getType();

    /**
     * Carrier error message
     *
     * @return string
     */
    public function getSpecificerrmsg();

    /**
     * Default carrier price
     *
     * @return float (12,2)
     */
    public function getPrice();

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
     * Get carriers store specific labels
     *
     * @return mixed[]
     */
    public function getStoreLabels();

    /**
     * Get store ids for carrier
     *
     * @return mixed[]
     */
    public function getStoreIds();

    /**
     * Set carrier name
     *
     * If name is no declared, then default_name is used
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Set carrier title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Set carrier code
     *
     * @param string $code
     * @return $this
     */
    public function setCarrierCode($code);

    /**
     * Retrieve corresponding model name\path
     *
     * @param string $model
     * @return $this
     */
    public function setModel($model);

    /**
     * Set carrier ID
     *
     * @param int $id
     * @return $this
     */
    public function setCarrierId($id);

    /**
     * Set is carrier active
     *
     * @param int $active
     * @return $this
     */
    public function setActive($active);

    /**
     * sallowspecific
     *
     * @param int $sallowSpecific
     * @return $this
     */
    public function setSallowspecific($sallowSpecific);

    /**
     * Carrier type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Carrier error message
     *
     * @param string $msg
     * @return $this
     */
    public function setSpecificerrmsg($msg);

    /**
     * Default carrier price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * Get created at date
     *
     * @param string|int|DateTime $date
     * @return $this
     */
    public function setCreatedAt($date);

    /**
     * Get last updated date
     *
     * @param string|int|DateTime $date
     * @return $this
     */
    public function setUpdatedAt($date);

    /**
     * @param mixed[] $storeLabels
     * @return $this
     */
    public function setStoreLabels($storeLabels = []);

    /**
     * @param mixed[] $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds = []);
}
