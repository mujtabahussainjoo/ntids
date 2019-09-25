<?php

namespace Serole\Carreport\Setup;

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

        // Get tutorial_simplenews table
        $tableName = $installer->getTable('car_report_status');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create tutorial_simplenews table
            $table = $installer->getConnection()
                ->newTable($tableName)
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
                    'ID'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'order id'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Product id'
                )
                ->addColumn(
                    'vin',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'vin'
                )
                ->addColumn(
                    'odometer',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'odometer'
                )
                ->addColumn(
                    'result_msg',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'result_msg'
                )
                ->addColumn(
                    'result_html',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'result_html'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'status'
                )
                ->addColumn(
                    'error_count',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Error Count'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'created_at'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'updated_at'
                )
                ->setComment('Car Report Satatua')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}