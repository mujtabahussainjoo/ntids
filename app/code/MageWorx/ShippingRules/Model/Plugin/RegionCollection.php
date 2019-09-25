<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Plugin;

use Magento\Directory\Model\ResourceModel\Region\Collection as OriginalRegionsCollection;
use MageWorx\ShippingRules\Model\Region as RegionModel;

class RegionCollection
{
    /**
     * Filter regions collection by active regions
     *
     * @param OriginalRegionsCollection $collection
     * @param bool|false $printQuery
     * @param bool|false $logQuery
     * @return array
     */
    public function beforeLoad(
        OriginalRegionsCollection $collection,
        $printQuery = false,
        $logQuery = false
    ) {
        if (!$collection->isLoaded()) {
            $cols = [
                'is_active'
            ];
            $cond = 'main_table.region_id = extended_regions_data.region_id';
            $alias = "extended_regions_data";
            $table = $collection->getTable(RegionModel::EXTENDED_REGIONS_TABLE_NAME);
            $collection->getSelect()->joinLeft([$alias => $table], $cond, $cols);
            $collection->addFieldToFilter('is_active', ['gt' => 0]);
        }

        return [$printQuery, $logQuery];
    }
}
