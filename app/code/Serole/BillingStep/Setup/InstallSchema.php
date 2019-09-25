<?php
namespace Serole\BillingStep\Setup;


use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

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

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'deliveryemail',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Delivery Email',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'billingemail',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Billing Email',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'deliveryemail',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Delivery Email',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'billingemail',
            [
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Billing Email',
            ]
        );

        $setup->endSetup();
    }
}