<?php

namespace Infortis\UltraMegamenu\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * Init
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);

        $groups = [
            'menu' => ['name' => 'Menu', 'code' => 'menu', 'sort' => 80, 'id' => null],
        ];

        $categorySetup->addAttributeGroup($entityTypeId, $attributeSetId, $groups['menu']['name'], $groups['menu']['sort']);
        $menuAttributeGroupId = $categorySetup->getAttributeGroupId($entityTypeId, $attributeSetId, $groups['menu']['code']);
        
        $attributes = $this->getAttributes();
        foreach($attributes as $attributeCode => $attributeProp)
        {
            $categorySetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, $attributeCode, $attributeProp);
            $categorySetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $menuAttributeGroupId,
                $attributeCode,
                $attributeProp['sort_order']
            );
        }
    }

    protected function getAttributes()
    {
        $attributes = [

            'umm_dd_type' => [
                'group'                 => 'Menu',
                'label'                 => 'Submenu Type',
                'note'                  => 'If category has subcategories, choose how subcategories should be displayed. For details refer to the user guide, chapter: 13. Menu',

                'backend'               => '',
                'type'                  => 'varchar',
                'frontend'              => '',
                'input'                 => 'select',
                'source'                => 'Infortis\UltraMegamenu\Model\Category\Attribute\Source\Dropdown\Type',

                'user_defined'          => true,
                'required'              => false,
                'visible'               => true,
                'searchable'            => false,
                'filterable'            => false,
                'comparable'            => false,
                'visible_on_front'      => true,
                'wysiwyg_enabled'       => false,
                'is_html_allowed_on_front' => false,
                'global'                => ScopedAttributeInterface::SCOPE_STORE,
                'sort_order'            => 10,
            ],

            'umm_dd_width' => [
                'group'                 => 'Menu',
                'label'                 => 'Drop-down Width',
                'note'                  => "Override default width of the drop-down box. Enter value in pixels, e.g. 150px, or as a percentage of the containing block's width, e.g. 200%.",

                'backend'               => '',
                'type'                  => 'varchar',
                'frontend'              => '',
                'input'                 => 'text',

                'user_defined'          => true,
                'required'              => false,
                'visible'               => true,
                'searchable'            => false,
                'filterable'            => false,
                'comparable'            => false,
                'visible_on_front'      => true,
                'wysiwyg_enabled'       => false,
                'is_html_allowed_on_front' => false,
                'global'                => ScopedAttributeInterface::SCOPE_STORE,
                'sort_order'            => 20,
            ],

            'umm_dd_proportions' => [
                'group'                 => 'Menu',
                'label'                 => 'Drop-down Content Proportions',
                'note'                  => 'Proportions between 3 sections of drop-down: Left Block, Subcategories, Right Block. Enter 3 numbers separated by semicolon, e.g.: 3;6;3; Width is expressed in grid units (number between 0 and 12). Sum has to equal 12. See User Guide, chapter: 13. Menu',

                'backend'               => 'Infortis\UltraMegamenu\Model\Category\Attribute\Backend\Grid\Columns',
                'type'                  => 'varchar',
                'frontend'              => '',
                'input'                 => 'text',
                //'input_renderer'        => 'Infortis\UltraMegamenu\Block\Category\Attribute\Helper\Grid\Columns',

                'user_defined'          => true,
                'required'              => false,
                'visible'               => true,
                'searchable'            => false,
                'filterable'            => false,
                'comparable'            => false,
                'visible_on_front'      => true,
                'wysiwyg_enabled'       => false,
                'is_html_allowed_on_front' => false,
                'global'                => ScopedAttributeInterface::SCOPE_STORE,
                'sort_order'            => 30,
            ],

            'umm_dd_columns' => [
                'group'                 => 'Menu',
                'label'                 => 'Number of Columns With Subcategories',
                'note'                  => "Applicable only for categories with Submenu Type 'Mega drop-down'. For example, select 3 to display subcategories in three columns. Default value is 4.",

                'backend'               => '',
                'type'                  => 'int',
                'frontend'              => '',
                'input'                 => 'select',
                'source'                => 'Infortis\UltraMegamenu\Model\Category\Attribute\Source\Dropdown\Columns',

                'user_defined'          => true,
                'required'              => false,
                'visible'               => true,
                'searchable'            => false,
                'filterable'            => false,
                'comparable'            => false,
                'visible_on_front'      => true,
                'wysiwyg_enabled'       => false,
                'is_html_allowed_on_front' => false,
                'global'                => ScopedAttributeInterface::SCOPE_STORE,
                'sort_order'            => 40,
            ],

            'umm_dd_block_top' => [
                'group'                 => 'Menu',
                'label'                 => 'Top Block',
                'note'                  => '',

                'type'                  => 'text',
                'frontend'              => '',
                'input'                 => 'textarea',

                'user_defined'          => true,
                'required'              => false,
                'visible'               => true,
                'searchable'            => false,
                'filterable'            => false,
                'comparable'            => false,
                'visible_on_front'      => true,
                'wysiwyg_enabled'       => true,
                'is_html_allowed_on_front' => true,
                'global'                => ScopedAttributeInterface::SCOPE_STORE,
                'sort_order'            => 300,
            ],

            'umm_dd_block_left' => [
                'group'                 => 'Menu',
                'label'                 => 'Left Block',
                'note'                  => '',

                'type'                  => 'text',
                'frontend'              => '',
                'input'                 => 'textarea',

                'user_defined'          => true,
                'required'              => false,
                'visible'               => true,
                'searchable'            => false,
                'filterable'            => false,
                'comparable'            => false,
                'visible_on_front'      => true,
                'wysiwyg_enabled'       => true,
                'is_html_allowed_on_front' => true,
                'global'                => ScopedAttributeInterface::SCOPE_STORE,
                'sort_order'            => 310,
            ],

            'umm_dd_block_right' => [
                'group'                 => 'Menu',
                'label'                 => 'Right Block',
                'note'                  => '',

                'type'                  => 'text',
                'frontend'              => '',
                'input'                 => 'textarea',

                'user_defined'          => true,
                'required'              => false,
                'visible'               => true,
                'searchable'            => false,
                'filterable'            => false,
                'comparable'            => false,
                'visible_on_front'      => true,
                'wysiwyg_enabled'       => true,
                'is_html_allowed_on_front' => true,
                'global'                => ScopedAttributeInterface::SCOPE_STORE,
                'sort_order'            => 320,
            ],

            'umm_dd_block_bottom' => [
                'group'                 => 'Menu',
                'label'                 => 'Bottom Block',
                'note'                  => '',

                'type'                  => 'text',
                'frontend'              => '',
                'input'                 => 'textarea',

                'user_defined'          => true,
                'required'              => false,
                'visible'               => true,
                'searchable'            => false,
                'filterable'            => false,
                'comparable'            => false,
                'visible_on_front'      => true,
                'wysiwyg_enabled'       => true,
                'is_html_allowed_on_front' => true,
                'global'                => ScopedAttributeInterface::SCOPE_STORE,
                'sort_order'            => 330,
            ],

            'umm_cat_label' => [
                'group'             => 'Menu',
                'label'             => 'Category Label',
                'note'              => "Labels can be defined in menu settings.",

                'backend'           => '',
                'type'              => 'varchar',
                'frontend'          => '',
                'input'             => 'select',
                'source'            => 'Infortis\UltraMegamenu\Model\Category\Attribute\Source\Categorylabel',

                'user_defined'      => true,
                'required'          => false,
                'visible'           => true,
                'searchable'        => false,
                'filterable'        => false,
                'comparable'        => false,
                'visible_on_front'  => true,
                'wysiwyg_enabled'   => false,
                'is_html_allowed_on_front' => false,
                'global'            => ScopedAttributeInterface::SCOPE_STORE,
                'sort_order'        => 500,
        

            ],

            'umm_cat_target' => [
                'group'                 => 'Menu',
                'label'                 => 'Custom URL',
                'note'                  => "Enter hash (#) to make this category not clickable. To create a custom link (which will replace category link), enter custom URL path. Path will be appended to store's base URL to create a new link. Leave this field empty if no changes are needed.",

                'backend'               => '',
                'type'                  => 'varchar',
                'frontend'              => '',
                'input'                 => 'text',

                'user_defined'          => true,
                'required'              => false,
                'visible'               => true,
                'searchable'            => false,
                'filterable'            => false,
                'comparable'            => false,
                'visible_on_front'      => true,
                'wysiwyg_enabled'       => false,
                'is_html_allowed_on_front' => false,
                'global'                => ScopedAttributeInterface::SCOPE_STORE,
                'sort_order'            => 600,
            ],

        ];

        return $attributes;                     
    }
}
