<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Attributes;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class Grid extends Extended
{
    /** @var \Magento\Framework\Registry|null $_coreRegistry */
    protected $_coreRegistry = null;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $_collectionFactory */
    protected $_collectionFactory;

    /** @var \Magento\Config\Model\Config\Source\Yesno $yesno */
    protected $yesno;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        $this->yesno = $yesno;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('amrolepremissions_product_attributes_grid');
        $this->setDefaultSort('attribute_id');
        $this->setUseAjax(true);
        if ($this->getRule() && $this->getRule()->getId()) {
            $this->setDefaultFilter(['in_attributes' => 1]);
        }
    }

    /**
     * @return \Amasty\Rolepermissions\Model\Rule
     */
    public function getRule()
    {
        return $this->_coreRegistry->registry('amrolepermissions_current_rule');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->_coreRegistry->register('its_amrolepermissions', 1, true);
        $collection = $this->_collectionFactory->create()->addVisibleFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_attributes',
            [
                'type' => 'checkbox',
                'name' => 'in_attributes',
                'values' => $this->_getSelectedAttributes(),
                'align' => 'center',
                'index' => 'attribute_id',
                'column_css_class' => 'col-select',
                'header_css_class' => 'col-select',
                'preserveSelectionsOnFilter' => true,
                'preserve_selections_on_filter' => true
            ]
        );

        $this->addColumn(
            'is_global',
            [
                'header' => __('Scope'),
                'sortable' => true,
                'index' => 'is_global',
                'type' => 'options',
                'options' => [
                    ScopedAttributeInterface::SCOPE_STORE => __('Store View'),
                    ScopedAttributeInterface::SCOPE_WEBSITE => __('Web Site'),
                    ScopedAttributeInterface::SCOPE_GLOBAL => __('Global'),
                ],
                'align' => 'center'
            ]
        );

        $this->addColumn(
            'attribute_code',
            [
                'header' => __('Attribute Code'),
                'sortable' => true,
                'index' => 'attribute_code',
                'column_css_class' => 'col-id',
                'header_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'frontend_label',
            [
                'header' => __('Default Label'),
                'index' => 'frontend_label',
                'column_css_class' => 'col-name',
                'header_css_class' => 'col-name',
            ]
        );

        $this->addColumn(
            'is_required',
            [
                'header' => __('Required'),
                'sortable' => true,
                'index' => 'is_required',
                'type' => 'options',
                'options' => $this->yesno->toArray(),
                'header_css_class' => 'col-required',
                'column_css_class' => 'col-required'
            ]
        );

        $this->addColumn(
            'is_user_defined',
            [
                'header' => __('System'),
                'sortable' => true,
                'index' => 'is_user_defined',
                'type' => 'options',
                'options' => [
                    '0' => __('Yes'), // intended reverted use
                    '1' => __('No'), // intended reverted use
                ],
                'header_css_class' => 'col-system',
                'column_css_class' => 'col-system'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData(
            'grid_url'
        ) ? $this->_getData(
            'grid_url'
        ) : $this->getUrl(
            'amasty_rolepermissions/product/attributesGrid',
            ['_current' => true]
        );
    }

    protected function _getSelectedAttributes()
    {
        $attributes = $this->getAllowedAttributes();
        if (!is_array($attributes)) {
            $attributes = $this->getSelectedRuleAttributes();
        }
        return $attributes;
    }

    public function getSelectedRuleAttributes()
    {
        if (!$this->getRule()->getAttributes()) {
            return [];
        }

        return $this->getRule()->getAttributes();
    }
}
