<?php
namespace Serole\Cashback\Block\Adminhtml\Usedpoints;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\Cashback\Model\usedpointsFactory
     */
    protected $_usedpointsFactory;

    /**
     * @var \Serole\Cashback\Model\Status
     */
    protected $_status;
	
	protected $_customer;
    protected $_customerFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\Cashback\Model\usedpointsFactory $usedpointsFactory
     * @param \Serole\Cashback\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\Cashback\Model\UsedpointsFactory $UsedpointsFactory,
        \Serole\Cashback\Model\Status $status,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_usedpointsFactory = $UsedpointsFactory;
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
        $collection = $this->_usedpointsFactory->create()->getCollection();
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
					'order_id',
					[
						'header' => __('Web Order Id'),
						'index' => 'order_id',
					]
				);
				
				$this->addColumn(
					'order_total',
					[
						'header' => __('Web Order Total'),
						'index' => 'order_total',
					]
				);
				
				$this->addColumn(
					'used_points',
					[
						'header' => __('Used Points'),
						'index' => 'used_points',
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

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('id');
        //$this->getMassactionBlock()->setTemplate('Serole_Cashback::usedpoints/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('usedpoints');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('cashback/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('cashback/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
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
     * @param \Serole\Cashback\Model\usedpoints|\Magento\Framework\Object $row
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