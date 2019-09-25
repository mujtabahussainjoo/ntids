<?php

namespace Serole\Digitalglue\Setup;

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

		$installer->run('CREATE TABLE IF NOT EXISTS `digitalglue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referencenumber` varchar(30) NOT NULL,
  `sku` varchar(30) NOT NULL,
  `magento_sku` varchar(50) NOT NULL,
  `quantity` int(3) NOT NULL,
  `statuscode` varchar(10) NOT NULL,
  `statusmessage` varchar(100) NOT NULL,
  `receiptnumber` varchar(50) NOT NULL,
  `amount` varchar(10) NOT NULL,
  `redemptionurl` text NOT NULL,
  `expirydate` varchar(30) NOT NULL,
  `costperunit` varchar(10) NOT NULL,
  `rrpperunit` varchar(10) NOT NULL,
  `includesgst` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1');


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