<?php
namespace Serole\Itemsales\Block\Adminhtml\Sales;

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
        $this->backendSession->setSalesStoreidfilter($value);
        //echo $this->backendSession->getSalesStoreidfilter();
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

    protected function _prepareCollection()
    {
        $fromDate = date('Y-m-d',strtotime("-1 year"));

        $collection = $this->orderItems->getCollection()
            ->addExpressionFieldToSelect('fullname',
                'CONCAT({{customer_firstname}}, \' \', {{customer_lastname}})',
                array('customer_firstname' => 'order_table.customer_firstname',
                    'customer_lastname' => 'order_table.customer_lastname')
            )
            // ->addFieldToFilter('main_table.sku', array('in' => array('AWE','AWM','AWEOP201718','AWEPEAK201718','AWMOP201718','AWEPEAK201718')))
            ->addFieldToFilter('order_table.status', 'complete');

        $storeIdentifySessionVal = $this->backendSession->getSalesStoreidfilter();
        if($storeIdentifySessionVal){
            $collection->addFieldToFilter('order_table.store_id',$storeIdentifySessionVal);
        }

        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $customerAttr = $objectManager->get('\Magento\Eav\Model\Entity\Attribute')
                                      ->loadByCode('customer', 'memberno');
        $customerAttrId = $customerAttr->getAttributeId();

        $collection->getSelect()
            ->join(array('order_table'=>'sales_order'),
                'order_table.entity_id=main_table.order_id',
                array(
                    'order_table.store_id',
                    'order_number'=>'order_table.increment_id',
                    'order_created_at'=>'order_table.created_at',
                )
            );

        $collection->getSelect()
            ->joinLeft(array('cusMembernumTb'=>'customer_entity_varchar'),
                'order_table.customer_id = cusMembernumTb.entity_id AND cusMembernumTb.attribute_id ='.$customerAttrId,
                array('memberno'=>'cusMembernumTb.value')
            );

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addExportType($this->getUrl('itemsales/*/exportcsv', ['_current' => true]),__('CSV'));
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        $this->addColumn('store_id',
            [
                'header' => __('Store'),
                'index' => 'main_table.store_id',
                'type'  => 'store',
                'store_view'=> true,
                'filter_condition_callback' => array($this,'_filterStoreCondition'),
                'renderer' => 'Serole\Itemsales\Block\Adminhtml\Sales\Render\Store'
            ]
        );

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

        /*Member Number*/

        $this->addColumn(
            'memberno',
            [
                'header' => __('Member #'),
                'index' => 'memberno',
                'filter_index'=>'cusMembernumTb.value',
                'width'  => '90px',
                'align'  => 'right',
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
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku'
            ]
        );

        /*Serial Codes*/

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

        $this->addColumn(
            'created_at',
            [
                'header' => __('Date Created'),
                'index' => 'created_at',
                'type'	 => 'datetime',
                'filter_index'=>'main_table.created_at',
                'renderer' => 'Serole\Itemsales\Block\Adminhtml\DateFormat'
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