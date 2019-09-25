<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Rbslider\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'aw_rbslider_banner'
         */
        $bannerTable = $installer->getConnection()->newTable($installer->getTable('aw_rbslider_banner'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'page_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Type'
            )
            ->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Position'
            )
            ->addColumn(
                'product_condition',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Product Condition'
            )
            ->addColumn(
                'category_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Category IDs'
            )
            ->addColumn(
                'animation_effect',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Animation Effect'
            )
            ->addColumn(
                'pause_time_between_transitions',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Pause Time Between Transitions'
            )
            ->addColumn(
                'slide_transition_speed',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Slide Transition Speed'
            )
            ->addColumn(
                'is_stop_animation_mouse_on_banner',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Stop Animation While Mouse is on the Banner'
            )
            ->addColumn(
                'display_arrows',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Display Arrows'
            )
            ->addColumn(
                'display_bullets',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Display Navigation Bullets'
            )
            ->addColumn(
                'is_random_order_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Display Images in Random Order'
            );
        $installer->getConnection()->createTable($bannerTable);

        /**
         * Create table 'aw_rbslider_slide'
         */
        $slideTable = $installer->getConnection()->newTable($installer->getTable('aw_rbslider_slide'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'display_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Display From'
            )
            ->addColumn(
                'display_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Display To'
            )
            ->addColumn(
                'img_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Image Type'
            )
            ->addColumn(
                'img_file',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Image File'
            )
            ->addColumn(
                'img_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Image to URL'
            )
            ->addColumn(
                'img_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Image Title'
            )
            ->addColumn(
                'img_alt',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Image Alt'
            )
            ->addColumn(
                'url',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'URL'
            )
            ->addColumn(
                'is_open_url_in_new_window',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Open URL in New Window'
            )
            ->addColumn(
                'is_add_nofollow_to_url',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Add No follow to URL'
            );
        $installer->getConnection()->createTable($slideTable);

        /**
         * Create table 'aw_rbslider_slide_store'
         */
        $storeTable = $installer->getConnection()
            ->newTable(
                $installer->getTable('aw_rbslider_slide_store')
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
                $installer->getIdxName('aw_rbslider_slide_store', ['slide_id']),
                ['slide_id']
            )->addIndex(
                $installer->getIdxName('aw_rbslider_slide_store', ['slide_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('aw_rbslider_slide_store', 'slide_id', 'aw_rbslider_slide', 'id'),
                'slide_id',
                $installer->getTable('aw_rbslider_slide'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('aw_rbslider_slide_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'AW Rbslider Slide To Store Relation Table'
            );
        $installer->getConnection()->createTable($storeTable);

        /**
         * Create table 'aw_rbslider_slide_customer_group'
         */
        $customerGroupTable = $installer->getConnection()
            ->newTable(
                $installer->getTable('aw_rbslider_slide_customer_group')
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
                $installer->getIdxName('aw_rbslider_slide_customer_group', ['slide_id']),
                ['slide_id']
            )->addIndex(
                $installer->getIdxName('aw_rbslider_slide_customer_group', ['customer_group_id']),
                ['customer_group_id']
            )->addForeignKey(
                $installer->getFkName('aw_rbslider_slide_customer_group', 'slide_id', 'aw_rbslider_slide', 'id'),
                'slide_id',
                $installer->getTable('aw_rbslider_slide'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'AW Rbslider Slide To Customer Group Relation Table'
            );
        $installer->getConnection()->createTable($customerGroupTable);

        /**
         * Create table 'aw_rbslider_slide_banner'
         */
        $slideBannerTable = $installer->getConnection()->newTable($installer->getTable('aw_rbslider_slide_banner'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )->addColumn(
                'slide_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Slide ID'
            )->addColumn(
                'banner_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Banner ID'
            )->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => 0],
                'Slide Position'
            )->addForeignKey(
                $installer->getFkName('aw_rbslider_slide_banner', 'slide_id', 'aw_rbslider_slide', 'id'),
                'slide_id',
                $installer->getTable('aw_rbslider_slide'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('aw_rbslider_slide_banner', 'banner_id', 'aw_rbslider_banner', 'id'),
                'banner_id',
                $installer->getTable('aw_rbslider_banner'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Slide To Banner Linkage Table'
            );
        $installer->getConnection()->createTable($slideBannerTable);

        /**
         * Create table 'aw_rbslider_statistic'
         */
        $statisticTable = $installer->getConnection()->newTable($installer->getTable('aw_rbslider_statistic'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'slide_banner_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Slide Banner ID'
            )
            ->addColumn(
                'view_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'View Count'
            )
            ->addColumn(
                'click_count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Click Count'
            )->addForeignKey(
                $installer->getFkName('aw_rbslider_statistic', 'slide_banner_id', 'aw_rbslider_slide_banner', 'id'),
                'slide_banner_id',
                $installer->getTable('aw_rbslider_slide_banner'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($statisticTable);

        $installer->endSetup();
    }
}
