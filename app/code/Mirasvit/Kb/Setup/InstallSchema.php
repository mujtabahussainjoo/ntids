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
 * @package   mirasvit/module-kb
 * @version   1.0.49
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Setup;

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
     * {@inheritdoc}
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_kb_article')
        )
            ->addColumn(
                'article_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Article Id'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Name'
            )
            ->addColumn(
                'text',
                Table::TYPE_TEXT,
                '64K',
                ['unsigned' => false, 'nullable' => true],
                'Text'
            )
            ->addColumn(
                'url_key',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Url Key'
            )
            ->addColumn(
                'meta_title',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Meta Title'
            )
            ->addColumn(
                'meta_keywords',
                Table::TYPE_TEXT,
                '64K',
                ['unsigned' => false, 'nullable' => true],
                'Meta Keywords'
            )
            ->addColumn(
                'meta_description',
                Table::TYPE_TEXT,
                '64K',
                ['unsigned' => false, 'nullable' => true],
                'Meta Description'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'default' => 0],
                'Is Active'
            )
            ->addColumn(
                'user_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'User Id'
            )
            ->addColumn(
                'votes_sum',
                Table::TYPE_FLOAT,
                null,
                ['unsigned' => false, 'nullable' => true],
                'Votes Sum'
            )
            ->addColumn(
                'votes_num',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => true],
                'Votes Num'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Updated At'
            )
            ->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Position'
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_article', ['user_id']),
                ['user_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_kb_article',
                    'user_id',
                    'admin_user',
                    'user_id'
                ),
                'user_id',
                $installer->getTable('admin_user'),
                'user_id',
                Table::ACTION_SET_NULL
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_kb_article_category')
        )
            ->addColumn(
                'article_category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Article Category Id'
            )
            ->addColumn(
                'ac_article_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Ac Article Id'
            )
            ->addColumn(
                'ac_category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Ac Category Id'
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_article_category', ['ac_article_id']),
                ['ac_article_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_article_category', ['ac_category_id']),
                ['ac_category_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_kb_article_category',
                    'ac_category_id',
                    'mst_kb_category',
                    'category_id'
                ),
                'ac_category_id',
                $installer->getTable('mst_kb_category'),
                'category_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_kb_article_category',
                    'ac_article_id',
                    'mst_kb_article',
                    'article_id'
                ),
                'ac_article_id',
                $installer->getTable('mst_kb_article'),
                'article_id',
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_kb_article_store')
        )
            ->addColumn(
                'article_store_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Article Store Id'
            )
            ->addColumn(
                'as_article_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'As Article Id'
            )
            ->addColumn(
                'as_store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'As Store Id'
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_article_store', ['as_article_id']),
                ['as_article_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_article_store', ['as_store_id']),
                ['as_store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_kb_article_store',
                    'as_store_id',
                    'store',
                    'store_id'
                ),
                'as_store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_kb_article_store',
                    'as_article_id',
                    'mst_kb_article',
                    'article_id'
                ),
                'as_article_id',
                $installer->getTable('mst_kb_article'),
                'article_id',
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_kb_article_tag')
        )
            ->addColumn(
                'article_tag_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Article Tag Id'
            )
            ->addColumn(
                'at_article_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'At Article Id'
            )
            ->addColumn(
                'at_tag_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'At Tag Id'
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_article_tag', ['at_article_id']),
                ['at_article_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_article_tag', ['at_tag_id']),
                ['at_tag_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_kb_article_tag',
                    'at_article_id',
                    'mst_kb_article',
                    'article_id'
                ),
                'at_article_id',
                $installer->getTable('mst_kb_article'),
                'article_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_kb_article_tag',
                    'at_tag_id',
                    'mst_kb_tag',
                    'tag_id'
                ),
                'at_tag_id',
                $installer->getTable('mst_kb_tag'),
                'tag_id',
                Table::ACTION_CASCADE
            )
            ->addIndex(
                $installer->getIdxName(
                    'mst_kb_article_tag',
                    ['at_article_id', 'at_tag_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['at_article_id', 'at_tag_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_kb_attachment')
        )
            ->addColumn(
                'attachment_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Attachment Id'
            )
            ->addColumn(
                'article_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Article Id'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Name'
            )
            ->addColumn(
                'type',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Type'
            )
            ->addColumn(
                'size',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => true],
                'Size'
            )
            ->addColumn(
                'body',
                Table::TYPE_BLOB,
                '4G',
                ['unsigned' => false, 'nullable' => true],
                'Body'
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_attachment', ['article_id']),
                ['article_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_kb_attachment',
                    'article_id',
                    'mst_kb_article',
                    'article_id'
                ),
                'article_id',
                $installer->getTable('mst_kb_article'),
                'article_id',
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_kb_category')
        )
            ->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Category Id'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Name'
            )
            ->addColumn(
                'url_key',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Url Key'
            )
            ->addColumn(
                'meta_title',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Meta Title'
            )
            ->addColumn(
                'meta_keywords',
                Table::TYPE_TEXT,
                '64K',
                ['unsigned' => false, 'nullable' => true],
                'Meta Keywords'
            )
            ->addColumn(
                'meta_description',
                Table::TYPE_TEXT,
                '64K',
                ['unsigned' => false, 'nullable' => true],
                'Meta Description'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'default' => 0],
                'Is Active'
            )
            ->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'default' => 0],
                'Sort Order'
            )
            ->addColumn(
                'parent_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => true],
                'Parent Id'
            )
            ->addColumn(
                'path',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Path'
            )
            ->addColumn(
                'level',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => true],
                'Level'
            )
            ->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => true],
                'Position'
            )
            ->addColumn(
                'children_count',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => true],
                'Children Count'
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_category', ['parent_id']),
                ['parent_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_kb_category',
                    'parent_id',
                    'mst_kb_category',
                    'category_id'
                ),
                'parent_id',
                $installer->getTable('mst_kb_category'),
                'category_id',
                Table::ACTION_SET_NULL
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_kb_category_store')
        )
            ->addColumn(
                'category_store_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Category Store Id'
            )
            ->addColumn(
                'as_category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'As Category Id'
            )
            ->addColumn(
                'as_store_id',
                Table::TYPE_INTEGER,
                5,
                ['unsigned' => true, 'nullable' => false],
                'As Store Id'
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_kb_tag')
        )
            ->addColumn(
                'tag_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Tag Id'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Name'
            )
            ->addColumn(
                'url_key',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => false, 'nullable' => false],
                'Url Key'
            );
        $installer->getConnection()->createTable($table);
    }
}