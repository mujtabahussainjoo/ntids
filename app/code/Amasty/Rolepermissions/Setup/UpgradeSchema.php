<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Setup;

use Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Scope;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    use TableTrait;

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (!$context->getVersion() || version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->separateTables($setup);
            $this->addModeColumns($setup);

            if ($context->getVersion()) {
                $this->moveOldData($setup, $context);
            }
        }

        $setup->endSetup();
    }

    private function addModeColumns(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amrolepermissions_rule'),
            'product_access_mode',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => \Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Products::MODE_ANY,
                'comment' => 'Product Access Mode'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amrolepermissions_rule'),
            'category_access_mode',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => \Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Categories::MODE_ALL,
                'comment' => 'Category Access Mode'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amrolepermissions_rule'),
            'scope_access_mode',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => Scope::MODE_NONE,
                'comment' => 'Website Storeview Scope Access Mode'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amrolepermissions_rule'),
            'attribute_access_mode',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => \Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Attributes::MODE_ANY,
                'comment' => 'Attribute Access Mode'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amrolepermissions_rule'),
            'role_access_mode',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => \Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Roles::MODE_ANY,
                'comment' => 'Admin Role Access Mode'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function separateTables(SchemaSetupInterface $setup)
    {
        $this->createRuleProductTable($setup);
        $this->createRuleCategoryTable($setup);
        $this->createRuleWebsiteTable($setup);
        $this->createRuleStoreviewTable($setup);
        $this->createRuleAttributeTable($setup);
        $this->createRuleRoleTable($setup);
    }

    private function moveOldData(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();
        $columns = [
            'id',
            'products',
            'categories',
            'scope_websites',
            'scope_storeviews',
        ];
        $processingArray = [
            'products' => [
                'table' => 'amasty_amrolepermissions_rule_product',
                'tableColumn' => 'product_id',
                'accessColumn' => 'product_access_mode',
                'insert' => []
            ],
            'categories' => [
                'table' => 'amasty_amrolepermissions_rule_category',
                'tableColumn' => 'category_id',
                'accessColumn' => 'category_access_mode',
                'insert' => []
            ],
            'scope_websites' => [
                'table' => 'amasty_amrolepermissions_rule_website',
                'tableColumn' => 'website_id',
                'accessColumn' => 'scope_access_mode',
                'insert' => []
            ],
            'scope_storeviews' => [
                'table' => 'amasty_amrolepermissions_rule_storeview',
                'tableColumn' => 'storeview_id',
                'accessColumn' => 'scope_access_mode',
                'insert' => []
            ]
        ];
        // column 'attributes' was added in version 1.1.0
        if (version_compare($context->getVersion(), '1.1.0', '>=')) {
            $columns[] = 'attributes';
            $processingArray['attributes'] = [
                'table' => 'amasty_amrolepermissions_rule_attribute',
                'tableColumn' => 'attribute_id',
                'accessColumn' => 'attribute_access_mode',
                'insert' => []
            ];
        }

        $relationsSelect = $connection->select()->from(
            $setup->getTable('amasty_amrolepermissions_rule'),
            $columns
        );

        $ruleRelationsDataSet = $connection->fetchAll($relationsSelect);

        $ruleUpdate = [];

        foreach ($ruleRelationsDataSet as $ruleData) {
            $ruleId = $ruleData['id'];
            $ruleUpdate[$ruleId] = [];
            foreach ($processingArray as $column => &$config) {
                $columnData = $ruleData[$column];
                $modeColumn = $config['accessColumn'];
                if ($columnData) {
                    switch ($column) {
                        case 'scope_websites':
                            $ruleUpdate[$ruleId][$modeColumn] = Scope::MODE_SITE;
                            break;
                        case 'scope_storeviews':
                            $ruleUpdate[$ruleId][$modeColumn] = Scope::MODE_VIEW;
                            break;
                        default:
                            $ruleUpdate[$ruleId][$modeColumn] = 1;
                    }
                    $ids = explode(',', $columnData);
                    foreach ($ids as $id) {
                        if ($id) {
                            $config['insert'][] = ['rule_id' => $ruleId, $config['tableColumn'] => $id];
                        }
                    }
                } elseif (!isset($ruleUpdate[$ruleId][$modeColumn])) {
                    if ($columnData === '') {
                        $ruleUpdate[$ruleId][$modeColumn] = 0;
                    } elseif ($columnData == 0) {
                        $ruleUpdate[$ruleId][$modeColumn] = 2;
                    }
                }
            }
        }

        foreach ($processingArray as &$config) {
            if (!empty($config['insert'])) {
                $connection->insertMultiple($setup->getTable($config['table']), $config['insert']);
                unset($config['insert']);
            }
        }

        foreach ($ruleUpdate as $id => $updateRow) {
            $connection->update($setup->getTable('amasty_amrolepermissions_rule'), $updateRow, 'id = ' . $id);
        }

        $mainRoleRuleTable = $setup->getTable('amasty_amrolepermissions_rule');
        foreach (array_keys($processingArray) as $column) {
            $connection->dropColumn($mainRoleRuleTable, $column);
        }
    }
}
