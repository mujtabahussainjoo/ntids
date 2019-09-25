<?php

namespace Serole\SubsidyOrderattributes\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $orderTable = 'sales_order_item';

        $setup->getConnection()->addColumn(
                $setup->getTable($orderTable),
                'subsidy',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' =>'subsidy'
                ]
            );
        $setup->getConnection()->addColumn(
               $setup->getTable($orderTable),
               'subsidy_vip',
               [
                   'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   'length' => 255,
                   'comment' =>'subsidy_vip'
               ]
           );
        $setup->getConnection()->addColumn(
            $setup->getTable($orderTable),
               'member_profit',
               [
                   'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   'length' => 255,
                   'comment' =>'member_profit'
               ]
           );

        $setup->endSetup();
    }
}