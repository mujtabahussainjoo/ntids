<?php

namespace Serole\OvernightUpload\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\Filesystem\DirectoryList;


class InstallSchema implements InstallSchemaInterface
{

    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

       if(!$installer->getConnection()->isTableExists('overnightupload_partnercode')) {
           $table = $installer->getConnection()->newTable(
               $installer->getTable('overnightupload_partnercode')
           )
               ->addColumn(
                   'entity_id',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   null,
                   ['identity' => true, 'nullable' => false, 'primary' => true],
                   'Partner Code Id'
               )
               ->addColumn(
                   'partnercode',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   255,
                   ['nullable' => false],
                   'Partner Code'
               )
               ->addColumn(
                   'company_name',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   '255',
                   ['nullable' => false],
                   'Company Name'
               )
               ->addColumn(
                   'server_name',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   null,
                   [],
                   'Server Name'
               )
               ->addColumn(
                   'server_port',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   null,
                   [],
                   'Server Port'
               )
               ->addColumn(
                   'server_protocol',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   null,
                   [],
                   'Server Protocol'
               )
               ->addColumn(
                   'server_username',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   null,
                   [],
                   'Server User Name'
               )
               ->addColumn(
                   'server_password',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   null,
                   [],
                   'password'
               )
               ->addColumn(
                   'status',
                   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                   null,
                   [],
                   'Status'
               )
               ->addColumn(
                   'created_at',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                   null,
                   [],
                   'Partnercode Creation Time'
               )
               ->addColumn(
                   'update_time',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                   null,
                   [],
                   'Partnercode Modification Time'
               )
               ->addColumn(
                   'store_id',
                   \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                   null,
                   ['unsigned' => true,],
                   'Store Id'
               )
               ->setComment(
                   'Partner Code Table'
               );

           $installer->getConnection()->createTable($table);
       }

       if(!$installer->getConnection()->isTableExists('provider_partnergroupcode')){
           $groupCodeTable = $installer->getConnection()->newTable(
               $installer->getTable('provider_partnergroupcode')
           )
               ->addColumn(
                   'id',
                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                   null,
                   ['identity' => true, 'nullable' => false, 'primary' => true],
                   'Id'
               )
               ->addColumn(
                   'providerid',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   255,
                   ['nullable' => true],
                   'Provider Id'
               )
               ->addColumn(
                   'patner_groupid',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   '255',
                   ['nullable' => true],
                   'Patner Group Id'
               )
               ->setComment(
                   'Provider Partner Group Link Table'
               );
           $installer->getConnection()->createTable($groupCodeTable);
       }

        if(!$installer->getConnection()->isTableExists('provider_partnergroupcode')){
            $groupCodeTable = $installer->getConnection()->newTable(
                $installer->getTable('provider_partnergroupcode')
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'providerid',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Provider Id'
                )
                ->addColumn(
                    'patner_groupid',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '255',
                    ['nullable' => true],
                    'Patner Group Id'
                )
                ->setComment(
                    'Provider Partner Group Link Table'
                );
            $installer->getConnection()->createTable($groupCodeTable);
        }

        if(!$installer->getConnection()->isTableExists('overnightupload_data')){
            $overnightuploadDataTable = $installer->getConnection()->newTable(
                $installer->getTable('overnightupload_data')
            )
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    10,
                    ['nullable' => true],
                    'Store Id'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Creation Time'
                )
                ->addColumn(
                    'sent_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Sent at'
                )
                ->addColumn(
                    'transaction_header_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'transaction_header_id'
                )
                ->addColumn(
                    'transaction_detail_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['nullable' => true],
                    'transaction_detail_id'
                )
                ->addColumn(
                    'time_stamp',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'time_stamp'
                )
                ->addColumn(
                    'member_reference_number',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'member_reference_number'
                )
                ->addColumn(
                    'member_reference_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'member_reference_type'
                )
                ->addColumn(
                    'unique_item_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'unique_item_id'
                )
                ->addColumn(
                    'unique_item_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'unique_item_type'
                )
                ->addColumn(
                    'item_description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'item_description'
                )
                ->addColumn(
                    'quantity',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,0',
                    ['nullable' => true],
                    'quantity'
                )
                ->addColumn(
                    'item_price',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    ['nullable' => true],
                    'item_price'
                )
                ->addColumn(
                    'discount',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    ['nullable' => true],
                    'discount'
                )
                ->addColumn(
                    'source_location',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'source_location'
                )
                ->addColumn(
                    'sub_category',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'sub_category'
                )
                ->addColumn(
                    'category',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'category'
                )
                ->addColumn(
                    'short_description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'short_description'
                )
                ->addColumn(
                    'filler1',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'filler1'
                )
                ->addColumn(
                    'filler2',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'filler2'
                )
                ->addColumn(
                    'filler3',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'filler3'
                )
                ->addColumn(
                    'filler4',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'filler4'
                )
                ->addColumn(
                    'partner_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'partner_code'
                )
                ->addColumn(
                    'autoclub',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'autoclub'
                )
                ->setComment(
                    'Overnights upload data'
                );
            $installer->getConnection()->createTable($overnightuploadDataTable);
        }
        
        $installer->endSetup();
    }
}
