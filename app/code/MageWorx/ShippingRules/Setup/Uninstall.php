<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use MageWorx\ShippingRules\Model\Carrier;
use MageWorx\ShippingRules\Model\Region as RegionModel;

class Uninstall implements UninstallInterface
{
    /**
     * Module uninstall code
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $connection = $setup->getConnection();

        $connection->dropTable($connection->getTableName('mageworx_shippingrules_customer_group'));
        $connection->dropTable($connection->getTableName('mageworx_shippingrules_store'));
        $connection->dropTable($connection->getTableName('mageworx_shippingrules'));
        $connection->dropTable($connection->getTableName(Carrier::CARRIER_TABLE_NAME));
        $connection->dropTable($connection->getTableName(Carrier::METHOD_TABLE_NAME));
        $connection->dropTable($connection->getTableName(Carrier::METHOD_LABELS_TABLE_NAME));
        $connection->dropTable($connection->getTableName(Carrier::CARRIER_LABELS_TABLE_NAME));

        $this->removeExtendedRegions($setup);
        $connection->dropTable($connection->getTableName(RegionModel::EXTENDED_REGIONS_TABLE_NAME));

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function removeExtendedRegions(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $regionsTable = $setup->getTable('directory_country_region');
        $extendedRegionsTable = $setup->getTable(RegionModel::EXTENDED_REGIONS_TABLE_NAME);
        $select = $connection->select()->from($extendedRegionsTable, ['region_id'])->where('is_custom != 0');
        $results = $connection->fetchAll($select);
        $ids = [];
        foreach ($results as $result) {
            $ids[] = $result['region_id'];
        }
        if (!empty($ids)) {
            $connection->delete($regionsTable, 'region_id IN (' . implode(',', $ids) . ')');
        }
    }
}
