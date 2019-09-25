<?php
/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\MembershipSubscription\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
 
        $installer->startSetup();
 
        /*
         * Create table 'magedelight_membership_products'
         */
        if (!$installer->tableExists('magedelight_membership_products')) {
            $magedelight_membership_products = $installer->getConnection()->newTable(
                $installer->getTable('magedelight_membership_products')
            )
            ->addColumn(
                'membership_product_id',
                Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Primary Id'
            )
            ->addColumn(
                'product_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Name'
            )
            ->addColumn(
                'membership_duration',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Membership Duration'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false,'unsigned' => true],
                'Catalog Product Id'
            )
            ->addColumn(
                'featured',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false,'unsigned' => true],
                'Is Featured Product'
            )
            ->addColumn(
                'customer_group_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Customer Group Id'
            )
            ->addColumn(
                'related_customer_group_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Related Customer Group Id'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
            ->addIndex(
                $installer->getIdxName('magedelight_membership_products', ['product_id']),
                ['product_id']
            )
            ->addForeignKey(
                $installer->getFkName('magedelight_membership_products', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Magedelight Membership Products');
            
            $installer->getConnection()->createTable($magedelight_membership_products);
        }
 
        
        if (!$installer->tableExists('magedelight_membership_orders')) {
            $magedelight_membership_orders = $installer->getConnection()->newTable(
                $installer->getTable('magedelight_membership_orders')
            )
            ->addColumn(
                'membership_order_id',
                Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Primary Id'
            )
            ->addColumn(
                'membership_product_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Membership Product id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Product id'
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Order id'
            )
            ->addColumn(
                'order_status',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Order Status'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Customer id'
            )
            ->addColumn(
                'customer_email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Customer email'
            )
            ->addColumn(
                'customer_plan',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Customer Plan'
            )
            ->addColumn(
                'price',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Price'
            )
            ->addColumn(
                'customer_past_group_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Customer Past Group Id'
            )
            ->addColumn(
                'related_customer_group_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Related Customer Group Id'
            )
            ->addColumn(
                'current_customer_group_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Current customer group id'
            )
            ->addColumn(
                'plan_expiry_status',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Customer plan expiry status'
            )
            ->addColumn(
                'plan_expiry_date',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Plan expiry date'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
            ->setComment('Magedelight Membership Orders');
            
            $installer->getConnection()->createTable($magedelight_membership_orders);
        }

        $installer->endSetup();
    }
}
