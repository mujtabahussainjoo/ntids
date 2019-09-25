<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Config\Source\Locale;


class AllRegions extends \Magento\Directory\Model\Config\Source\Allregion
{
    /**
     * @var array
     */
    protected $_countries;

    /**
     * @var array
     */
    protected $_options;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $_countryCollectionFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_regionCollectionFactory;

    /**
     * @var array
     */
    protected $keyValuePairs;

    /**
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = true)
    {
        if (!$this->_options) {
            $countriesAsArray = $this->_countryCollectionFactory
                ->create()
                ->load()
                ->toOptionArray(false);
            
            $this->_countries  = [];
            foreach ($countriesAsArray as $country) {
                $this->_countries[$country['value']] = $country['label'];
            }

            $countryStates    = [];
            /** @var \Magento\Directory\Model\ResourceModel\Region\Collection $statesCollection */
            $statesCollection = $this->_regionCollectionFactory
                ->create()
                ->load();
            foreach ($statesCollection as $state) {
                $countryStates[$state->getCountryId()][$state->getId()] = $state->getDefaultName();
            }
            uksort($countryStates, [$this, 'sortRegionCountries']);

            $this->_options = [];
            foreach ($countryStates as $countryId => $states) {
                $stateOptions = [];
                foreach ($states as $regionId => $regionName) {
                    $stateOptions[] = [
                        'label'      => $regionName,
                        'value'      => $regionId,
                        'country_id' => $countryId
                    ];
                }
                $this->_options[] = [
                    'label'      => $this->_countries[$countryId],
                    'value'      => $stateOptions,
                    'country_id' => $countryId
                ];
            }
        }
        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => '']);
        }

        return $options;
    }

    /**
     * Get regions (all) as a key => value pairs
     * where:
     * key - region id
     * value - region label
     *
     * @return array
     */
    public function toKeyValuePairs()
    {
        if ($this->keyValuePairs !== null) {
            return $this->keyValuePairs;
        }

        $origStateOptions = [];
        /** @var \Magento\Directory\Model\ResourceModel\Region\Collection $statesCollection */
        $statesCollection = $this->_regionCollectionFactory
            ->create()
            ->load();
        foreach ($statesCollection as $state) {
            $origStateOptions[$state->getId()] = $state->getDefaultName();
        }

        $this->keyValuePairs = $origStateOptions;

        return $this->keyValuePairs;
    }
}