<?php

namespace Serole\Racvportal\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.0') < 0){
            $installer->run('CREATE TABLE IF NOT EXISTS `portal_shop` (
                              `entity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT \'Shop ID\',
                              `name` varchar(255) NOT NULL COMMENT \'Shop Name\',
                              `street` varchar(255) NOT NULL COMMENT \'Street\',
                              `suburb` varchar(255) NOT NULL COMMENT \'Suburb\',
                              `region` int(11) NOT NULL COMMENT \'State\',
                              `postcode` int(10) unsigned NOT NULL COMMENT \'Postcode\',
                              `phone` varchar(255) NOT NULL COMMENT \'Phone Number\',
                              `status` smallint(6) DEFAULT NULL COMMENT \'Enabled\',
                              `updated_at` timestamp NULL DEFAULT NULL COMMENT \'Shop Modification Time\',
                              `created_at` timestamp NULL DEFAULT NULL COMMENT \'Shop Creation Time\',                              
                              `store_id` int(4) NOT NULL COMMENT \'Store Id\',
                              PRIMARY KEY (`entity_id`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT=\'Shop Table\' AUTO_INCREMENT=24');

            $installer->run('CREATE TABLE IF NOT EXISTS `racvportal` (
                                  `Id` int(11) NOT NULL AUTO_INCREMENT,
                                  `increment_id` varchar(250) DEFAULT NULL,
                                  `shop_id` varchar(250) DEFAULT NULL,
                                  `shop_name` varchar(250) DEFAULT NULL,
                                  PRIMARY KEY (`Id`)
                                ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18659 ');


		}
        $installer->endSetup();
    }
}