<?php

namespace Serole\Sage\Setup;

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

		$installer->run('CREATE TABLE IF NOT EXISTS `sage_integration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` varchar(30) DEFAULT \'0\',
  `sales_header` tinyint(1) NOT NULL DEFAULT \'0\',
  `sales_details` tinyint(1) NOT NULL DEFAULT \'0\',
  `sales_serialcode` tinyint(1) NOT NULL DEFAULT \'0\',
  `payment_receipt` tinyint(1) NOT NULL DEFAULT \'0\',
  `credit_memo_header` tinyint(1) NOT NULL DEFAULT \'0\',
  `credit_memo_details` tinyint(1) NOT NULL DEFAULT \'0\',
  `credit_memo_serialcode` tinyint(1) NOT NULL DEFAULT \'0\',
  `credit_memo_receipt` tinyint(1) NOT NULL DEFAULT \'0\',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1');


		//demo 
//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//$scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
//demo 

		}

        $installer->endSetup();

    }
}