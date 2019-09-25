<?php

namespace Ibnab\OwlSlider\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface {

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
        /* assign object to class global variable for use in other class methods */
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
       
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'hot_deal_from');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'hot_deal_to');
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'hot_deal_from', [
            'group' => 'Product Details',
            'frontend' => '',
            'label' => 'Hot Deal Start From',
            'type' => 'datetime',
            'input' => 'date',
            'backend' => Datetime::class,
            'class' => '',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false
                ]
        );
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'hot_deal_to', [
            'group' => 'Product Details',
            'frontend' => '',
            'label' => 'Hot Deal Start From',
            'type' => 'datetime',
            'input' => 'date',
            'backend' => Datetime::class,
            'class' => '',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => true,
            'unique' => false
                ]
        );
    }

}
