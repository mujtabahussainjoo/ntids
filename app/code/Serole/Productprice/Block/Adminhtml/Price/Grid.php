<?php
namespace Serole\Productprice\Block\Adminhtml\Price;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $moduleManager;


    protected $_sageintegrationFactory;


    protected $_status;


    protected $prodcutFactory;


    protected $backendSession;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $prodcutFactory,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->backendSession = $backendSession;
        $this->prodcutFactory = $prodcutFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _filterStoreCondition($collection, $column){


        if (!$value = $column->getFilter()->getValue()) {
            $this->backendSession->setStoreidfilter('');
            return;
        }

        $this->backendSession->setStoreidfilter($value);

        if ($value) {
            $this->getCollection()->addStoreFilter($value);
            $this->getCollection()->joinAttribute('special_price', 'catalog_product/special_price', 'entity_id', null, 'left', $value);
            $this->getCollection()->joinAttribute('msrp', 'catalog_product/msrp', 'entity_id', null, 'left', $value);
            $this->getCollection()->joinAttribute('subsidy', 'catalog_product/subsidy', 'entity_id', null, 'left', $value);
            $this->getCollection()->joinAttribute('name','catalog_product/name','entity_id', null, 'inner', $value);
            //$this->getCollection()->joinAttribute('custom_name','catalog_product/name','entity_id',null,'inner',$value);
            $this->getCollection()->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $value);
            //$this->getCollection()->joinAttribute('store_id','catalog_product/store_id', 'entity_id', null, 'inner', $value);
            $this->getCollection()->joinAttribute('visibility','catalog_product/visibility','entity_id', null, 'inner', $value);
            //$this->getCollection()->joinAttribute('price','catalog_product/price','entity_id', null, 'left', $value );
        }
        return $this;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    protected function _prepareCollection()
    {
        $collection = $this->prodcutFactory->create()->getCollection()
         ->addAttributeToSelect('*')
         ->addAttributeToSelect('name')
         ->addAttributeToSelect('attribute_set_id')
         ->addAttributeToSelect('type_id');

        $storeIdentifySessionVal = $this->backendSession->getStoreidfilter();
        if($storeIdentifySessionVal){
           $collection->setStore($storeIdentifySessionVal);
        }
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn(
            'store_id',
            [
                'header' => __('Store View'),
                'index' => 'store_id',
                'type'  => 'store',
                'store_view' => true,
				'store_all' => true,
                'filter_condition_callback' => array($this,'_filterStoreCondition'),
                'renderer' => 'Serole\Productprice\Block\Adminhtml\Price\Render\Store',
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',

            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
            ]
        );

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'index' => 'price',
                'type'  => 'currency',
            ]
        );

        $this->addColumn(
            'special_price',
            [
                'header' => __('Special Price'),
                'index' => 'special_price',
                'type'  => 'currency',
            ]
        );

        $this->addColumn(
            'subsidy',
            [
                'header' => __('Subsidy'),
                'index' => 'subsidy',
                //'type'  => 'currency',
                'class' => 'xxx'
            ]
        );
/*
        $this->addColumn(
            'msrp',
            [
                'header' => __('RRP Price'),
                'index' => 'msrp',
                //'type'  => 'currency',
            ]
        );
*/
		$this->addExportType($this->getUrl('productprice/*/exportallcsv', ['_current' => true]),__('All StoreCSV'));
        $this->addExportType($this->getUrl('productprice/*/exportcsv', ['_current' => true]),__('CSV'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

 }