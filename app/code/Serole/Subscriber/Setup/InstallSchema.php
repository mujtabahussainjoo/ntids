<?php

namespace Serole\Subscriber\Setup;

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

		$installer->run('create table subscriber(
  id int(11) NOT NULL AUTO_INCREMENT,
  customer_id varchar(100) DEFAULT NULL,
  customer_email varchar(100) DEFAULT NULL,
  customer_member varchar(100) DEFAULT NULL,
  customer_first_name varchar(100) DEFAULT NULL,
  customer_last_name varchar(100) DEFAULT NULL,
  customer_phno varchar(100) DEFAULT NULL,
  customer_address varchar(100) DEFAULT NULL,
  customer_postcode varchar(100) DEFAULT NULL,
  customer_state varchar(100) DEFAULT NULL,
  suburb varchar(100) DEFAULT NULL,
  order_id varchar(100) DEFAULT NULL,
  export varchar(100) DEFAULT NULL,
  opt_in varchar(100) DEFAULT NULL,
  store_id varchar(100) DEFAULT NULL,
  created_at datetime DEFAULT  CURRENT_TIMESTAMP,
  updted_at datetime DEFAULT NULL,
  PRIMARY KEY (id)
)');


		//demo 
//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//$scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
//$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/updaterates.log');
//$logger = new \Zend\Log\Logger();
//$logger->addWriter($writer);
//$logger->info('updaterates');
//demo 

		}

        $installer->endSetup();

    }
}