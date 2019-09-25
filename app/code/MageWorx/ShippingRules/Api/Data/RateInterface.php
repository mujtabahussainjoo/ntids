<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api\Data;

interface RateInterface
{
    const ENTITY_ID_FIELD_NAME = 'rate_id';

    /**
     * Retrieve rate ID
     *
     * @return int
     */
    public function getRateId();

    /**
     * Retrieve rate code (used during import\export)
     *
     * @return string
     */
    public function getRateCode();

    /**
     * Get id of the corresponding method
     *
     * @return int
     */
    public function getMethodId();

    /**
     * Get priority of the rate (sort order)
     *
     * @return int
     */
    public function getPriority();

    /**
     * Check is rate active
     *
     * @return int|bool
     */
    public function getActive();

    /**
     * Get price calculation method
     *
     * @return int
     */
    public function getRateMethodPrice();

    /**
     * Retrieve rate name
     *
     * @return string
     */
    public function getTitle();

    /**
     * Retrieve corresponding country id
     *
     * @return mixed[]
     */
    public function getCountryId();

    /**
     * Get region plain name
     *
     * @return mixed[]
     */
    public function getRegion();

    /**
     * Get id of region
     *
     * @return mixed[]
     */
    public function getRegionId();

    /**
     * Get conditions zip from
     *
     * @return mixed[]
     */
    public function getZipFrom();

    /**
     * Get conditions zip to
     *
     * @return mixed[]
     */
    public function getZipTo();

    /**
     * Get conditions price from
     *
     * @return float
     */
    public function getPriceFrom();

    /**
     * Get conditions price to
     *
     * @return float
     */
    public function getPriceTo();

    /**
     * Get conditions qty from
     *
     * @return float
     */
    public function getQtyFrom();

    /**
     * Get conditions qty to
     *
     * @return float
     */
    public function getQtyTo();

    /**
     * Get conditions weight from
     *
     * @return float
     */
    public function getWeightFrom();

    /**
     * Get conditions weight to
     *
     * @return float
     */
    public function getWeightTo();

    /**
     * Get rates price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Get rates price per each product in cart
     *
     * @return float
     */
    public function getPricePerProduct();

    /**
     * Get rates price per each item in cart
     *
     * @return float
     */
    public function getPricePerItem();

    /**
     * Get rates price percent per each product in cart
     *
     * @return float
     */
    public function getPricePercentPerProduct();

    /**
     * Get rates price percent per each item in cart
     *
     * @return float
     */
    public function getPricePercentPerItem();

    /**
     * Get item price percent
     *
     * @return float
     */
    public function getItemPricePercent();

    /**
     * Price per each unit of weight
     *
     * @return float
     */
    public function getPricePerWeight();

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
     * Min estimated delivery time (usd to overwrite method value, visible at checkout & cart)
     *
     * @return float
     */
    public function getEstimatedDeliveryTimeMin();

    /**
     * Max estimated delivery time (usd to overwrite method value, visible at checkout & cart)
     *
     * @return float
     */
    public function getEstimatedDeliveryTimeMax();


    /**
     * Get corresponding method code (relation)
     *
     * @return string
     */
    public function getMethodCode();

    /**
     * Get associated store Ids
     *
     * @return mixed[]
     */
    public function getStoreIds();

    /**
     * Get rate store specific labels
     *
     * @return mixed[]
     */
    public function getStoreLabels();

    //____________________________________________ SETTERS _____________________________________________________________

    /**
     * Set rate ID
     *
     * @param int $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setRateId($value);

    /**
     * Set rate code (used during import\export)
     *
     * string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setRateCode($value);

    /**
     * Set priority of the rate (sort order)
     *
     * @param int $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPriority($value);

    /**
     * Check is rate active
     *
     * @param bool $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setActive($value);

    /**
     * Set price calculation method
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setRateMethodPrice($value);

    /**
     * Set rate name
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setTitle($value);

    /**
     * Retrieve corresponding country id
     *
     * @param int[] $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setCountryId($value);

    /**
     * set region plain name
     *
     * @param string[] $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setRegion($value);

    /**
     * set id of region
     *
     * @param int[] $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setRegionId($value);

    /**
     * Set conditions zip from
     *
     * @param string[] $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setZipFrom($value);

    /**
     * Set conditions zip to
     *
     * @param string[] $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setZipTo($value);

    /**
     * Set conditions price from
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPriceFrom($value);

    /**
     * Set conditions price to
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPriceTo($value);

    /**
     * Set conditions qty from
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setQtyFrom($value);

    /**
     * Set conditions qty to
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setQtyTo($value);

    /**
     * Set conditions weight from
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setWeightFrom($value);

    /**
     * Set conditions weight to
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setWeightTo($value);

    /**
     * Set rates price
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPrice($value);

    /**
     * Set rates price per each product in cart
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPricePerProduct($value);

    /**
     * Set rates price per each item in cart
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPricePerItem($value);

    /**
     * Set rates price percent per each product in cart
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPricePercentPerProduct($value);

    /**
     * Set rates price percent per each item in cart
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPricePercentPerItem($value);

    /**
     * Set item price percent
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setItemPricePercent($value);

    /**
     * Price per each unit of weight
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPricePerWeight($value);

    /**
     * Set created at date
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setCreatedAt($value);

    /**
     * Set last updated date
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setUpdatedAt($value);

    /**
     * Min estimated delivery time (usd to overwrite method value, visible at checkout & cart)
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setEstimatedDeliveryTimeMin($value);

    /**
     * Max estimated delivery time (usd to overwrite method value, visible at checkout & cart)
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setEstimatedDeliveryTimeMax($value);

    /**
     * Set id of the corresponding method
     *
     * @param $id
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setMethodId($id);
    
    /**
     * Set corresponding method code
     *
     * @param string $code
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setMethodCode($code);

    /**
     * Set associated store Ids
     *
     * @param mixed[] $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setStoreIds($value);

    /**
     * Set store specific labels (title)
     *
     * @param mixed[] $storeLabels
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setStoreLabels($storeLabels = []);
}
