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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class Upgrade_1_0_4 implements UpgradeInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function upgrade(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_kb_article_customer_group')
        )
            ->addColumn(
                'article_cg_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Article Customer Group Id'
            )
            ->addColumn(
                'acg_article_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Article Id'
            )
            ->addColumn(
                'acg_group_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Group Id'
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_article_customer_group', ['acg_article_id']),
                ['acg_article_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_kb_article_customer_group', ['acg_group_id']),
                ['acg_group_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_kb_article_category',
                    'acg_article_id',
                    'mst_kb_article',
                    'article_id'
                ),
                'acg_article_id',
                $installer->getTable('mst_kb_article'),
                'article_id',
                Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }
}