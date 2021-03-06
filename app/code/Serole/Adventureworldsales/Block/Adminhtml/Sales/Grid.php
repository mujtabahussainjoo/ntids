<?php
namespace Serole\Adventureworldsales\Block\Adminhtml\Sales;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $moduleManager;


    protected $_sageintegrationFactory;


    protected $_status;


    protected $orderFactory;


    protected $orderItems;


    protected $backendSession;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\Order $orderFactory,
        \Magento\Sales\Model\Order\Item $orderItems,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->backendSession = $backendSession;
        $this->orderFactory = $orderFactory;
        $this->orderItems = $orderItems;
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
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }
	
	public function getProdSkus()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		$productCollectionFactory = $objectManager->get('\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
		 
		$collection = $productCollectionFactory->create();
		$collection->addAttributeToSelect('*');
		$collection->addAttributeToFilter('supplier_code', 'ADV');
		//echo $collection->getSelect();
		$skus = array();
		if(count($collection) > 0)
		{
			foreach ($collection as $product) {
				$skus[] = $product->getSku();
			}
		}
		if(!empty($skus))
			return $skus;
		else
			return false;
	}

    protected function _prepareCollection()
    {
        $fromDate = date('Y-m-d',strtotime("-1 year"));
		
		$skus = $this->getProdSkus();

        $collection = $this->orderItems->getCollection()
            ->addExpressionFieldToSelect('fullname',
                                         'CONCAT({{customer_firstname}}, \' \', {{customer_lastname}})',
                                         array('customer_firstname' => 'order_table.customer_firstname',
                                               'customer_lastname' => 'order_table.customer_lastname')
                                        )
           // ->addFieldToFilter('main_table.sku', array('in' => array('AWE','AWM','AWEOP201718','AWEPEAK201718','AWMOP201718','AWEPEAK201718')))
            ->addFieldToFilter('order_table.status', 'complete');

        $collection->addAttributeToFilter('order_table.created_at', array(
            'from' => $fromDate,
            'date' => true,
        ));
		
		if($skus)
		{
		  $collection->addFieldToFilter('main_table.sku', array('in' => $skus));
		}
		else
			$collection->addFieldToFilter('main_table.sku', array('in' => array('NA')));

        $collection->getSelect()
            ->join(array('order_table'=>'sales_order'),
                'order_table.entity_id=main_table.order_id',
                array(
                    'order_table.store_id',
                    'order_number'=>'order_table.increment_id',
                    'order_created_at'=>'order_table.created_at',
                )
            );
//echo $collection->getSelect();
        //echo "<pre>"; print_r($collection->getData());

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addExportType($this->getUrl('adventureworldsales/*/exportcsv', ['_current' => true]),__('CSV'));
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        $this->addColumn(
            'order_number',
            [
                'header' => __('Order #'),
                'index' => 'order_number',
                'filter_index'=>'order_table.increment_id',
                'width'  => '90px',
                'align'  => 'right',
            ]
        );


        $this->addColumn(
            'created_at',
            [
                'header' => __('Date Created'),
                'index' => 'created_at',
                'type'	 => 'datetime',
            ]
        );


        $this->addColumn(
            'fullname',
            [
                'header' => __('Customer Name'),
                'index' => 'fullname',
                'filter_condition_callback' => array($this, '_customerNameFilter'),
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Description'),
                'index' => 'name'
            ]
        );

        $this->addColumn(
            'qty_invoiced',
            [
                'header' => __('Quantity'),
                'index' => 'qty_invoiced'
            ]
        );

        return parent::_prepareColumns();
    }

    protected function _customerNameFilter($collection, $column){
        $condition = $column->getFilter()->getCondition();
        $collection->getSelect()->where("CONCAT(order_table.customer_firstname,' ',order_table.customer_lastname) LIKE {$condition['like']} ");
        return $this;
    }

}