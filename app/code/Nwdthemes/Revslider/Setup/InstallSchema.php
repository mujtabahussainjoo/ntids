<?php

namespace Nwdthemes\Revslider\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {

        $installer = $setup;
        $installer->startSetup();

        $installer->run("DROP TABLE IF EXISTS `{$installer->getTable('nwdthemes_revslider_backup')}`");
        $installer->run("DROP TABLE IF EXISTS `{$installer->getTable('nwdthemes_revslider_css')}`");
        $installer->run("DROP TABLE IF EXISTS `{$installer->getTable('nwdthemes_revslider_animations')}`");
        $installer->run("DROP TABLE IF EXISTS `{$installer->getTable('nwdthemes_revslider_navigations')}`");
        $installer->run("DROP TABLE IF EXISTS `{$installer->getTable('nwdthemes_revslider_options')}`");
        $installer->run("DROP TABLE IF EXISTS `{$installer->getTable('nwdthemes_revslider_sliders')}`");
        $installer->run("DROP TABLE IF EXISTS `{$installer->getTable('nwdthemes_revslider_slides')}`");
        $installer->run("DROP TABLE IF EXISTS `{$installer->getTable('nwdthemes_revslider_static_slides')}`");

        $installer->run("CREATE TABLE `{$installer->getTable('nwdthemes_revslider_backup')}` (
          `id` int(9) NOT NULL AUTO_INCREMENT,
          `slide_id` int(9) NOT NULL,
          `slider_id` int(9) NOT NULL,
          `slide_order` int(11) NOT NULL,
          `params` longtext NOT NULL,
          `layers` longtext NOT NULL,
          `settings` text NOT NULL,
          `created` datetime NOT NULL,
          `session` varchar(100) NOT NULL,
          `static` varchar(20) NOT NULL,
          UNIQUE KEY `id` (`id`),
          KEY `slide_id` (`slide_id`),
          KEY `slider_id` (`slider_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $installer->run("CREATE TABLE `{$installer->getTable('nwdthemes_revslider_css')}` (
          `id` int(9) NOT NULL AUTO_INCREMENT,
          `handle` text NOT NULL,
          `settings` longtext,
          `hover` longtext,
          `params` longtext NOT NULL,
          `advanced` longtext,
          UNIQUE KEY `id` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $installer->run("CREATE TABLE `{$installer->getTable('nwdthemes_revslider_animations')}` (
          `id` int(9) NOT NULL AUTO_INCREMENT,
          `handle` text NOT NULL,
          `params` text NOT NULL,
          `settings` text,
          UNIQUE KEY `id` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $installer->run("CREATE TABLE `{$installer->getTable('nwdthemes_revslider_navigations')}` (
          `id` int(9) NOT NULL AUTO_INCREMENT,
          `name` varchar(191) NOT NULL,
          `handle` varchar(191) NOT NULL,
          `css` longtext NOT NULL,
          `markup` longtext NOT NULL,
          `settings` longtext,
          UNIQUE KEY `id` (`id`),
          KEY `handle` (`handle`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $installer->run("CREATE TABLE `{$installer->getTable('nwdthemes_revslider_options')}` (
          `id` int(9) NOT NULL AUTO_INCREMENT,
          `handle` varchar(100) NOT NULL,
          `option` longtext NOT NULL,
          PRIMARY KEY (`id`),
          KEY `handle` (`handle`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $installer->run("CREATE TABLE `{$installer->getTable('nwdthemes_revslider_sliders')}` (
          `id` int(9) NOT NULL AUTO_INCREMENT,
          `title` varchar(255) NOT NULL,
          `alias` varchar(255),
          `params` longtext NOT NULL,
          `settings` text NULL DEFAULT '',
          `type` varchar(191) NOT NULL DEFAULT '',
          UNIQUE KEY `id` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $installer->run("CREATE TABLE `{$installer->getTable('nwdthemes_revslider_slides')}` (
          `id` int(9) NOT NULL AUTO_INCREMENT,
          `slider_id` int(9) NOT NULL,
          `slide_order` int(11) NOT NULL,
          `params` longtext NOT NULL,
          `layers` longtext NOT NULL,
          `settings` text NOT NULL,
          UNIQUE KEY `id` (`id`),
          KEY `slider_id` (`slider_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $installer->run("CREATE TABLE `{$installer->getTable('nwdthemes_revslider_static_slides')}` (
          `id` int(9) NOT NULL AUTO_INCREMENT,
          `slider_id` int(9) NOT NULL,
          `params` longtext NOT NULL,
          `layers` longtext NOT NULL,
          `settings` text NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `id` (`id`),
          KEY `slider_id` (`slider_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $installer->endSetup();
    }

}
