<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

trait TableTrait
{
    /**
     * Create Table 'amasty_amrolepermissions_rule_product'
     * This method is called from UpgradeSchema is module has installed or moduleVersion < 1.2
     *
     * @param SchemaSetupInterface $installer
     */
    private function createRuleProductTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrolepermissions_rule_product'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule Id'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Catalog Product ID'
            )
            ->addIndex(
                $installer->getIdxName('amasty_amrolepermissions_rule_product', ['rule_id']),
                ['rule_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrolepermissions_rule_product',
                    'rule_id',
                    'amasty_amrolepermissions_rule',
                    'id'
                ),
                'rule_id',
                $installer->getTable('amasty_amrolepermissions_rule'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty Advanced Permissions Rule Product Relation Table');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create Table 'amasty_amrolepermissions_rule_category'
     * This method is called from UpgradeSchema is module has installed or moduleVersion < 1.2
     *
     * @param SchemaSetupInterface $installer
     */
    private function createRuleCategoryTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrolepermissions_rule_category'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule Id'
            )
            ->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Catalog Category ID'
            )
            ->addIndex(
                $installer->getIdxName('amasty_amrolepermissions_rule_category', ['rule_id']),
                ['rule_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrolepermissions_rule_category',
                    'rule_id',
                    'amasty_amrolepermissions_rule',
                    'id'
                ),
                'rule_id',
                $installer->getTable('amasty_amrolepermissions_rule'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty Advanced Permissions Rule Category Relation Table');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create Table 'amasty_amrolepermissions_rule_website'
     * This method is called from UpgradeSchema is module has installed or moduleVersion < 1.2
     *
     * @param SchemaSetupInterface $installer
     */
    private function createRuleWebsiteTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrolepermissions_rule_website'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule Id'
            )
            ->addColumn(
                'website_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website ID'
            )
            ->addIndex(
                $installer->getIdxName('amasty_amrolepermissions_rule_website', ['rule_id']),
                ['rule_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrolepermissions_rule_website',
                    'rule_id',
                    'amasty_amrolepermissions_rule',
                    'id'
                ),
                'rule_id',
                $installer->getTable('amasty_amrolepermissions_rule'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty Advanced Permissions Rule Website Relation Table');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create Table 'amasty_amrolepermissions_rule_storeview'
     * This method is called from UpgradeSchema is module has installed or moduleVersion < 1.2
     *
     * @param SchemaSetupInterface $installer
     */
    private function createRuleStoreviewTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrolepermissions_rule_storeview'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule Id'
            )
            ->addColumn(
                'storeview_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store View ID'
            )
            ->addIndex(
                $installer->getIdxName('amasty_amrolepermissions_rule_storeview', ['rule_id']),
                ['rule_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrolepermissions_rule_storeview',
                    'rule_id',
                    'amasty_amrolepermissions_rule',
                    'id'
                ),
                'rule_id',
                $installer->getTable('amasty_amrolepermissions_rule'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty Advanced Permissions Rule Storeview Relation Table');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create Table 'amasty_amrolepermissions_rule_attribute'
     * This method is called from UpgradeSchema is module has installed or moduleVersion < 1.2
     *
     * @param SchemaSetupInterface $installer
     */
    private function createRuleAttributeTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrolepermissions_rule_attribute'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule Id'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website ID'
            )
            ->addIndex(
                $installer->getIdxName('amasty_amrolepermissions_rule_attribute', ['rule_id']),
                ['rule_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrolepermissions_rule_attribute',
                    'rule_id',
                    'amasty_amrolepermissions_rule',
                    'id'
                ),
                'rule_id',
                $installer->getTable('amasty_amrolepermissions_rule'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty Advanced Permissions Rule Attribute Relation Table');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create Table 'amasty_amrolepermissions_rule_role'
     * This method is called from UpgradeSchema is module has installed or moduleVersion < 1.2
     *
     * @param SchemaSetupInterface $installer
     */
    private function createRuleRoleTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_amrolepermissions_rule_role'))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule Id'
            )
            ->addColumn(
                'role_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Allowed Admin Role ID'
            )
            ->addIndex(
                $installer->getIdxName('amasty_amrolepermissions_rule_role', ['rule_id']),
                ['rule_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_amrolepermissions_rule_role',
                    'rule_id',
                    'amasty_amrolepermissions_rule',
                    'id'
                ),
                'rule_id',
                $installer->getTable('amasty_amrolepermissions_rule'),
                'id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty Advanced Permissions Rule Role Relation Table');

        $installer->getConnection()->createTable($table);
    }
}
