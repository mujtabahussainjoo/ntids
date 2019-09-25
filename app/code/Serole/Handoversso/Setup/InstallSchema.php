<?php

namespace Serole\Handoversso\Setup;

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

		$installer->run('CREATE TABLE IF NOT EXISTS `handoversso_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT \'Token ID\',
  `token` varchar(255) NOT NULL COMMENT \'Token\',
  `email` varchar(255) DEFAULT NULL COMMENT \'Email\',
  `firstname` varchar(255) DEFAULT NULL COMMENT \'First name\',
  `lastname` varchar(255) DEFAULT NULL COMMENT \'Last name\',
  `ssoid` varchar(255) DEFAULT NULL COMMENT \'Single Signon ID\',
  `dob` varchar(10) DEFAULT NULL COMMENT \'DOB\',
  `status` smallint(6) DEFAULT NULL COMMENT \'Enabled\',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT \'Token Modification Time\',
  `created_at` timestamp NULL DEFAULT NULL COMMENT \'Token Creation Time\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT=\'Token Table\' AUTO_INCREMENT=1');
$installer->run('CREATE TABLE IF NOT EXISTS `signature` (
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `username` varchar(100) DEFAULT NULL,
	  `vendor_timestamp` varchar(100) DEFAULT NULL,
	  `signature` varchar(100) DEFAULT NULL,
	  `request_time` datetime DEFAULT NULL,
	  `email_id` varchar(100) DEFAULT NULL,
	  `created_at` datetime DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');


		//demo 
//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//$scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
//demo 

		}

        $installer->endSetup();

    }
}