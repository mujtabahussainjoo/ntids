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

use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\CatalogRule\Api\Data\RuleInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        MetadataPool $metadataPool,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->metadataPool = $metadataPool;
        $this->productMetadata = $productMetadata;
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.11', '<')) {
            if (version_compare($this->productMetadata->getVersion(), '2.2.2', '>=')) {
                $this->convertSerializedDataToJson($setup);
            }
        }

        $setup->endSetup();
    }

    /**
     * Convert metadata from serialized to JSON format:
     *
     * @param ModuleDataSetupInterface $setup
     *
     * @return void
     */
    public function convertSerializedDataToJson($setup)
    {
        /** @var \Magento\Framework\DB\AggregatedFieldDataConverter $aggregatedFieldConverter */
        $aggregatedFieldConverter = $this->objectManager->get("\Magento\Framework\DB\AggregatedFieldDataConverter");
        $aggregatedFieldConverter->convert(
            [
                new FieldToConvert(
                    SerializedToJson::class,
                    $setup->getTable('mst_helpdesk_rule'),
                    "rule_id",
                    'conditions_serialized'
                ),
            ],
            $setup->getConnection()
        );
    }
}
