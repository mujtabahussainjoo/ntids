<?php

   namespace Serole\Adminhtml\Setup;

   use Magento\Framework\Setup\ModuleContextInterface;
   use Magento\Framework\Setup\SchemaSetupInterface;
   use Magento\Framework\Setup\UpgradeSchemaInterface;

   class UpgradeSchema implements UpgradeSchemaInterface{

       public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){
           if (version_compare($context->getVersion(), '2.0.1') < 0) {
               $setup->startSetup();
               $setup->getConnection()->addColumn(
               $setup->getTable('admin_user'),
                   'website_id',
                   ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                       'length' => '11',
                       'nullable' => false,
                       'default' => '0',
                       'comment' => 'WebsiteId'
                   ]);
               $setup->endSetup();
           }
       }
   }