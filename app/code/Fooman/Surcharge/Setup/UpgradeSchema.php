<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.2.2', '<')) {
            $this->addColumnTaxClassId($setup);
        }

        if (version_compare($context->getVersion(), '9.1.0', '<')) {
            $this->addColumnTaxInclusive($setup);
        }
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return void
     */
    private function addColumnTaxClassId(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable('fooman_surcharge'),
            'tax_class_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'length' => null,
                'default' => false,
                'unsigned' => true,
                'primary' => false,
                'comment' => 'Tax Class ID',
                'after' => 'description'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return void
     */
    private function addColumnTaxInclusive(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable('fooman_surcharge'),
            'tax_inclusive',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'length' => null,
                'default' => 0,
                'unsigned' => true,
                'primary' => false,
                'comment' => 'Tax Inclusive',
                'after' => 'tax_class_id'
            ]
        );
    }
}
