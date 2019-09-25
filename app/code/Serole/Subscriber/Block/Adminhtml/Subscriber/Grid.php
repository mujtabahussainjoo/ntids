<?php
namespace Serole\Subscriber\Block\Adminhtml\Subscriber;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\Subscriber\Model\subscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @var \Serole\Subscriber\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\Subscriber\Model\subscriberFactory $subscriberFactory
     * @param \Serole\Subscriber\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\Subscriber\Model\SubscriberFactory $SubscriberFactory,
        \Serole\Subscriber\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_subscriberFactory = $SubscriberFactory;
        $this->_status = $status;
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
        $collection = $this->_subscriberFactory->create()->getCollection();
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
						'header' => __('Customer Id'),
						'index' => 'customer_id',
					]
				);
				
				$this->addColumn(
					'customer_email',
					[
						'header' => __('Email'),
						'index' => 'customer_email',
					]
				);
				
				$this->addColumn(
					'customer_member',
					[
						'header' => __('Member No'),
						'index' => 'customer_member',
					]
				);
				
				$this->addColumn(
					'customer_first_name',
					[
						'header' => __('Customer Name'),
						'index' => 'customer_first_name',
					]
				);
				
				$this->addColumn(
					'customer_last_name',
					[
						'header' => __('Customer Last Name'),
						'index' => 'customer_last_name',
					]
				);
				
				$this->addColumn(
					'customer_phno',
					[
						'header' => __('Contact No'),
						'index' => 'customer_phno',
					]
				);
				
				$this->addColumn(
					'customer_address',
					[
						'header' => __('Address'),
						'index' => 'customer_address',
					]
				);
				
				$this->addColumn(
					'customer_postcode',
					[
						'header' => __('Postcode'),
						'index' => 'customer_postcode',
					]
				);
				
				$this->addColumn(
					'customer_state',
					[
						'header' => __('State'),
						'index' => 'customer_state',
					]
				);
				
				$this->addColumn(
					'suburb',
					[
						'header' => __('Suburb'),
						'index' => 'suburb',
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
					'export',
					[
						'header' => __('Export'),
						'index' => 'export',
					]
				);
				
				$this->addColumn(
					'opt_in',
					[
						'header' => __('Opt In'),
						'index' => 'opt_in',
					]
				);
				
				$this->addColumn(
					'store_id',
					[
						'header' => __('Store Id'),
						'index' => 'store_id',
					]
				);
				
				$this->addColumn(
					'created_at',
					[
						'header' => __('Created At'),
						'index' => 'created_at',
						'type'      => 'datetime',
					]
				);
					
					
				$this->addColumn(
					'updted_at',
					[
						'header' => __('Updated At'),
						'index' => 'updted_at',
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
		

		
		   $this->addExportType($this->getUrl('subscriber/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('subscriber/*/exportExcel', ['_current' => true]),__('Excel XML'));

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
        //$this->getMassactionBlock()->setTemplate('Serole_Subscriber::subscriber/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('subscriber');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('subscriber/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('subscriber/*/massStatus', ['_current' => true]),
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
        return $this->getUrl('subscriber/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\Subscriber\Model\subscriber|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'subscriber/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	

}