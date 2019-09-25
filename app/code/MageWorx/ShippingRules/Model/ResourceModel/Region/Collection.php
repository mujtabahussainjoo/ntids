<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ResourceModel\Region;

use Magento\Directory\Model\ResourceModel\Region\Collection as OriginalRegionCollection;
use MageWorx\ShippingRules\Model\Region as RegionModel;

class Collection extends OriginalRegionCollection
{
    /**
     * @var bool
     */
    protected $extendedTableJoined = false;

    /**
     * Define main, country, locale region name tables
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\ShippingRules\Model\Region', 'MageWorx\ShippingRules\Model\ResourceModel\Region');

        $this->_countryTable = $this->getTable('directory_country');
        $this->_regionNameTable = $this->getTable('directory_country_region_name');
        $this->_map['fields']['region_id'] = 'main_table.region_id';
    }

    /**
     * @return string
     */
    public function getIdFieldName()
    {
        return parent::getIdFieldName() ? parent::getIdFieldName() : 'region_id';
    }

    /**
     * Initialize select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        if ($this->extendedTableJoined) {
            return $this;
        }

        $columns = [
            'is_active',
            'is_custom'
        ];
        $cond = 'main_table.region_id = extended_regions_data.region_id';
        $this->joinLeft(
            ["extended_regions_data" => $this->getTable(RegionModel::EXTENDED_REGIONS_TABLE_NAME)],
            $cond,
            $columns
        );

        $this->extendedTableJoined = true;

        return $this;
    }

    /**
     * Right join table to collection select
     *
     * @param string $table
     * @param string $cond
     * @param string $cols
     * @return $this
     */
    public function joinLeft($table, $cond, $cols = '*')
    {
        if (is_array($table)) {
            foreach ($table as $k => $v) {
                $alias = $k;
                $table = $v;
                break;
            }
        } else {
            $alias = $table;
        }

        if (isset($alias) && !isset($this->_joinedTables[$alias])) {
            $this->getSelect()->joinLeft([$alias => $this->getTable($table)], $cond, $cols);
            $this->_joinedTables[$alias] = true;
        }

        return $this;
    }
}
