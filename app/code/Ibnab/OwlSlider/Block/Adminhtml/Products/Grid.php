<?php

namespace Ibnab\OwlSlider\Block\Adminhtml\Products;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Ibnab\OwlSlider\Model\ProductSliderFactory
     */
    protected $_productsSlider;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Ibnab\OwlSlider\Model\ProductSliderFactory $productsSliderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Ibnab\OwlSlider\Model\SliderProductFactory $productsSliderFactory, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Backend\Helper\Data $helper, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\App\ResourceConnection $resource, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_productsSlider = $productsSliderFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_resource = $resource;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        parent::__construct($context, $helper, $data);
    }

    /**
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('ProducsSliderGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('slider_id')) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
    }

    /**
     * Retrieve product slider object
     *
     * @return \JakeSharp\Productslider\Model\Productslider
     */
    public function getSlider() {
        return $this->_coreRegistry->registry('product_slider');
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection() {


        $collection = $this->_productFactory->create()->getCollection();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('price')
                ->addStoreFilter(
                        $this->getRequest()->getParam('store')
        );
        /*
          ->joinField(
          'position', 'js_productslider_product', 'position', 'product_id=entity_id', 'slider_id=' . (int) $this->getRequest()->getParam('id', 0), 'left'
          );
         */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns() {

        $this->addColumn(
                'in_products', [
            'type' => 'checkbox',
            'name' => 'in_products',
            'values' => $this->_getSelectedProducts(),
            'index' => 'entity_id',
            'header_css_class' => 'col-select col-massaction',
            'column_css_class' => 'col-select col-massaction'
                ]
        );

        $this->addColumn(
                'entity_id', [
            'header' => __('ID'),
            'sortable' => true,
            'index' => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
                ]
        );

        $this->addColumn(
                'name', [
            'header' => __('Name'),
            'index' => 'name'
        ]);

        $this->addColumn(
                'sku', [
            'header' => __('SKU'),
            'index' => 'sku'
        ]);

        $this->addColumn(
                'price', [
            'header' => __('Price'),
            'type' => 'currency',
            'currency_code' => (string) $this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            'index' => 'price'
                ]
        );

        $this->addColumn(
                'position', [
            'header' => __('Position'),
            'type' => 'number',
            'index' => 'position',
            'editable' => true
                ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     *
     * @return $this
     */
    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in slider flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getSelectedSliderProducts() {

        $productsliderFactory = $this->_productsSlider->create();
        $products = [];
        $slider_id = $this->getRequest()->getParam('slider_id');
        if (!isset($slider_id)) {
            return [];
        }
        $productsliderSelectedFactory = $productsliderFactory->getSelectedSliderProducts($slider_id);
        foreach ($productsliderSelectedFactory as $product) {
            if (!isset($products[$product->getId()])):
                $products[$product->getId()] = ['position' => $product->getPostion()];
            endif;
        }
        return $products;
    }

    /**
     * @return array|mixed
     */
    protected function _getSelectedProducts() {
        $products = $this->getProductsSlider();
        if ($products === null) {
            $products = array_keys($this->getSelectedSliderProducts());
        }
        return $products;
    }

    /**
     * Retrieve grid reload url
     *
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/productsgrid', ['_current' => true]);
    }

}
