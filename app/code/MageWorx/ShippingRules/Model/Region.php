<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model;

use Magento\Directory\Model\Region as OriginalRegion;
use MageWorx\ShippingRules\Api\Data\RegionInterface;

/**
 * Class Region
 * @package MageWorx\ShippingRules\Model
 *
 * @method Region setIsActive($bool)
 * @method Region setIsCustom($bool)
 *
 */
class Region extends OriginalRegion implements RegionInterface
{
    const CURRENT_REGION = 'current_region';
    const EXTENDED_REGIONS_TABLE_NAME = 'mageworx_shippingrules_extended_regions';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\ShippingRules\Model\ResourceModel\Region');
    }

    /**
     * Do not delete original regions
     *
     * @return $this
     */
    public function delete()
    {
        if (!$this->getIsCustom()) {
            $this->setIsActive(false);
            $this->getResource()->save($this);

            return $this;
        }

        $this->_getResource()->delete($this);

        return $this;
    }

    /**
     * @param \Magento\Framework\DataObject $dataObject
     * @return array
     */
    public function validateData($dataObject)
    {
        $result = [];

        return $result;
    }

    /**
     * Retrieve default region name
     *
     * @return string
     */
    public function getDefaultName()
    {
        return $this->getData('default_name');
    }

    /**
     * Retrieve region code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData('code');
    }

    /**
     * Retrieve corresponding country id
     *
     * @return string
     */
    public function getCountryId()
    {
        return $this->getData('country_id');
    }

    /**
     * Retrieve region ID
     *
     * @return int
     */
    public function getRegionId()
    {
        return $this->getData('region_id');
    }

    /**
     * Check is region active
     *
     * @return int
     */
    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    /**
     * Check is custom region
     *
     * @return int|bool
     */
    public function getIsCustom()
    {
        return $this->getData('is_custom');
    }
}
