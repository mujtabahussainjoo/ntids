<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Rbslider module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.0', '<=')) {
            $this->addRelatedTable($setup);
            $this->migrateStoreCustomerGroupData($setup);
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.4', '<=')) {
            $this->removeFkKeyFromCustomerGroupsTable($setup);
        }
    }

    /**
     * Add related table store and customer group for the slide table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function addRelatedTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'aw_rbslider_slide_store'
         */
        $storeTable = $setup->getConnection()->newTable(
            $setup->getTable('aw_rbslider_slide_store')
        )->addColumn(
            'slide_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Slide ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store ID'
        )->addIndex(
            $setup->getIdxName('aw_rbslider_slide_store', ['slide_id']),
            ['slide_id']
        )->addIndex(
            $setup->getIdxName('aw_rbslider_slide_store', ['slide_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName('aw_rbslider_slide_store', 'slide_id', 'aw_rbslider_slide', 'id'),
            'slide_id',
            $setup->getTable('aw_rbslider_slide'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('aw_rbslider_slide_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'AW Rbslider Slide To Store Relation Table'
        );
        $setup->getConnection()->createTable($storeTable);

        /**
         * Create table 'aw_rbslider_slide_customer_group'
         */
        $customerGroupTable = $setup->getConnection()->newTable(
            $setup->getTable('aw_rbslider_slide_customer_group')
        )->addColumn(
            'slide_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Rule ID'
        )->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer Group ID'
        )->addIndex(
            $setup->getIdxName('aw_rbslider_slide_customer_group', ['slide_id']),
            ['slide_id']
        )->addIndex(
            $setup->getIdxName('aw_rbslider_slide_customer_group', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $setup->getFkName('aw_rbslider_slide_customer_group', 'slide_id', 'aw_rbslider_slide', 'id'),
            'slide_id',
            $setup->getTable('aw_rbslider_slide'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'AW Rbslider Slide To Customer Group Relation Table'
        );
        $setup->getConnection()->createTable($customerGroupTable);

        return $this;
    }

    /**
     * Migrate and delete store and customer group data from aw_rbslider_slide table into aw_rbslider_slide_store
     * and aw_rbslider_slide_customer_group tables
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function migrateStoreCustomerGroupData(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $select = $connection->select()
            ->from($setup->getTable('aw_rbslider_slide'), ['id', 'store_ids', 'customer_group_ids']);
        $data = $connection->fetchAssoc($select);

        $toInsertStore = [];
        $toInsertCustomerGroup = [];
        foreach ($data as $row) {
            $storeIds = explode(',', $row['store_ids']);
            $customerGroupIds = explode(',', $row['customer_group_ids']);
            foreach ($storeIds as $storeId) {
                $toInsertStore[] = [
                    'slide_id' => (int)$row['id'],
                    'store_id' => (int)$storeId,
                ];
            }
            foreach ($customerGroupIds as $customerGroupId) {
                $toInsertCustomerGroup[] = [
                    'slide_id' => (int)$row['id'],
                    'customer_group_id' => (int)$customerGroupId,
                ];
            }
        }
        if (count($toInsertStore)) {
            $connection->insertMultiple(
                $setup->getTable('aw_rbslider_slide_store'),
                $toInsertStore
            );
        }
        if (count($toInsertCustomerGroup)) {
            $connection->insertMultiple(
                $setup->getTable('aw_rbslider_slide_customer_group'),
                $toInsertCustomerGroup
            );
        }

        $connection->dropColumn($setup->getTable('aw_rbslider_slide'), 'store_ids');
        $connection->dropColumn($setup->getTable('aw_rbslider_slide'), 'customer_group_ids');

        return $this;
    }

    /**
     * Drop foreign key from 'aw_rbslider_slide_customer_group' table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    private function removeFkKeyFromCustomerGroupsTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();

        $connection->dropForeignKey(
            $setup->getTable('aw_rbslider_slide_customer_group'),
            $setup->getFkName(
                'aw_rbslider_slide_customer_group',
                'customer_group_id',
                'customer_group',
                'customer_group_id'
            )
        );

        return $this;
    }
}
