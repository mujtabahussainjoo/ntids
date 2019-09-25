<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use MageWorx\ShippingRules\Model\Carrier;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Magento\Framework\Module\ResourceInterface
     */
    protected $moduleResource;

    /**
     * InstallSchema constructor.
     * @param \Magento\Framework\Module\ResourceInterface $moduleResource
     */
    public function __construct(
        \Magento\Framework\Module\ResourceInterface $moduleResource
    ) {
        $this->moduleResource = $moduleResource;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'mageworx_shippingrules'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_shippingrules')
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            [],
            'Name'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '64k',
            [],
            'Description'
        )->addColumn(
            'from_date',
            Table::TYPE_DATE,
            null,
            ['nullable' => true, 'default' => null],
            'From'
        )->addColumn(
            'to_date',
            Table::TYPE_DATE,
            null,
            ['nullable' => true, 'default' => null],
            'To'
        )->addColumn(
            'days_of_week',
            Table::TYPE_TEXT,
            '64k',
            [],
            'Days of Week (Available On)'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Is Active'
        )->addColumn(
            'conditions_serialized',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Conditions Serialized'
        )->addColumn(
            'actions_serialized',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Actions Serialized'
        )->addColumn(
            'stop_rules_processing',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Stop Rules Processing'
        )->addColumn(
            'shipping_methods',
            Table::TYPE_TEXT,
            '64k',
            [],
            'Shipping Methods'
        )->addColumn(
            'disabled_shipping_methods',
            Table::TYPE_TEXT,
            '64k',
            [],
            'Disabled Shipping Methods'
        )->addColumn(
            'enabled_shipping_methods',
            Table::TYPE_TEXT,
            '64k',
            [],
            'Enabled Shipping Methods'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Sort Order (Priority)'
        )->addColumn(
            'action_type',
            Table::TYPE_TEXT,
            '64k',
            [],
            'Action Type'
        )->addColumn(
            'simple_action',
            Table::TYPE_TEXT,
            '64k',
            [],
            'Simple Action'
        )->addColumn(
            'amount',
            Table::TYPE_TEXT,
            '2M',
            []
        )->addColumn(
            'time_from',
            Table::TYPE_INTEGER,
            null,
            [],
            'Time From'
        )->addColumn(
            'time_to',
            Table::TYPE_INTEGER,
            null,
            [],
            'Time To'
        )->addColumn(
            'use_time',
            Table::TYPE_SMALLINT,
            null,
            [],
            'Use or not time flag'
        )->addColumn(
            'time_enabled',
            Table::TYPE_SMALLINT,
            null,
            [],
            'Time Range is enabled or disabled time'
        )->addIndex(
            $installer->getIdxName('mageworx_shippingrules', ['is_active', 'sort_order', 'to_date', 'from_date']),
            ['is_active', 'sort_order', 'to_date', 'from_date']
        )->setComment(
            'Shippingrules'
        );

        $installer->getConnection()->createTable($table);

        $storeTable = $installer->getTable('store');
        $customerGroupsTable = $installer->getTable('customer_group');
        $rulesStoresTable = $installer->getTable('mageworx_shippingrules_store');
        $rulesCustomerGroupsTable = $installer->getTable('mageworx_shippingrules_customer_group');

        /**
         * Create table 'mageworx_shippingrules_store' if not exists. This table will be used instead of
         * column store_ids of main catalog shipping rules table
         */
        $table = $installer->getConnection()->newTable(
            $rulesStoresTable
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store View Id'
        )->addIndex(
            $installer->getIdxName('mageworx_shippingrules_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('mageworx_shippingrules_store', 'rule_id', 'mageworx_shippingrules', 'rule_id'),
            'rule_id',
            $installer->getTable('mageworx_shippingrules'),
            'rule_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('mageworx_shippingrules_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $storeTable,
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Shipping Rules To Stores Relations'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'mageworx_shippingrules_customer_group' if not exists. This table will be used instead of
         * column customer_group_ids of main shipping rules table
         */
        $table = $installer->getConnection()->newTable(
            $rulesCustomerGroupsTable
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'customer_group_id',
            $this->getCustomerGroupColumnType(),
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group Id'
        )->addIndex(
            $installer->getIdxName('mageworx_shippingrules_customer_group', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $installer->getFkName(
                'mageworx_shippingrules_customer_group',
                'rule_id',
                'mageworx_shippingrules',
                'rule_id'
            ),
            'rule_id',
            $installer->getTable('mageworx_shippingrules'),
            'rule_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'mageworx_shippingrules_customer_group',
                'customer_group_id',
                'customer_group',
                'customer_group_id'
            ),
            'customer_group_id',
            $customerGroupsTable,
            'customer_group_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Shipping Rules To Customer Groups Relations'
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

    /**
     * @return string
     */
    protected function getCustomerGroupColumnType()
    {
        $customerDbVersion = $this->moduleResource->getDbVersion('Magento_Customer');

        if (version_compare($customerDbVersion, '2.0.10', '<')) {
            return Table::TYPE_SMALLINT;
        }

        return Table::TYPE_INTEGER;
    }
}
