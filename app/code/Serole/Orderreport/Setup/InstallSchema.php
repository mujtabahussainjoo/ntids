<?php

namespace Serole\Orderreport\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Get orderreport table
        $tableName = $installer->getTable('orderreport');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create orderreport table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'orderreport_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'orderreport_id'
                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'title'
                )
                ->addColumn(
                    'filename',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'filename'
                )
                ->addColumn(
                    'content',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'content'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'status'
                )
                ->addColumn(
                    'created_time',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'created_time'
                )
                ->addColumn(
                    'update_time',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'update_time'
                )
                ->setComment('Order Reports Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $subsicyTableName = $installer->getTable('subsidy_data');
        if ($installer->getConnection()->isTableExists($subsicyTableName) != true) {
            $table = $installer->getConnection()
                ->newTable($subsicyTableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Id'
                )
                ->addColumn(
                    'invoice_no',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'invoice_no'
                )
                ->addColumn(
                    'category',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'category'
                )
                ->addColumn(
                    'product',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'product'
                )
                ->addColumn(
                    'price',
                    Table::TYPE_DECIMAL,
                    null,
                    ['nullable' => false,
                     'scale'    => 2,
                     'precision' => 10,
                    ],
                    'price'
                )

                ->addColumn(
                    'subsidy',
                    Table::TYPE_DECIMAL,
                    null,
                    ['nullable' => false,
                     'scale'    => 2,
                     'precision' => 10,
                    ],
                    'subsidy'
                )

                ->addColumn(
                    'qty',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'qty'
                )

                ->addColumn(
                    'total_price',
                    Table::TYPE_DECIMAL,
                    null,
                    ['nullable' => false,
                     'scale'    => 2,
                     'precision' => 10,
                     ],
                    'total_price'
                )

                ->addColumn(
                    'total_subsidy',
                    Table::TYPE_DECIMAL,
                    null,
                    ['nullable' => false,
                     'scale'    => 2,
                     'precision' => 10,
                    ],
                    'total_subsidy'
                )

                ->addColumn(
                    'store_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'store_id'
                )
                ->setComment('Subsidy Data')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $priceHistoryName = $installer->getTable('price_history');
        if ($installer->getConnection()->isTableExists($priceHistoryName) != true) {
            $table = $installer->getConnection()
                ->newTable($priceHistoryName)
                ->addColumn(
                    'store_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'store_id'
                )
                ->addColumn(
                    'sku',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'sku'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'created_at'
                )
                ->addColumn(
                    'rrp',
                    Table::TYPE_DECIMAL,
                    '10,2',
                    ['nullable' => false],
                    'rrp'
                )
               ->addColumn(
                    'sell_price',
                    Table::TYPE_DECIMAL,
                    '10,2',
                    ['nullable' => false],
                    'sell_price'
                )
                ->addColumn(
                    'subsidy',
                    Table::TYPE_DECIMAL,
                    '10,2',
                    ['nullable' => false],
                    'subsidy'
                )
                ->addColumn(
                    'vip_subsidy',
                    Table::TYPE_DECIMAL,
                    '10,2',
                    ['nullable' => false],
                    'vip_subsidy'
                )
                ->addColumn(
                    'vip_price',
                    Table::TYPE_DECIMAL,
                    '10,2',
                    ['nullable' => false],
                    'vip_price'
                )
                ->setComment('Order Reports Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}