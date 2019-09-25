<?php
namespace Serole\Cashback\Block\Adminhtml\Carddetails;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\Cashback\Model\carddetailsFactory
     */
    protected $_carddetailsFactory;

    /**
     * @var \Serole\Cashback\Model\Status
     */
    protected $_status;
	
	protected $_customer;
    protected $_customerFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\Cashback\Model\carddetailsFactory $carddetailsFactory
     * @param \Serole\Cashback\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\Cashback\Model\CarddetailsFactory $CarddetailsFactory,
        \Serole\Cashback\Model\Status $status,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_carddetailsFactory = $CarddetailsFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
		$this->_customerFactory = $customerFactory;
        $this->_customer = $customers;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
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

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
				
        $collection = $this->_carddetailsFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }
	
	function getCustomer()
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
							'card_type',
							[
								'header' => __('Card Type'),
								'index' => 'card_type',
								'type' => 'options',
								'options' => \Serole\Cashback\Block\Adminhtml\Carddetails\Grid::getOptionArray13()
							]
						);
						
						
				$this->addColumn(
					'owner_name',
					[
						'header' => __('Card Holder Name'),
						'index' => 'owner_name',
					]
				);
				
				$this->addColumn(
					'card_no',
					[
						'header' => __('Card No'),
						'index' => 'card_no',
					]
				);
				
				
				$this->addColumn(
					'issuing_bank',
					[
						'header' => __('Issuing Bank'),
						'index' => 'issuing_bank',
					]
				);
				
						
						$this->addColumn(
							'verified',
							[
								'header' => __('Verified'),
								'index' => 'verified',
								'type' => 'options',
								'options' => \Serole\Cashback\Block\Adminhtml\Carddetails\Grid::getOptionArray16()
							]
						);
						
						
						
						$this->addColumn(
							'status',
							[
								'header' => __('Status'),
								'index' => 'status',
								'type' => 'options',
								'options' => \Serole\Cashback\Block\Adminhtml\Carddetails\Grid::getOptionArray17()
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

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('id');
        //$this->getMassactionBlock()->setTemplate('Serole_Cashback::carddetails/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('carddetails');

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
     * @param \Serole\Cashback\Model\carddetails|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'cashback/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	
		static public function getOptionArray13()
		{
            $data_array=array(); 
			$data_array['visa']='Visa';
			$data_array['mastercard']='Mastercard';
			$data_array['americanexpress']='American Express';
            return($data_array);
		}
		static public function getValueArray13()
		{
            $data_array=array();
			foreach(\Serole\Cashback\Block\Adminhtml\Carddetails\Grid::getOptionArray13() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray16()
		{
            $data_array=array(); 
			$data_array[1]='Yes';
			$data_array[0]='No';
            return($data_array);
		}
		static public function getValueArray16()
		{
            $data_array=array();
			foreach(\Serole\Cashback\Block\Adminhtml\Carddetails\Grid::getOptionArray16() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray17()
		{
            $data_array=array(); 
			$data_array[1]='Active';
			$data_array[0]='Inactive';
            return($data_array);
		}
		static public function getValueArray17()
		{
            $data_array=array();
			foreach(\Serole\Cashback\Block\Adminhtml\Carddetails\Grid::getOptionArray17() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}