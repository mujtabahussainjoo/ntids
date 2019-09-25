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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Helpdesk\Setup;

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
            $installer->getConnection()->dropForeignKey(
                $installer->getTable('mst_helpdesk_permission'),
                $installer->getFkName(
                    'mst_helpdesk_permission',
                    'role_id',
                    'admin_role',
                    'role_id'
                )
            );

            $installer->getConnection()->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_permission',
                    'role_id',
                    'authorization_role',
                    'role_id'
                ),
                $installer->getTable('mst_helpdesk_permission'),
                'role_id',
                $installer->getTable('authorization_role'),
                'role_id',
                Table::ACTION_CASCADE
            );
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('mst_helpdesk_rule'),
                'is_archive',
                'tinyint'
            );
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            include_once 'Upgrade_1_0_3.php';

            Upgrade_1_0_3::upgrade($installer, $context);
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

        if (version_compare($context->getVersion(), '1.0.9') < 0) {
            include_once 'Upgrade_1_0_9.php';

            Upgrade_1_0_9::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.10') < 0) {
            include_once 'Upgrade_1_0_10.php';

            Upgrade_1_0_10::upgrade($installer, $context);
        }
    }
}
