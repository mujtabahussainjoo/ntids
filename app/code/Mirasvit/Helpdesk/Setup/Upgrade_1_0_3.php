<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Helpdesk\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Upgrade_1_0_3
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_schedule')
        )
        ->addColumn(
            'schedule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Workign Hours Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true],
            'Schedule Name'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true, 'default' => 0],
            'Is Active'
        )
        ->addColumn(
            'active_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Active From'
        )
        ->addColumn(
            'active_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Active To'
        )
        ->addColumn(
            'timezone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Size'
        )
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Body'
        )
        ->addColumn(
            'is_holiday',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'External Id'
        )
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'type'
        )
        ->addColumn(
            'working_hours',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            512,
            ['unsigned' => false, 'nullable' => true],
            'Storage'
        )
        ->addColumn(
            'open_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            512,
            ['unsigned' => false, 'nullable' => true],
            'Storage'
        )
        ->addColumn(
            'closed_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            512,
            ['unsigned' => false, 'nullable' => true],
            'Storage'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_schedule_store')
        )
            ->addColumn(
                'schedule_store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Working Hours Store Id'
            )
            ->addColumn(
                'whs_schedule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Working Hours Id'
            )
            ->addColumn(
                'whs_store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_field_store', ['whs_schedule_id']),
                ['whs_schedule_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_field_store', ['whs_store_id']),
                ['whs_store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_schedule_store',
                    'whs_store_id',
                    'store',
                    'store_id'
                ),
                'whs_store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_schedule_store',
                    'whs_schedule_id',
                    'mst_helpdesk_schedule',
                    'schedule_id'
                ),
                'whs_schedule_id',
                $installer->getTable('mst_helpdesk_schedule'),
                'schedule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }
}