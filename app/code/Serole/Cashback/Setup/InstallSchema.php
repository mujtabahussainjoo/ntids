<?php

namespace Serole\Cashback\Setup;

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

		$installer->run('CREATE TABLE IF NOT EXISTS customer_card_detail (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `customer_id` varchar(255) DEFAULT NULL,
			  `card_type` varchar(255) DEFAULT NULL,
			  `owner_name` varchar(255) DEFAULT NULL,
			  `card_no` varchar(255) DEFAULT NULL,
			  `cvv_no` varchar(10) DEFAULT NULL,
			  `issuing_bank` varchar(50) DEFAULT NULL,
			  `verified` int(1) DEFAULT 0,
              `status` int(1) DEFAULT 1,
			  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1');
$installer->run('CREATE TABLE IF NOT EXISTS customer_order_detail (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `customer_id` varchar(255) DEFAULT NULL,
			  `merchant_id` varchar(255) DEFAULT NULL,
			  `card_id` int(11) DEFAULT NULL,
			  `order_id` varchar(255) DEFAULT NULL,
			  `ship_to` varchar(255) DEFAULT NULL,
			  `bill_to` varchar(255) DEFAULT NULL,
			  `products` varchar(255) DEFAULT NULL,
			  `order_total` varchar(10) DEFAULT NULL,
			  `rewards_points` varchar(10) DEFAULT 0,
			  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1');
$installer->run('CREATE TABLE IF NOT EXISTS customer_used_points (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `customer_id` varchar(255) DEFAULT NULL,
			  `order_id` varchar(255) DEFAULT NULL,
			  `order_total` varchar(10) DEFAULT NULL,
			  `used_points` varchar(10) DEFAULT 0,
			  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
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