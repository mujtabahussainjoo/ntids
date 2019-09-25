<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use MageWorx\ShippingRules\Model\Carrier;
use MageWorx\ShippingRules\Model\Zone;
use MageWorx\ShippingRules\Model\ExtendedZone;
use MageWorx\ShippingRules\Model\Region;
use MageWorx\ShippingRules\Model\Rule;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var ResourceInterface
     */
    protected $moduleResource;

    /**
     * InstallSchema constructor.
     * @param ResourceInterface $moduleResource
     */
    public function __construct(
        ResourceInterface $moduleResource
    ) {
        $this->moduleResource = $moduleResource;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addCarriersTable($setup);
            $this->addMethodsTable($setup);
            $this->addLabelsTables($setup);
        }

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->addMethodRatesTable($setup);
        }

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->addZonesTable($setup);
            $this->addZonesStoreViewTable($setup);
        }

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->addExtendedRegionsTable($setup);
        }

        if (version_compare($context->getVersion(), '1.6.1', '<')) {
            $this->updateMethodsTable($setup);
        }

        if (version_compare($context->getVersion(), '1.6.2', '<')) {
            $this->addExtendedZonesTable($setup);
            $this->addExtendedZonesStoreViewTable($setup);
            $this->addExtendedZoneLabelsTable($setup);
        }

        if (version_compare($context->getVersion(), '1.6.3', '<')) {
            $this->updateMethodsTable($setup);
            $this->updateRatesTable($setup);
            $this->addLabelsTables($setup);
            $this->addStoreSpecificEstimatedDeliveryTimeMessageTable($setup);
            $this->addErrorMessagesForRulesActions($setup);
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $this->updatePriceWeightFieldsAddSmallerValues($setup);
        }

        if (version_compare($context->getVersion(), '2.0.7', '<')) {
            $this->addStoreAndGroupTablesToTheMethodsAndRates($setup);
            $this->addCodeColumnsToEntities($setup);
        }

        if (version_compare($context->getVersion(), '2.1.1', '<')) {
            $this->addChangedTitlesColumnToRule($setup);
            $this->updateRateCountryRegionFields($setup);
        }

        $setup->endSetup();
    }

    /**
     * Updates fields country_id and region_id
     * what makes available to store a serialized values inside.
     * Makes the region column larger.
     *
     * @param SchemaSetupInterface $setup
     */
    private function updateRateCountryRegionFields(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->changeColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'country_id',
            'country_id',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '64k',
            ]
        );

        $setup->getConnection()->changeColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'region_id',
            'region_id',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '64k',
            ]
        );

        $setup->getConnection()->changeColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'region',
            'region',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '64k',
            ]
        );
    }

    /**
     * Add column changed titles (serialized value)
     *
     * @param SchemaSetupInterface $setup
     */
    private function addChangedTitlesColumnToRule(SchemaSetupInterface $setup)
    {
        // Add changed title column in the rules table
        $setup->getConnection()->addColumn(
            $setup->getTable(Rule::TABLE_NAME),
            'changed_titles',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '2M',
                'nullable' => true,
                'comment' => 'Shipping Methods Changed Titles',
            ]
        );
    }

    /**
     * Create entity to store relation table
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param $tableName
     * @param string $refIdFieldName
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Exception
     */
    private function addStoreRelationTable(SchemaSetupInterface $setup, $tableName, $refIdFieldName = 'entity_id')
    {
        if (!$tableName) {
            throw new LocalizedException(__('Table name is required'));
        }

        $storeTable = $setup->getTable('store');
        $entityTable = $setup->getTable($tableName);
        $entityStoreTableName = $tableName . '_store';
        $entityStoresTable = $setup->getTable($entityStoreTableName);

        $table = $setup->getConnection()->newTable(
            $entityStoresTable
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store View Id'
        )->addIndex(
            $setup->getIdxName($entityStoreTableName, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName(
                $entityStoreTableName,
                'entity_id',
                $tableName,
                $refIdFieldName
            ),
            'entity_id',
            $entityTable,
            $refIdFieldName,
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName($entityStoreTableName, 'store_id', 'store', 'store_id'),
            'store_id',
            $storeTable,
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Shipping Rules Entity To Stores Relations'
        );

        $setup->getConnection()->createTable($table);
    }

    /**
     * Adds store relations tables for the entities: carrier, method, rate
     *
     * @param SchemaSetupInterface $setup
     */
    private function addStoreAndGroupTablesToTheMethodsAndRates(SchemaSetupInterface $setup)
    {
        $this->addStoreRelationTable($setup, Carrier::CARRIER_TABLE_NAME, 'carrier_id');
        $this->addStoreRelationTable($setup, Carrier::METHOD_TABLE_NAME, 'entity_id');
        $this->addStoreRelationTable($setup, Carrier::RATE_TABLE_NAME, 'rate_id');
    }

    /**
     * Add code > entity relations
     *
     * @param SchemaSetupInterface $setup
     */
    private function addCodeColumnsToEntities(SchemaSetupInterface $setup)
    {
        // Add Carrier code in the methods table
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'carrier_code',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 64,
                'nullable' => false,
                'comment' => 'Carrier Code (relation)',
            ]
        );

        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                Carrier::METHOD_TABLE_NAME,
                'carrier_code',
                Carrier::CARRIER_TABLE_NAME,
                'carrier_code'
            ),
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'carrier_code',
            $setup->getTable(Carrier::CARRIER_TABLE_NAME),
            'carrier_code',
            'CASCADE ON UPDATE CASCADE',
            false
        );

        // Add Method code in the rates table
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'method_code',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 64,
                'nullable' => false,
                'comment' => 'Method Code (relation)',
            ]
        );

        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                Carrier::RATE_TABLE_NAME,
                'method_code',
                Carrier::METHOD_TABLE_NAME,
                'code'
            ),
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'method_code',
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'code',
            'CASCADE ON UPDATE CASCADE',
            false
        );

        // Add rate code in the rates table
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'rate_code',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 64,
                'nullable' => true,
                'comment' => 'Rate Code',
            ]
        );

        // Code is unique
        $setup->getConnection()->addIndex(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            $setup->getIdxName(
                Carrier::RATE_TABLE_NAME,
                ['rate_code'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['rate_code'],
            AdapterInterface::INDEX_TYPE_UNIQUE
        );
    }

    /**
     * Adds main Pop-up Zones table
     *
     * @param SchemaSetupInterface $setup
     */
    protected function addExtendedZonesTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ExtendedZone::EXTENDED_ZONE_TABLE_NAME))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            // Sort order of the EX (render on the frontend using this column)
            ->addColumn(
                'priority',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Priority (Sort Order)'
            )
            // Inactive zones should be not visible on the frontend
            ->addColumn(
                'is_active',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Is Active'
            )
            // EZ name visible on the frontend in modal. Should be unique
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [],
                'Name'
            )
            // EZ description visible on the frontend in modal
            ->addColumn(
                'description',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Description'
            )
            // Pop-up Zone Preview image for the form in modal (frontend)
            ->addColumn(
                'image',
                Table::TYPE_TEXT,
                255,
                [],
                'Image'
            )
            // Pop-up Zone associated Countries list
            ->addColumn(
                'countries_id',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => false],
                'Countries'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
            ->addIndex(
                $setup->getIdxName(ExtendedZone::EXTENDED_ZONE_TABLE_NAME, ['entity_id']),
                ['entity_id']
            )
            // Zone Name should be unique
            ->addIndex(
                $setup->getIdxName(
                    ExtendedZone::EXTENDED_ZONE_TABLE_NAME,
                    ['name'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                'name',
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Zones Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addExtendedZonesStoreViewTable($setup)
    {
        $storeTable = $setup->getTable('store');
        $zonesStoresTable = $setup->getTable(ExtendedZone::EXTENDED_ZONE_STORE_TABLE_NAME);

        /**
         * Create table 'mageworx_shippingrules_extended_zone_store' if not exists. This table will be used instead of
         * column store_ids of main Pop-up Zones table
         */
        $table = $setup->getConnection()->newTable(
            $zonesStoresTable
        )->addColumn(
            'zone_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Pop-up Zone Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store View Id'
        )->addIndex(
            $setup->getIdxName(ExtendedZone::EXTENDED_ZONE_STORE_TABLE_NAME, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName(
                ExtendedZone::EXTENDED_ZONE_STORE_TABLE_NAME,
                'zone_id',
                ExtendedZone::EXTENDED_ZONE_TABLE_NAME,
                'entity_id'
            ),
            'zone_id',
            $setup->getTable(ExtendedZone::EXTENDED_ZONE_TABLE_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                ExtendedZone::EXTENDED_ZONE_STORE_TABLE_NAME,
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $storeTable,
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Pop-up Zones To Stores Relations'
        );

        $setup->getConnection()->createTable($table);
    }

    /**
     * Adds labels table for the Pop-up Zones
     *
     * @param SchemaSetupInterface $setup
     */
    protected function addExtendedZoneLabelsTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'mageworx_shippingrules_carrier_label'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable(ExtendedZone::EXTENDED_ZONE_LABELS_TABLE_NAME)
        )->addColumn(
            'label_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'zone_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Zone Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'label',
            Table::TYPE_TEXT,
            255,
            [],
            'Label'
        )->addIndex(
            $setup->getIdxName(
                ExtendedZone::EXTENDED_ZONE_LABELS_TABLE_NAME,
                ['zone_id', 'store_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['zone_id', 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName(ExtendedZone::EXTENDED_ZONE_LABELS_TABLE_NAME, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName(ExtendedZone::EXTENDED_ZONE_LABELS_TABLE_NAME, 'zone_id', ExtendedZone::EXTENDED_ZONE_TABLE_NAME, 'entity_id'),
            'zone_id',
            $setup->getTable(ExtendedZone::EXTENDED_ZONE_TABLE_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(ExtendedZone::EXTENDED_ZONE_LABELS_TABLE_NAME, 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Shipping Rules Pop-up Zones Label'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Adds carriers table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    protected function addCarriersTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(Carrier::CARRIER_TABLE_NAME))
            ->addColumn(
                'carrier_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'carrier_code',
                Table::TYPE_TEXT,
                64,
                [],
                'Carrier Code'
            )
            ->addColumn(
                'active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Is Active'
            )
            ->addColumn(
                'sallowspecific',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'sallowspecific'
            )
            ->addColumn(
                'model',
                Table::TYPE_TEXT,
                mb_strlen(Carrier::DEFAULT_MODEL),
                ['default' => Carrier::DEFAULT_MODEL],
                'Corresponding Model'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Carrier Name'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Carrier Title'
            )
            ->addColumn(
                'type',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Carrier Type'
            )
            ->addColumn(
                'specificerrmsg',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Carrier Error Message'
            )
            ->addColumn(
                'price',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Default Price'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
            ->addIndex(
                $setup->getIdxName(Carrier::CARRIER_TABLE_NAME, ['carrier_code']),
                ['carrier_code']
            )
            ->addIndex(
                $setup->getIdxName(Carrier::CARRIER_TABLE_NAME, ['carrier_id']),
                ['carrier_id']
            )
            ->addIndex(
                $setup->getIdxName(
                    Carrier::CARRIER_TABLE_NAME,
                    ['carrier_id', 'carrier_code'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['carrier_id', 'carrier_code'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Artificial Carriers Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * Adds methods table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    protected function addMethodsTable(SchemaSetupInterface $setup)
    {

        $table = $setup->getConnection()
            ->newTable($setup->getTable(Carrier::METHOD_TABLE_NAME))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'code',
                Table::TYPE_TEXT,
                64,
                [],
                'Method Code'
            )
            ->addColumn(
                'carrier_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Carrier ID'
            )
            ->addColumn(
                'active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Is Active'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Method Title'
            )
            ->addColumn(
                'price',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Default Price'
            )
            ->addColumn(
                'cost',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Default Cost'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
            ->addIndex(
                $setup->getIdxName(
                    Carrier::METHOD_TABLE_NAME,
                    ['entity_id', 'code', 'carrier_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'code', 'carrier_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $setup->getIdxName(Carrier::METHOD_TABLE_NAME, ['entity_id']),
                ['entity_id']
            )
            ->addIndex(
                $setup->getIdxName(Carrier::METHOD_TABLE_NAME, ['code']),
                ['code']
            )
            ->addIndex(
                $setup->getIdxName(Carrier::METHOD_TABLE_NAME, ['carrier_id']),
                ['carrier_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    Carrier::METHOD_TABLE_NAME,
                    'carrier_id',
                    Carrier::CARRIER_TABLE_NAME,
                    'carrier_id'
                ),
                'carrier_id',
                $setup->getTable(Carrier::CARRIER_TABLE_NAME),
                'carrier_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Artificial Carriers Methods Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * Adds labels tables for the methods and carriers
     *
     * @param SchemaSetupInterface $setup
     */
    protected function addLabelsTables(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'mageworx_shippingrules_carrier_label'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable(Carrier::CARRIER_LABELS_TABLE_NAME)
        )->addColumn(
            'label_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'carrier_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Carrier Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'label',
            Table::TYPE_TEXT,
            255,
            [],
            'Label'
        )->addIndex(
            $setup->getIdxName(
                Carrier::CARRIER_LABELS_TABLE_NAME,
                ['carrier_id', 'store_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['carrier_id', 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName(Carrier::CARRIER_LABELS_TABLE_NAME, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName(Carrier::CARRIER_LABELS_TABLE_NAME, 'carrier_id', Carrier::CARRIER_TABLE_NAME, 'carrier_id'),
            'carrier_id',
            $setup->getTable(Carrier::CARRIER_TABLE_NAME),
            'carrier_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(Carrier::CARRIER_LABELS_TABLE_NAME, 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Shipping Rules Carrier Label'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'mageworx_shippingrules_methods_label'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable(Carrier::METHOD_LABELS_TABLE_NAME)
        )->addColumn(
            'label_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'method_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Method Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'label',
            Table::TYPE_TEXT,
            255,
            [],
            'Label'
        )->addIndex(
            $setup->getIdxName(
                Carrier::METHOD_LABELS_TABLE_NAME,
                ['method_id', 'store_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['method_id', 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName(Carrier::METHOD_LABELS_TABLE_NAME, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName(Carrier::METHOD_LABELS_TABLE_NAME, 'method_id', Carrier::METHOD_TABLE_NAME, 'entity_id'),
            'method_id',
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(Carrier::METHOD_LABELS_TABLE_NAME, 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Shipping Rules Method Label'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'mageworx_shippingrules_rate_label'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable(Carrier::RATE_LABELS_TABLE_NAME)
        )->addColumn(
            'label_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'rate_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Rate Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'label',
            Table::TYPE_TEXT,
            255,
            [],
            'Label'
        )->addIndex(
            $setup->getIdxName(
                Carrier::RATE_LABELS_TABLE_NAME,
                ['rate_id', 'store_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['rate_id', 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName(Carrier::RATE_LABELS_TABLE_NAME, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName(Carrier::RATE_LABELS_TABLE_NAME, 'rate_id', Carrier::RATE_TABLE_NAME, 'rate_id'),
            'rate_id',
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'rate_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(Carrier::RATE_LABELS_TABLE_NAME, 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Shipping Rules Rate Labels'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Add method rates table
     *
     * @param SchemaSetupInterface $setup
     */
    protected function addMethodRatesTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(Carrier::RATE_TABLE_NAME))
            ->addColumn(
                'rate_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'method_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Corresponding Method ID'
            )
            ->addColumn(
                'priority',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Priority'
            )
            ->addColumn(
                'active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Is Active'
            )
            ->addColumn(
                'rate_method_price',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Rate price calculation type'
            )
            ->addColumn(
                'title',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Rate Title'
            )
            ->addColumn(
                'country_id',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Country'
            )
            ->addColumn(
                'region',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'State/Province'
            )
            ->addColumn(
                'region_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true, 'default' => null],
                'State/Province'
            )
            ->addColumn(
                'zip_from',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Zip/Postal Code From'
            )
            ->addColumn(
                'zip_to',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Zip/Postal Code To'
            )
            ->addColumn(
                'price_from',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Price From'
            )
            ->addColumn(
                'price_to',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Price To'
            )
            ->addColumn(
                'qty_from',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Qty From'
            )
            ->addColumn(
                'qty_to',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Qty To'
            )
            ->addColumn(
                'weight_from',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Weight From'
            )
            ->addColumn(
                'weight_to',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Weight To'
            )
            ->addColumn(
                'price',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Price'
            )
            ->addColumn(
                'price_per_product',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Price Per Product'
            )
            ->addColumn(
                'price_per_item',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Price Per Item'
            )
            ->addColumn(
                'price_percent_per_product',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Price Percent Per Product'
            )
            ->addColumn(
                'price_percent_per_item',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Price Percent Per Item'
            )
            ->addColumn(
                'item_price_percent',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Item Price Percent'
            )
            ->addColumn(
                'price_per_weight',
                Table::TYPE_DECIMAL,
                '12,2',
                [],
                'Price Per One Unit Of Weight'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
            ->addIndex(
                $setup->getIdxName(
                    Carrier::RATE_TABLE_NAME,
                    ['rate_id', 'method_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['rate_id', 'method_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex(
                $setup->getIdxName(Carrier::RATE_TABLE_NAME, ['rate_id']),
                ['rate_id']
            )
            ->addIndex(
                $setup->getIdxName(Carrier::RATE_TABLE_NAME, ['method_id']),
                ['method_id']
            )
            ->addForeignKey(
                $setup->getFkName(
                    Carrier::RATE_TABLE_NAME,
                    'method_id',
                    Carrier::METHOD_TABLE_NAME,
                    'entity_id'
                ),
                'method_id',
                $setup->getTable(Carrier::METHOD_TABLE_NAME),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Method Rates Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * Adds table for the zones
     *
     * @param SchemaSetupInterface $setup
     */
    protected function addZonesTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(Zone::ZONE_TABLE_NAME))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'priority',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Priority'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Is Active'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [],
                'Name'
            )
            ->addColumn(
                'description',
                Table::TYPE_TEXT,
                '64k',
                [],
                'Description'
            )
            ->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                '2M',
                [],
                'Conditions Serialized'
            )
            ->addColumn(
                'default_shipping_method',
                Table::TYPE_TEXT,
                255,
                [],
                'Default Shipping Method'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
            ->addIndex(
                $setup->getIdxName(Zone::ZONE_TABLE_NAME, ['entity_id']),
                ['entity_id']
            )
            ->setComment('Zones Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addZonesStoreViewTable($setup)
    {
        $storeTable = $setup->getTable('store');
        $zonesStoresTable = $setup->getTable(Zone::ZONE_STORE_TABLE_NAME);

        /**
         * Create table 'mageworx_shippingrules_zone_store' if not exists. This table will be used instead of
         * column store_ids of main shipping zones table
         */
        $table = $setup->getConnection()->newTable(
            $zonesStoresTable
        )->addColumn(
            'zone_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Zone Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store View Id'
        )->addIndex(
            $setup->getIdxName(Zone::ZONE_STORE_TABLE_NAME, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName(Zone::ZONE_STORE_TABLE_NAME, 'zone_id', Zone::ZONE_TABLE_NAME, 'entity_id'),
            'zone_id',
            $setup->getTable(Zone::ZONE_TABLE_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(Zone::ZONE_STORE_TABLE_NAME, 'store_id', 'store', 'store_id'),
            'store_id',
            $storeTable,
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Shipping Zones To Stores Relations'
        );

        $setup->getConnection()->createTable($table);
    }

    /**
     * Add extended regions data table
     * will be left joined to the regular table if use extended regions setting is enabled
     *
     * @param SchemaSetupInterface $setup
     */
    protected function addExtendedRegionsTable($setup)
    {
        $extendedRegionsTable = $setup->getTable(Region::EXTENDED_REGIONS_TABLE_NAME);
        $originalRegionsTableName = 'directory_country_region';
        $originalRegionsTable = $setup->getTable($originalRegionsTableName);

        /**
         * Create table 'mageworx_shippingrules_zone_store' if not exists. This table will be used instead of
         * column store_ids of main shipping zones table
         */
        $table = $setup->getConnection()->newTable(
            $extendedRegionsTable
        )->addColumn(
            'region_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Region Id'
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Is Active'
        )->addColumn(
            'is_custom',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Custom Region created using module'
        )->addForeignKey(
            $setup->getFkName(Region::EXTENDED_REGIONS_TABLE_NAME, 'region_id', $originalRegionsTableName, 'region_id'),
            'region_id',
            $originalRegionsTable,
            'region_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Shipping Suite: Extended Regions Data Table'
        );

        $setup->getConnection()->createTable($table);
    }

    /**
     * Adds flag for the method: disabled if there are no valid rates
     * Adds Estimated Delivery Time (EDT)
     * Adds price min&max threshold
     * Adds multiple rates price calculation method
     * Adds description
     * Adds image
     * Adds flag: show description & image (frontend)
     * Adds flag: replaceable title & EDT
     *
     * @param SchemaSetupInterface $setup
     */
    protected function updateMethodsTable(SchemaSetupInterface $setup)
    {
        //DisabledWithoutValidRates
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'disabled_without_valid_rates',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Method without any valid rate should be disabled',
            ]
        );

        // Max price Threshold
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'max_price_threshold',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'comment' => 'Max price Threshold',
            ]
        );
        // Min price Threshold
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'min_price_threshold',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'comment' => 'Min price Threshold',
            ]
        );

        // Multiple Rates price calculation type
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'multiple_rates_price',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Multiple Rates price calculation type',
            ]
        );

        // Replace title from valid rate
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'replaceable_title',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 1,
                'comment' => 'Replace title from valid rate',
            ]
        );

        // Method description
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'description',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'LENGTH' => '64k',
                'comment' => 'Method description',
            ]
        );

        // Show description on the frontend flag
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'show_description',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Show description on the frontend flag',
            ]
        );

        // Method image
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'image',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'LENGTH' => 255,
                'comment' => 'Method image',
            ]
        );

        // Show image on the frontend flag
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'show_image',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Show image on the frontend flag',
            ]
        );

        // Replace Estimated delivery time from valid rate
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'replaceable_estimated_delivery_time',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 1,
                'comment' => 'Replace Estimated delivery time from a valid rate',
            ]
        );

        // Estimated delivery time MIN
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'estimated_delivery_time_min',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'comment' => 'Estimated delivery time Min',
            ]
        );

        // Estimated delivery time MAX
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'estimated_delivery_time_max',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'comment' => 'Estimated delivery time Max',
            ]
        );

        // Estimated delivery time data type: Days or Hours
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'estimated_delivery_time_display_type',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Estimated delivery time data type: Days or Hours',
            ]
        );

        // Estimated delivery time message to display on front-end
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'estimated_delivery_time_message',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'LENGTH' => 255,
                'comment' => 'Estimated delivery time message to display on front-end',
            ]
        );

        // Show estimated_delivery_time on the frontend flag
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'show_estimated_delivery_time',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Show estimated delivery time on the frontend flag',
            ]
        );

        // Allow Free Shipping flag (by third-party modules as sales rule)
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'allow_free_shipping',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Allow Free Shipping flag (by third-party modules as sales rule)',
            ]
        );
    }

    /**
     * Adds Estimated Delivery Time (EDT)
     *
     * @param SchemaSetupInterface $setup
     */
    protected function updateRatesTable(SchemaSetupInterface $setup)
    {
        // Estimated delivery time MIN
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'estimated_delivery_time_min',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'comment' => 'Estimated delivery time Min',
            ]
        );

        // Estimated delivery time MAX
        $setup->getConnection()->addColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'estimated_delivery_time_max',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,2',
                'nullable' => true,
                'comment' => 'Estimated delivery time Max',
            ]
        );
    }

    /**
     * Adds Estimated Delivery Time (EDT) store specific messages table (Method related)
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    protected function addStoreSpecificEstimatedDeliveryTimeMessageTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'mageworx_shippingrules_method_edt_store_specific_message'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable(Carrier::METHOD_STORE_SPECIFIC_EDT_MESSAGE_TABLE_NAME)
        )->addColumn(
            'message_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'method_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Method Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'message',
            Table::TYPE_TEXT,
            255,
            [],
            'Message'
        )->addIndex(
            $setup->getIdxName(
                Carrier::METHOD_STORE_SPECIFIC_EDT_MESSAGE_TABLE_NAME,
                ['method_id', 'store_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['method_id', 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName(Carrier::METHOD_STORE_SPECIFIC_EDT_MESSAGE_TABLE_NAME, ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName(
                Carrier::METHOD_STORE_SPECIFIC_EDT_MESSAGE_TABLE_NAME,
                'method_id',
                Carrier::METHOD_TABLE_NAME,
                'entity_id'
            ),
            'method_id',
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(Carrier::METHOD_STORE_SPECIFIC_EDT_MESSAGE_TABLE_NAME, 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'MageWorx Shipping Rules Estimated Delivery Time store specific message'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Adds error messages functionality for the rules
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    protected function addErrorMessagesForRulesActions(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(Rule::TABLE_NAME),
            'display_error_message',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Display or not the disabled methods error message',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(Rule::TABLE_NAME),
            'error_message',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'LENGTH' => 255,
                'comment' => 'Disabled methods error message',
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(Rule::TABLE_NAME),
            'store_errmsgs',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'LENGTH' => '64k',
                'comment' => 'Store Specific Error Messages (serialized)',
            ]
        );
    }

    /**
     * Adds ability to store .4 values in the price and weight fields
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    protected function updatePriceWeightFieldsAddSmallerValues(SchemaSetupInterface $setup)
    {
        // Methods table
        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'price',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'cost',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'max_price_threshold',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::METHOD_TABLE_NAME),
            'min_price_threshold',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        // Rates table
        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'price_from',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'price_to',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'qty_from',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'qty_to',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'weight_from',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'weight_to',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'price',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'price_per_product',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'price_per_item',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'price_percent_per_product',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'price_percent_per_item',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'item_price_percent',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );

        $setup->getConnection()->modifyColumn(
            $setup->getTable(Carrier::RATE_TABLE_NAME),
            'price_per_weight',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '14,4',
            ]
        );
    }

    /**
     * @return string
     */
    protected function getCustomerGroupColumnType()
    {
        $customerDbVersion = $this->moduleResource->getDbVersion('Magento_Customer');

        if (version_compare($customerDbVersion, '2.0.10', '<')) {
            return Table::TYPE_SMALLINT;
        }

        return Table::TYPE_INTEGER;
    }
}
