<?php

namespace Infortis\UltraMegamenu\Setup;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * Init
     *
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory, 
        EavSetupFactory $eavSetupFactory,
        ProductMetadataInterface $productMetadata
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->productMetadata = $productMetadata;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $magentoVersion = $this->productMetadata->getVersion();

        if (version_compare($context->getVersion(), '2.1.2') < 0)
        {
            if (version_compare($magentoVersion, '2.1.0', '>='))
            {
                $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
                $categorySetup->updateAttribute(
                    \Magento\Catalog\Model\Category::ENTITY,
                    'umm_dd_proportions',
                    'note',
                    'Proportions between 3 sections of drop-down: Left Block, Subcategories, Right Block. Enter 3 numbers separated by semicolon, e.g.: 3;6;3; Width is expressed in grid units (number between 0 and 12). Sum has to equal 12. See User Guide, chapter: 13. Menu'
                );
            }
        }

        $setup->endSetup();
    }
}
