<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Config\Source\Locale;

use Magento\Framework\Data\OptionSourceInterface;
use MageWorx\ShippingRules\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use MageWorx\ShippingRules\Model\ResourceModel\Region\Collection as RegionCollection;

class Country extends \Magento\Config\Model\Config\Source\Locale\Country implements OptionSourceInterface
{
    const CODE_WORLD = '001';

    /**
     * @var RegionCollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * @var array
     */
    protected $keyValuePairs;

    /**
     * Country constructor.
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param RegionCollectionFactory $regionCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Locale\ListsInterface $localeLists,
        RegionCollectionFactory $regionCollectionFactory
    ) {
        parent::__construct($localeLists);
        $this->regionCollectionFactory = $regionCollectionFactory;
    }

    /**
     * @var array
     */
    protected $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $options = [];
        $origCountryOptions = parent::toOptionArray();
        $options = array_merge($options, $origCountryOptions);
        /** @var RegionCollection $regionCollection */
        $regionCollection = $this->regionCollectionFactory->create();
        $regionCollection->addFieldToSelect('country_id');
        $regionCollection->getSelect()->group('country_id');
        $regionCollection->load();
        foreach ($options as $index => $option) {
            if (!$option['value']) {
                continue;
            }
            $countryWithRegions = $regionCollection->getItemsByColumnValue(
                'country_id',
                $option['value']
            );
            $options[$index]['is_region_visible'] = !empty($countryWithRegions);
        }

        $this->options = $options;

        return $this->options;
    }

    /**
     * Get countries as a key => value pairs
     * where:
     * key - country id
     * value - country label
     *
     * @return array
     */
    public function toKeyValuePairs()
    {
        if ($this->keyValuePairs !== null) {
            return $this->keyValuePairs;
        }

        $keyValuePairs = [];
        $origCountryOptions = parent::toOptionArray();
        foreach ($origCountryOptions as $index => $option) {
            if (!$option['value']) {
                continue;
            }

            $keyValuePairs[$option['value']] = $option['label'];
        }

        $this->keyValuePairs = $keyValuePairs;

        return $this->keyValuePairs;
    }
}
