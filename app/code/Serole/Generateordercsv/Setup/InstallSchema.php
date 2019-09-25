<?php
namespace Serole\Generateordercsv\Setup;


use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /*$installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'sftpSentDate',
            [
                'type' => Table::TYPE_DATETIME,
                'nullable' => false,
                'default' => '1979-01-01 00:00:00',
                'comment' => 'SFTP Sent Date',
            ]
        );*/

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_item'),
            'serial_codes',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'default' => NULL,
                'comment' => 'serial_codes',
            ]
        );

        $setup->endSetup();
    }
}