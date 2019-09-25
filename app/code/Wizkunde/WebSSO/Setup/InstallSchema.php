<?php

namespace Wizkunde\WebSSO\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        /**
         * Create the table for the identity providers
         */
        $table = $setup->getConnection()->newTable($setup->getTable('wizkunde_websso_server'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ], 'ID')
            ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => false,
            ], 'Name')
            ->addColumn('identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => false,
            ], 'Identifier')
            ->addColumn('connection_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => false,
            ], 'Connection Type')
            ->addIndex(
                $setup->getIdxName(
                    $setup->getTable('wizkunde_websso_server'),
                    ['name'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )->setComment(
                'WebSSO Servers'
            );

        $setup->getConnection()->createTable($table);

        /**
         * Create the table for the identity providers
         */
        $table = $setup->getConnection()->newTable($setup->getTable('wizkunde_websso_server_saml2'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ], 'ID')
            ->addColumn('server_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'unsigned' => true,
                'nullable' => false,
            ], 'Server ID')
            ->addColumn('name_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'NameID')
            ->addColumn('name_id_format', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'unsigned' => true,
                'nullable' => false,
            ], 'NameID Format')
            ->addColumn('metadata_url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Metadata URL')
            ->addColumn('is_passive', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'IsPassive')
            ->addColumn('metadata_expiration', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Expiration Time')
            ->addColumn('forceauthn', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'ForceAuthN')
            ->addColumn('crt_data', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Certificate Data')
            ->addColumn('pem_data', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'PEM Data')
            ->addColumn('passphrase', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Passphrase')
            ->addColumn('sso_binding', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'SSO Binding'])
            ->addColumn('slo_binding', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'SLO Binding'])
            ->addColumn('sign_metadata', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                    'length'    => 255,
                    'nullable'  => true,
                    'comment'   => 'Sign SP Metadata'])
            ->addIndex(
                $setup->getIdxName('wizkunde_websso_server_saml2', ['server_id']),
                ['server_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    'wizkunde_websso_server_saml2',
                    'server_id',
                    'wizkunde_websso_server',
                    'id'
                ),
                'server_id',
                $setup->getTable('wizkunde_websso_server'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'WebSSO SAML2 Data'
            );

        $setup->getConnection()->createTable($table);

        /**
         * Create the table for the identity providers
         */
        $table = $setup->getConnection()->newTable($setup->getTable('wizkunde_websso_server_oauth2'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ], 'ID')
            ->addColumn('server_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'unsigned' => true,
                'nullable' => false,
            ], 'Server ID')
            ->addColumn('server_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Server Type')
            ->addColumn('login_url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Login URL')
            ->addColumn('scope_permissions', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Scope Permissions')
            ->addColumn('token_endpoint', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Token Endpoint')
            ->addColumn('userinfo_endpoint', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Userinfo Endpoint')
            ->addColumn('client_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Client ID')
            ->addColumn('client_secret', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Client Secret')
            ->addIndex(
                $setup->getIdxName('wizkunde_websso_server_oauth2', ['server_id']),
                ['server_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    'wizkunde_websso_server_oauth2',
                    'server_id',
                    'wizkunde_websso_server',
                    'id'
                ),
                'server_id',
                $setup->getTable('wizkunde_websso_server'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'WebSSO OAuth2 Data'
            );

        $setup->getConnection()->createTable($table);
        
        /**
         * Create the Mapping mapping table
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('wizkunde_websso_mapping'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ], 'ID')
            ->addColumn('server_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'unsigned' => true,
                'nullable' => false,
            ], 'Server ID')
            ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable'  => false,
            ], 'Name')
            ->addColumn('external', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable'  => false,
            ], 'External Attribute')
            ->addColumn('internal', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable'  => false,
            ], 'Magento Attribute')
            ->addIndex(
                $setup->getIdxName('wizkunde_websso_mapping', ['server_id']),
                ['server_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    'wizkunde_websso_mapping',
                    'server_id',
                    'wizkunde_websso_server',
                    'id'
                ),
                'server_id',
                $setup->getTable('wizkunde_websso_server'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'WebSSO Mapping Data'
            );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
