<?php

namespace Nwdthemes\Revslider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {

        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '5.4.5') < 0) {

            $indexesToAdd = array(
                'nwdthemes_revslider_backup' => array('slide_id', 'slider_id'),
                'nwdthemes_revslider_navigations' => array('handle'),
                'nwdthemes_revslider_options' => array('handle'),
                'nwdthemes_revslider_slides' => array('slider_id'),
                'nwdthemes_revslider_static_slides' => array('slider_id')
            );

            foreach ($indexesToAdd as $tableName => $indexNames) {
                $table = $setup->getTable($tableName);
                $indexesList = $connection->getIndexList($table);
                foreach ($indexNames as $indexName) {
                    if ( ! isset($indexesList[strtoupper($indexName)])) {
                        $connection->addIndex(
                            $table,
                            $indexName,
                            $indexName
                        );
                    }
                }
            }
        }

        if (version_compare($context->getVersion(), '5.4.6.2') < 0) {

            $setup->run("ALTER TABLE `{$setup->getTable('nwdthemes_revslider_css')}`
                            CHANGE `params` `params` longtext NOT NULL AFTER `hover`;");

            $setup->run("ALTER TABLE `{$setup->getTable('nwdthemes_revslider_navigations')}`
                            CHANGE `css` `css` longtext NOT NULL AFTER `handle`,
                            CHANGE `markup` `markup` longtext NOT NULL AFTER `css`,
                            CHANGE `settings` `settings` longtext NULL AFTER `markup`;");

            $setup->run("ALTER TABLE `{$setup->getTable('nwdthemes_revslider_sliders')}`
                            CHANGE `settings` `settings` text NULL DEFAULT '' AFTER `params`;");

        }

        if (version_compare($context->getVersion(), '5.4.8.3') < 0) {

            $setup->run("ALTER TABLE `{$setup->getTable('nwdthemes_revslider_sliders')}`
                            CHANGE `title` `title` varchar(255) NOT NULL AFTER `id`,
                            CHANGE `alias` `alias` varchar(255) NULL AFTER `title`;");

        }

        $setup->endSetup();
    }

}
