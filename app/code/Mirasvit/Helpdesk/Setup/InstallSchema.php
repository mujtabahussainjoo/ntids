<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\Helpdesk\Api\Data\StatusInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_attachment')
        )
        ->addColumn(
            'attachment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Attachment Id'
        )
        ->addColumn(
            'email_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Email Id'
        )
        ->addColumn(
            'message_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Message Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Type'
        )
        ->addColumn(
            'size',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Size'
        )
        ->addColumn(
            'body',
            \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
            '4G',
            ['unsigned' => false, 'nullable' => true],
            'Body'
        )
        ->addColumn(
            'external_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'External Id'
        )
        ->addColumn(
            'storage',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true],
            'Storage'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_department')
        )
        ->addColumn(
            'department_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Department Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )
        ->addColumn(
            'sender_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Sender Email'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        )
        ->addColumn(
            'signature',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Signature'
        )
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )
        ->addColumn(
            'is_notification_enabled',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Notification Enabled'
        )
        ->addColumn(
            'notification_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Notification Email'
        )
        ->addColumn(
            'is_members_notification_enabled',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Members Notification Enabled'
        )
        ->addColumn(
            'is_show_in_frontend',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 1],
            'Is Show In Frontend'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_department_store')
        )
        ->addColumn(
            'department_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Department Store Id'
        )
        ->addColumn(
            'ds_department_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Ds Department Id'
        )
        ->addColumn(
            'ds_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Ds Store Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_department_store', ['ds_department_id']),
                ['ds_department_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_department_store', ['ds_store_id']),
                ['ds_store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_department_store',
                    'ds_department_id',
                    'mst_helpdesk_department',
                    'department_id'
                ),
                'ds_department_id',
                $installer->getTable('mst_helpdesk_department'),
                'department_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_department_store',
                    'ds_store_id',
                    'store',
                    'store_id'
                ),
                'ds_store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_department_user')
        )
        ->addColumn(
            'department_user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Department User Id'
        )
        ->addColumn(
            'du_department_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Du Department Id'
        )
        ->addColumn(
            'du_user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Du User Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_department_user', ['du_department_id']),
                ['du_department_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_department_user', ['du_user_id']),
                ['du_user_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_department_user',
                    'du_user_id',
                    'admin_user',
                    'user_id'
                ),
                'du_user_id',
                $installer->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_department_user',
                    'du_department_id',
                    'mst_helpdesk_department',
                    'department_id'
                ),
                'du_department_id',
                $installer->getTable('mst_helpdesk_department'),
                'department_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_draft')
        )
        ->addColumn(
            'draft_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Draft Id'
        )
        ->addColumn(
            'ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Ticket Id'
        )
        ->addColumn(
            'users_online',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Users Online'
        )
        ->addColumn(
            'body',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Body'
        )
        ->addColumn(
            'updated_by',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Updated By'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Updated At'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_draft', ['ticket_id']),
                ['ticket_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_draft',
                    'ticket_id',
                    'mst_helpdesk_ticket',
                    'ticket_id'
                ),
                'ticket_id',
                $installer->getTable('mst_helpdesk_ticket'),
                'ticket_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_email')
        )
        ->addColumn(
            'email_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Email Id'
        )
        ->addColumn(
            'from_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'From Email'
        )
        ->addColumn(
            'to_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'To Email'
        )
        ->addColumn(
            'subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Subject'
        )
        ->addColumn(
            'body',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Body'
        )
        ->addColumn(
            'format',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            3,
            ['unsigned' => false, 'nullable' => false],
            'Format'
        )
        ->addColumn(
            'sender_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Sender Name'
        )
        ->addColumn(
            'message_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Message Id'
        )
        ->addColumn(
            'pattern_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Pattern Id'
        )
        ->addColumn(
            'gateway_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Gateway Id'
        )
        ->addColumn(
            'headers',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Headers'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Created At'
        )
        ->addColumn(
            'is_processed',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Processed'
        )
        ->addColumn(
            'cc',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Cc'
        )
        ->addColumn(
            'bcc',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Bcc'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_field')
        )
        ->addColumn(
            'field_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Field Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )
        ->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Code'
        )
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Type'
        )
        ->addColumn(
            'values',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Values'
        )
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Description'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        )
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )
        ->addColumn(
            'is_required_staff',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Required Staff'
        )
        ->addColumn(
            'is_required_customer',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Required Customer'
        )
        ->addColumn(
            'is_visible_customer',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Visible Customer'
        )
        ->addColumn(
            'is_editable_customer',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Editable Customer'
        )
        ->addColumn(
            'is_visible_contact_form',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Visible Contact Form'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_field_store')
        )
        ->addColumn(
            'field_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Field Store Id'
        )
        ->addColumn(
            'fs_field_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Fs Field Id'
        )
        ->addColumn(
            'fs_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Fs Store Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_field_store', ['fs_field_id']),
                ['fs_field_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_field_store', ['fs_store_id']),
                ['fs_store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_field_store',
                    'fs_store_id',
                    'store',
                    'store_id'
                ),
                'fs_store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_field_store',
                    'fs_field_id',
                    'mst_helpdesk_field',
                    'field_id'
                ),
                'fs_field_id',
                $installer->getTable('mst_helpdesk_field'),
                'field_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_gateway')
        )
        ->addColumn(
            'gateway_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Gateway Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )
        ->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Email'
        )
        ->addColumn(
            'login',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Login'
        )
        ->addColumn(
            'password',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Password'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        )
        ->addColumn(
            'host',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Host'
        )
        ->addColumn(
            'port',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Port'
        )
        ->addColumn(
            'protocol',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Protocol'
        )
        ->addColumn(
            'encryption',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Encryption'
        )
        ->addColumn(
            'fetch_frequency',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Fetch Frequency'
        )
        ->addColumn(
            'fetch_max',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Fetch Max'
        )
        ->addColumn(
            'department_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Department Id'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )
        ->addColumn(
            'notes',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Notes'
        )
        ->addColumn(
            'fetched_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Fetched At'
        )
        ->addColumn(
            'last_fetch_result',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Last Fetch Result'
        )
        ->addColumn(
            'fetch_limit',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Fetch Limit'
        )
        ->addColumn(
            'is_delete_emails',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Delete Emails'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_gateway', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_gateway', ['department_id']),
                ['department_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_gateway',
                    'department_id',
                    'mst_helpdesk_department',
                    'department_id'
                ),
                'department_id',
                $installer->getTable('mst_helpdesk_department'),
                'department_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_gateway',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_history')
        )
        ->addColumn(
            'history_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'History Id'
        )
        ->addColumn(
            'ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Ticket Id'
        )
        ->addColumn(
            'triggered_by',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Triggered By'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )
        ->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Message'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Created At'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_history', ['ticket_id']),
                ['ticket_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_history',
                    'ticket_id',
                    'mst_helpdesk_ticket',
                    'ticket_id'
                ),
                'ticket_id',
                $installer->getTable('mst_helpdesk_ticket'),
                'ticket_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_message')
        )
        ->addColumn(
            'message_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Message Id'
        )
        ->addColumn(
            'ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Ticket Id'
        )
        ->addColumn(
            'email_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Email Id'
        )
        ->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'User Id'
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Customer Id'
        )
        ->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Customer Email'
        )
        ->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Customer Name'
        )
        ->addColumn(
            'body',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Body'
        )
        ->addColumn(
            'body_format',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => false, 'nullable' => false],
            'Body Format'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Created At'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Updated At'
        )
        ->addColumn(
            'uid',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Uid'
        )
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Type'
        )
        ->addColumn(
            'third_party_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Third Party Email'
        )
        ->addColumn(
            'third_party_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Third Party Name'
        )
        ->addColumn(
            'triggered_by',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Triggered By'
        )
        ->addColumn(
            'is_read',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Read'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_message', ['ticket_id']),
                ['ticket_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_message',
                    'ticket_id',
                    'mst_helpdesk_ticket',
                    'ticket_id'
                ),
                'ticket_id',
                $installer->getTable('mst_helpdesk_ticket'),
                'ticket_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_pattern')
        )
        ->addColumn(
            'pattern_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Pattern Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )
        ->addColumn(
            'pattern',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Pattern'
        )
        ->addColumn(
            'scope',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Scope'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_permission')
        )
        ->addColumn(
            'permission_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Permission Id'
        )
        ->addColumn(
            'role_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Role Id'
        )
        ->addColumn(
            'is_ticket_remove_allowed',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Ticket Remove Allowed'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_permission', ['role_id']),
                ['role_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_permission',
                    'role_id',
                    'admin_role',
                    'role_id'
                ),
                'role_id',
                $installer->getTable('admin_role'),
                'role_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_permission_department')
        )
        ->addColumn(
            'permission_department_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Permission Department Id'
        )
        ->addColumn(
            'permission_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Permission Id'
        )
        ->addColumn(
            'department_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Department Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_permission_department', ['permission_id']),
                ['permission_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_permission_department', ['department_id']),
                ['department_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_permission_department',
                    'permission_id',
                    'mst_helpdesk_permission',
                    'permission_id'
                ),
                'permission_id',
                $installer->getTable('mst_helpdesk_permission'),
                'permission_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_permission_department',
                    'department_id',
                    'mst_helpdesk_department',
                    'department_id'
                ),
                'department_id',
                $installer->getTable('mst_helpdesk_department'),
                'department_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_priority')
        )
        ->addColumn(
            'priority_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Priority Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )
        ->addColumn(
            'color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Color'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_priority_store')
        )
        ->addColumn(
            'priority_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Priority Store Id'
        )
        ->addColumn(
            'ps_priority_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Ps Priority Id'
        )
        ->addColumn(
            'ps_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Ps Store Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_priority_store', ['ps_priority_id']),
                ['ps_priority_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_priority_store', ['ps_store_id']),
                ['ps_store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_priority_store',
                    'ps_store_id',
                    'store',
                    'store_id'
                ),
                'ps_store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_priority_store',
                    'ps_priority_id',
                    'mst_helpdesk_priority',
                    'priority_id'
                ),
                'ps_priority_id',
                $installer->getTable('mst_helpdesk_priority'),
                'priority_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);



        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_rule')
        )
        ->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Rule Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )
        ->addColumn(
            'event',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Event'
        )
        ->addColumn(
            'email_subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Email Subject'
        )
        ->addColumn(
            'email_body',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Email Body'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Is Active'
        )
        ->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Conditions Serialized'
        )
        ->addColumn(
            'is_send_owner',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Send Owner'
        )
        ->addColumn(
            'is_send_department',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Send Department'
        )
        ->addColumn(
            'is_send_user',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Send User'
        )
        ->addColumn(
            'other_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Other Email'
        )
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )
        ->addColumn(
            'is_stop_processing',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Stop Processing'
        )
        ->addColumn(
            'priority_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Priority Id'
        )
        ->addColumn(
            'status_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Status Id'
        )
        ->addColumn(
            'department_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Department Id'
        )
        ->addColumn(
            'add_tags',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Add Tags'
        )
        ->addColumn(
            'remove_tags',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Remove Tags'
        )
        ->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'User Id'
        )
        ->addColumn(
            'is_send_attachment',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Send Attachment'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_rule', ['priority_id']),
                ['priority_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_rule', ['status_id']),
                ['status_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_rule', ['department_id']),
                ['department_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_rule', ['user_id']),
                ['user_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_rule',
                    'priority_id',
                    'mst_helpdesk_priority',
                    'priority_id'
                ),
                'priority_id',
                $installer->getTable('mst_helpdesk_priority'),
                'priority_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_rule',
                    'user_id',
                    'admin_user',
                    'user_id'
                ),
                'user_id',
                $installer->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_rule',
                    'status_id',
                    'mst_helpdesk_status',
                    'status_id'
                ),
                'status_id',
                $installer->getTable('mst_helpdesk_status'),
                'status_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_rule',
                    'department_id',
                    'mst_helpdesk_department',
                    'department_id'
                ),
                'department_id',
                $installer->getTable('mst_helpdesk_department'),
                'department_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_satisfaction')
        )
        ->addColumn(
            'satisfaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Satisfaction Id'
        )
        ->addColumn(
            'ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Ticket Id'
        )
        ->addColumn(
            'message_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Message Id'
        )
        ->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'User Id'
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Customer Id'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )
        ->addColumn(
            'rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Rate'
        )
        ->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Comment'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Created At'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Updated At'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_satisfaction', ['ticket_id']),
                ['ticket_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_satisfaction', ['message_id']),
                ['message_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_satisfaction', ['user_id']),
                ['user_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_satisfaction', ['customer_id']),
                ['customer_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_satisfaction', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_satisfaction',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_satisfaction',
                    'message_id',
                    'mst_helpdesk_message',
                    'message_id'
                ),
                'message_id',
                $installer->getTable('mst_helpdesk_message'),
                'message_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_satisfaction',
                    'user_id',
                    'admin_user',
                    'user_id'
                ),
                'user_id',
                $installer->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_satisfaction',
                    'customer_id',
                    'customer_entity',
                    'entity_id'
                ),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_satisfaction',
                    'ticket_id',
                    'mst_helpdesk_ticket',
                    'ticket_id'
                ),
                'ticket_id',
                $installer->getTable('mst_helpdesk_ticket'),
                'ticket_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_status')
        )
        ->addColumn(
            'status_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Status Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )
        ->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Code'
        )
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )
        ->addColumn(
            'color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Color'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_status_store')
        )
        ->addColumn(
            'status_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Status Store Id'
        )
        ->addColumn(
            'ss_status_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Ss Status Id'
        )
        ->addColumn(
            'ss_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Ss Store Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_status_store', ['ss_status_id']),
                ['ss_status_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_status_store', ['ss_store_id']),
                ['ss_store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_status_store',
                    'ss_status_id',
                    'mst_helpdesk_status',
                    'status_id'
                ),
                'ss_status_id',
                $installer->getTable('mst_helpdesk_status'),
                'status_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_status_store',
                    'ss_store_id',
                    'store',
                    'store_id'
                ),
                'ss_store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_tag')
        )
        ->addColumn(
            'tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Tag Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_template')
        )
        ->addColumn(
            'template_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Template Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )
        ->addColumn(
            'template',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Template'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_template_store')
        )
        ->addColumn(
            'template_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Template Store Id'
        )
        ->addColumn(
            'ts_template_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Ts Template Id'
        )
        ->addColumn(
            'ts_store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Ts Store Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_template_store', ['ts_template_id']),
                ['ts_template_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_template_store', ['ts_store_id']),
                ['ts_store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_template_store',
                    'ts_template_id',
                    'mst_helpdesk_template',
                    'template_id'
                ),
                'ts_template_id',
                $installer->getTable('mst_helpdesk_template'),
                'template_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_template_store',
                    'ts_store_id',
                    'store',
                    'store_id'
                ),
                'ts_store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_ticket')
        )
        ->addColumn(
            'ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Ticket Id'
        )
        ->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Code'
        )
        ->addColumn(
            'external_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'External Id'
        )
        ->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'User Id'
        )
        ->addColumn(
            'subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Subject'
        )
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Description'
        )
        ->addColumn(
            'priority_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Priority Id'
        )
        ->addColumn(
            'status_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Status Id'
        )
        ->addColumn(
            'department_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Department Id'
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Customer Id'
        )
        ->addColumn(
            'quote_address_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Quote Address Id'
        )
        ->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Customer Email'
        )
        ->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Customer Name'
        )
        ->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Order Id'
        )
        ->addColumn(
            'last_reply_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Last Reply Name'
        )
        ->addColumn(
            'last_reply_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Last Reply At'
        )
        ->addColumn(
            'reply_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Reply Cnt'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => true],
            'Store Id'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Created At'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Updated At'
        )
        ->addColumn(
            'folder',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 1],
            'Folder'
        )
        ->addColumn(
            'email_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Email Id'
        )
        ->addColumn(
            'first_reply_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'First Reply At'
        )
        ->addColumn(
            'first_solved_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'First Solved At'
        )
        ->addColumn(
            'fp_period_unit',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Fp Period Unit'
        )
        ->addColumn(
            'fp_period_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Fp Period Value'
        )
        ->addColumn(
            'fp_execute_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Fp Execute At'
        )
        ->addColumn(
            'fp_is_remind',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Fp Is Remind'
        )
        ->addColumn(
            'fp_remind_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Fp Remind Email'
        )
        ->addColumn(
            'fp_priority_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Fp Priority Id'
        )
        ->addColumn(
            'fp_status_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Fp Status Id'
        )
        ->addColumn(
            'fp_department_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Fp Department Id'
        )
        ->addColumn(
            'fp_user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Fp User Id'
        )
        ->addColumn(
            'channel',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Channel'
        )
        ->addColumn(
            'channel_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Channel Data'
        )
        ->addColumn(
            'third_party_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Third Party Email'
        )
        ->addColumn(
            'search_index',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Search Index'
        )
        ->addColumn(
            'cc',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Cc'
        )
        ->addColumn(
            'bcc',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Bcc'
        )
        ->addColumn(
            'merged_ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Merged Ticket Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_ticket', ['priority_id']),
                ['priority_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_ticket', ['status_id']),
                ['status_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_ticket', ['department_id']),
                ['department_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_ticket', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_ticket',
                    'department_id',
                    'mst_helpdesk_department',
                    'department_id'
                ),
                'department_id',
                $installer->getTable('mst_helpdesk_department'),
                'department_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_ticket',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_ticket',
                    'status_id',
                    'mst_helpdesk_status',
                    'status_id'
                ),
                'status_id',
                $installer->getTable('mst_helpdesk_status'),
                'status_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_ticket',
                    'priority_id',
                    'mst_helpdesk_priority',
                    'priority_id'
                ),
                'priority_id',
                $installer->getTable('mst_helpdesk_priority'),
                'priority_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )->addIndex(
                // CREATE FULLTEXT INDEX search_index_idx
                // ON mage_mst_helpdesk_ticket(code,name, customer_email, customer_name, search_index, cc, bcc);
                $installer->getIdxName(
                    'mst_helpdesk_ticket',
                    [
                        'code',
                        'subject',
                        'customer_email',
                        'customer_name',
                        'search_index',
                        'cc',
                        'bcc',
                    ],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    'code',
                    'subject',
                    'customer_email',
                    'customer_name',
                    'search_index',
                    'cc',
                    'bcc',
                ],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
            );

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_ticket_aggregated_day')
        )
        ->addColumn(
            'period',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Period'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Store Id'
        )
        ->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'User Id'
        )
        ->addColumn(
            'new_ticket_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'New Ticket Cnt'
        )
        ->addColumn(
            'solved_ticket_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Solved Ticket Cnt'
        )
        ->addColumn(
            'changed_ticket_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Changed Ticket Cnt'
        )
        ->addColumn(
            'total_reply_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Total Reply Cnt'
        )
        ->addColumn(
            'first_reply_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'First Reply Time'
        )
        ->addColumn(
            'first_resolution_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'First Resolution Time'
        )
        ->addColumn(
            'full_resolution_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Full Resolution Time'
        )
        ->addColumn(
            'satisfaction_rate_1_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate 1 Cnt'
        )
        ->addColumn(
            'satisfaction_rate_2_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate 2 Cnt'
        )
        ->addColumn(
            'satisfaction_rate_3_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate 3 Cnt'
        )
        ->addColumn(
            'satisfaction_rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate'
        )
        ->addColumn(
            'satisfaction_response_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Response Cnt'
        )
        ->addColumn(
            'satisfaction_response_rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Response Rate'
        )
            ->addIndex(
                $installer->getIdxName(
                    'mst_helpdesk_ticket_aggregated_day',
                    ['period', 'store_id', 'user_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['period', 'store_id', 'user_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_ticket_aggregated_hour')
        )
        ->addColumn(
            'period',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Period'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Store Id'
        )
        ->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'User Id'
        )
        ->addColumn(
            'new_ticket_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'New Ticket Cnt'
        )
        ->addColumn(
            'solved_ticket_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Solved Ticket Cnt'
        )
        ->addColumn(
            'changed_ticket_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Changed Ticket Cnt'
        )
        ->addColumn(
            'total_reply_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Total Reply Cnt'
        )
        ->addColumn(
            'first_reply_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'First Reply Time'
        )
        ->addColumn(
            'first_resolution_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'First Resolution Time'
        )
        ->addColumn(
            'full_resolution_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Full Resolution Time'
        )
        ->addColumn(
            'satisfaction_rate_1_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate 1 Cnt'
        )
        ->addColumn(
            'satisfaction_rate_2_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate 2 Cnt'
        )
        ->addColumn(
            'satisfaction_rate_3_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate 3 Cnt'
        )
        ->addColumn(
            'satisfaction_rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate'
        )
        ->addColumn(
            'satisfaction_response_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Response Cnt'
        )
        ->addColumn(
            'satisfaction_response_rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Response Rate'
        )
            ->addIndex(
                $installer->getIdxName(
                    'mst_helpdesk_ticket_aggregated_hour',
                    ['period', 'store_id', 'user_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['period', 'store_id', 'user_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_ticket_aggregated_month')
        )
        ->addColumn(
            'period',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Period'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Store Id'
        )
        ->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'User Id'
        )
        ->addColumn(
            'new_ticket_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'New Ticket Cnt'
        )
        ->addColumn(
            'solved_ticket_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Solved Ticket Cnt'
        )
        ->addColumn(
            'changed_ticket_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Changed Ticket Cnt'
        )
        ->addColumn(
            'total_reply_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Total Reply Cnt'
        )
        ->addColumn(
            'first_reply_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'First Reply Time'
        )
        ->addColumn(
            'first_resolution_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'First Resolution Time'
        )
        ->addColumn(
            'full_resolution_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Full Resolution Time'
        )
        ->addColumn(
            'satisfaction_rate_1_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate 1 Cnt'
        )
        ->addColumn(
            'satisfaction_rate_2_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate 2 Cnt'
        )
        ->addColumn(
            'satisfaction_rate_3_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate 3 Cnt'
        )
        ->addColumn(
            'satisfaction_rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Rate'
        )
        ->addColumn(
            'satisfaction_response_cnt',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Response Cnt'
        )
        ->addColumn(
            'satisfaction_response_rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Satisfaction Response Rate'
        )
            ->addIndex(
                $installer->getIdxName(
                    'mst_helpdesk_ticket_aggregated_month',
                    ['period', 'store_id', 'user_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['period', 'store_id', 'user_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_ticket_tag')
        )
        ->addColumn(
            'ticket_tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Ticket Tag Id'
        )
        ->addColumn(
            'tt_ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Tt Ticket Id'
        )
        ->addColumn(
            'tt_tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Tt Tag Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_ticket_tag', ['tt_ticket_id']),
                ['tt_ticket_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_ticket_tag', ['tt_tag_id']),
                ['tt_tag_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_ticket_tag',
                    'tt_ticket_id',
                    'mst_helpdesk_ticket',
                    'ticket_id'
                ),
                'tt_ticket_id',
                $installer->getTable('mst_helpdesk_ticket'),
                'ticket_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_ticket_tag',
                    'tt_tag_id',
                    'mst_helpdesk_tag',
                    'tag_id'
                ),
                'tt_tag_id',
                $installer->getTable('mst_helpdesk_tag'),
                'tag_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_helpdesk_user')
        )
        ->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'User Id'
        )
        ->addColumn(
            'signature',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Signature'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => true],
            'Store Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_user', ['user_id']),
                ['user_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_helpdesk_user', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_user',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_helpdesk_user',
                    'user_id',
                    'admin_user',
                    'user_id'
                ),
                'user_id',
                $installer->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }
}
