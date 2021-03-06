<?php

namespace Serole\ExternalProduct\Setup;

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

		$installer->run('CREATE TABLE IF NOT EXISTS referral_link_history (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `customerid` varchar(10) DEFAULT NULL,
						  `productid` varchar(10) DEFAULT NULL,
						  `linkurl` varchar(255) DEFAULT NULL,
						  `store` varchar(50) DEFAULT NULL,
						  `status` varchar(50) DEFAULT NULL,
						  `date_clicked` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1');


		//demo 
//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//$scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
//demo 

		}

        $installer->endSetup();

    }
}