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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $installer->getConnection()
                ->addColumn($installer->getTable('mst_kb_category'), 'display_mode', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => 255,
                    'default'  => '',
                    'comment'  => 'Display mode',
                ]);
        }
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $installer->getConnection()
                ->modifyColumn($installer->getTable('mst_kb_article'), 'created_at', [
                    'type'     => Table::TYPE_TIMESTAMP,
                    'unsigned' => false,
                    'nullable' => false,
                    'default'  => Table::TIMESTAMP_INIT,
                ]);
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $installer->getConnection()
                ->addColumn($installer->getTable('mst_kb_category'), 'description', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '64K',
                    'default'  => '',
                    'comment'  => 'Description',
                ]);

            $installer->getConnection()
                ->addColumn($installer->getTable('mst_kb_category'), 'custom_layout_update', [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length'   => '64K',
                    'default'  => '',
                    'comment'  => 'Custom Layout Update',
                ]);
        }

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            include_once 'Upgrade_1_0_4.php';

            Upgrade_1_0_4::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            include_once 'Upgrade_1_0_5.php';

            Upgrade_1_0_5::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.6') < 0) {
            include_once 'Upgrade_1_0_6.php';

            Upgrade_1_0_6::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.7') < 0) {
            include_once 'Upgrade_1_0_7.php';

            Upgrade_1_0_7::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.8') < 0) {
            include_once 'Upgrade_1_0_8.php';

            Upgrade_1_0_8::upgrade($installer, $context);
        }
    }
}
