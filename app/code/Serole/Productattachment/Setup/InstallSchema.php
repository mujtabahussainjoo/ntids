<?php

namespace Serole\Productattachment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0){

		$installer->run('CREATE TABLE IF NOT EXISTS `serole_productattachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'Entity Id\',
  `name` varchar(255) DEFAULT NULL COMMENT \'Name\',
  `description` varchar(255) DEFAULT NULL COMMENT \'Description\',
  `file` varchar(255) DEFAULT NULL COMMENT \'Content\',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'Created At\',
  `modified_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'Modified date\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT=\'Productattach item\' AUTO_INCREMENT=1');


		

		}

        $installer->endSetup();

    }
}