<?php
namespace Serole\Cashback\Block\Adminhtml\Customerorder;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\Cashback\Model\customerorderFactory
     */
    protected $_customerorderFactory;

    /**
     * @var \Serole\Cashback\Model\Status
     */
    protected $_status;
	
	protected $_customer;
    protected $_customerFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\Cashback\Model\customerorderFactory $customerorderFactory
     * @param \Serole\Cashback\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\Cashback\Model\CustomerorderFactory $CustomerorderFactory,
        \Serole\Cashback\Model\Status $status,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_customerorderFactory = $CustomerorderFactory;
        $this->_status = $status;
		$this->_customerFactory = $customerFactory;
        $this->_customer = $customers;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_customerorderFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


		
			$this->addColumn(
							'customer_id',
							[
								'header' => __('Customer'),
								'index' => 'customer_id',
								'type' => 'options',
								'options' => $this->getCustomer()
							]
						);
				
				$this->addColumn(
					'merchant_id',
					[
						'header' => __('Merchant'),
						'index' => 'merchant_id',
						'type' => 'options',
						'options' => $this->getMerchant()
					]
				);
				
				$this->addColumn(
					'card_id',
					[
						'header' => __('Customer Card'),
						'index' => 'card_id',
						'type' => 'options',
						'options' => $this->getCards()
					]
				);
				
				$this->addColumn(
					'order_id',
					[
						'header' => __('Order Id'),
						'index' => 'order_id',
					]
				);
				
				$this->addColumn(
					'ship_to',
					[
						'header' => __('Shipping Address'),
						'index' => 'ship_to',
					]
				);
				
				$this->addColumn(
					'bill_to',
					[
						'header' => __('Billing Address'),
						'index' => 'bill_to',
					]
				);
				
				$this->addColumn(
					'products',
					[
						'header' => __('Products'),
						'index' => 'products',
					]
				);
				
				$this->addColumn(
					'order_total',
					[
						'header' => __('Order Total'),
						'index' => 'order_total',
					]
				);
				
				$this->addColumn(
					'rewards_points',
					[
						'header' => __('Earned Points'),
						'index' => 'rewards_points',
					]
				);
				
				$this->addColumn(
					'created_at',
					[
						'header' => __('Created On'),
						'index' => 'created_at',
						'type'      => 'datetime',
					]
				);
					
					


		
        //$this->addColumn(
            //'edit',
            //[
                //'header' => __('Edit'),
                //'type' => 'action',
                //'getter' => 'getId',
                //'actions' => [
                    //[
                        //'caption' => __('Edit'),
                        //'url' => [
                            //'base' => '*/*/edit'
                        //],
                        //'field' => 'id'
                    //]
                //],
                //'filter' => false,
                //'sortable' => false,
                //'index' => 'stores',
                //'header_css_class' => 'col-action',
                //'column_css_class' => 'col-action'
            //]
        //);
		

		
		   $this->addExportType($this->getUrl('cashback/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('cashback/*/exportExcel', ['_current' => true]),__('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	protected function getCustomer()
	{
		$customerCollection = $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("store_id", array("eq" => "72"))
                ->load();
		$customerData = array();
		foreach($customerCollection as $customer)
		{
			$customerData[$customer->getId()] = $customer->getFirstname()." ".$customer->getLastname()."(".$customer->getEmail().")";
		}
		return $customerData;
	}
	
	protected function getMerchant()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$cateinstance = $objectManager->create('Magento\Catalog\Model\CategoryFactory');
		$cateid = '188';
		$allcategoryproduct = $cateinstance->create()->setStoreId(72)->load($cateid)->getProductCollection()
		->addAttributeToSelect('*'); 
		$prodData = array();
		foreach($allcategoryproduct as $prod)
		{
			$prodData[$prod->getId()] = $prod->getName();
		}
		return $prodData;
	}
	
	protected function getCards()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$cardinstance = $objectManager->create('Serole\Cashback\Model\CarddetailsFactory');
		
		$allcards = $cardinstance->create()->getCollection(); 
		
		$cardData = array();
		
		foreach($allcards as $card)
		{
			$cardData[$card->getId()] = $card->getCardNo();
		}
		return $cardData;
	}

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('id');
        //$this->getMassactionBlock()->setTemplate('Serole_Cashback::customerorder/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('customerorder');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('cashback/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('cashback/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\Cashback\Model\customerorder|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'cashback/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	

}