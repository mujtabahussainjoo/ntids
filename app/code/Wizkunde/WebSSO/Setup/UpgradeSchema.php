<?php

namespace Wizkunde\WebSSO\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.7.0') < 0) {
            $table = $setup->getConnection()->newTable($setup->getTable('wizkunde_websso_log'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'ID'
                )
                ->addColumn(
                    'date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    0,
                    [
                        'nullable' => false,
                    ],
                    'Date'
                )
                ->addColumn(
                    'identifier',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    [
                        'nullable' => false,
                    ],
                    'Identifier'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    [
                        'nullable' => false,
                    ],
                    'Status'
                )
                ->addColumn(
                    'server',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    [
                        'nullable' => false,
                    ],
                    'Server'
                )
                ->addColumn(
                    'additional_info',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    [
                        'nullable' => false,
                    ],
                    'Additional Info'
                )
                ->addColumn(
                    'mappings',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    [
                        'nullable' => false,
                    ],
                    'Mappings'
                );

            $setup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.7.5') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('wizkunde_websso_server_saml2'),
                'algorithm',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Hashing Algorithm'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.7.7') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('wizkunde_websso_server_oauth2'),
                'logout_url',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Logout URL'
                ]
            );
        }

        $setup->endSetup();
    }
}
