<?php
namespace Serole\Racparkpasses\Block\Adminhtml\Racparkpass;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\Racparkpasses\Model\racparkpassFactory
     */
    protected $_racparkpassFactory;

    /**
     * @var \Serole\Racparkpasses\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\Racparkpasses\Model\racparkpassFactory $racparkpassFactory
     * @param \Serole\Racparkpasses\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\Racparkpasses\Model\RacparkpassFactory $RacparkpassFactory,
        \Serole\Racparkpasses\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_racparkpassFactory = $RacparkpassFactory;
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
        $this->setDefaultSort('updated_at');
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
        $collection = $this->_racparkpassFactory->create()->getCollection()
		                ->addFieldToSelect('*')
						->addExpressionFieldToSelect(
							'fullname',
							'CONCAT({{customer_firstname}}, \' \', {{customer_lastname}})',
							array('customer_firstname' => 'billing.firstname', 
									'customer_lastname' => 'billing.lastname')
							)														
						->addExpressionFieldToSelect(
							'firstname',
							'{{customer_firstname}}',
							array('customer_firstname' => 'billing.firstname')
							)	
                        ->addExpressionFieldToSelect(
							'billing_address',
							'CONCAT({{billing_street}}, \', \', {{billing_city}}, \', \', {{billing_region}}, \' \', {{billing_postcode}} )',
							array('billing_street' => 'billing.street', 
								'billing_city' => 'billing.city',
								'billing_region' => 'billing.region',
								'billing_postcode' => 'billing.postcode')
							)
							
						->addFieldToFilter('order_table.status', 'complete')
						->addFieldToFilter('sku', array('in'=>array('RACHP','RACCONAAPP','RACAPP')));
						
                    $collection->getSelect()
        					->join(array('order_table'=>'sales_order'),
								'order_table.entity_id=main_table.order_id',
								array(
									'order_table.store_id',
									'order_number'=>'order_table.increment_id',
									'order_created_at'=>'order_table.created_at',
									'order_updated_at'=>'order_table.updated_at',
								)
							);
        $collection->getSelect()
        					->join(
	        					array('billing'=> 'sales_order_address'),
    							'main_table.order_id = billing.parent_id',
								array('street', 'city', 'region', 'postcode', 'billing_email'=>'email', 'billing.firstname', 'billing.lastname')
							)
							->where("billing.address_type = 'billing'");
							
		$collection->getSelect()							
							->joinLeft(
								array('newsletter' => 'newsletter_subscriber'),
								'order_table.customer_id = newsletter.customer_id',
								array('is_subscribed' => new \Zend_Db_Expr('IF(newsletter.subscriber_status = 1, \'Yes\',\'No\')'),
								'subscribed_email' => new \Zend_Db_Expr('IF(newsletter.subscriber_status = 1, billing.email,\'\')'))
							);	
						
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
					'order_number',
					[
						'header' => __('Order #'),
						'index'  => 'order_number',
			            'filter_index'=>'order_table.increment_id',	
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
					
					
				$this->addColumn(
					'updated_at',
					[
						'header' => __('Last Update'),
						'index' => 'updated_at',
						'type'      => 'datetime',
					]
				);
					
					
				$this->addColumn(
					'fullname',
					[
						'header' => __('Customer Name'),
						'index'  => 'fullname',
			            'filter_condition_callback' => array($this, '_customerNameFilter'),
					]
				);
				
				$this->addColumn(
					'is_subscribed',
					[
						'header' => __('Is Subscribed'),
						'index' => 'is_subscribed',
						'filter'	=> false,
					]
				);
				
				$this->addColumn(
					'subscribed_email',
					[
						'header' => __('Subscribed Email'),
						'index' => 'subscribed_email',
						'filter'	=> false,
					]
				);
				
				$this->addColumn(
					'veh_reg_1',
					[
						'header' => __('Vehicle Registration 1'),
						'filter'	=> false,
						'index'		=> 'product_options',
						'renderer'  => 'Serole\Racparkpasses\Block\Adminhtml\Racparkpass\Renderer\Itemoption',
						'option_label'=>'Vehicle Registration Number'
					]
				);
				
				$this->addColumn(
					'veh_reg_2',
					[
						'header' => __('Vehicle Registration 2'),
						'filter'	=> false,
						'index'		=> 'product_options',
						'renderer'  => 'Serole\Racparkpasses\Block\Adminhtml\Racparkpass\Renderer\Itemoption',
						'option_label'=>'2nd Vehicle Registration Number'
						
					]
				);
				
				$this->addColumn(
					'name',
					[
						'header' => __('Description'),
						'index' => 'name',
					]
				);
				
				$this->addColumn(
					'price_incl_tax',
					[
						'header' => __('Unit Price'),
						'index' => 'price_incl_tax',
					]
				);
				
				$this->addColumn(
					'serial_codes',
					[
						'header' => __('Pass Number(s)'),
						'index' => 'serial_codes',
					]
				);
				
				$this->addColumn(
					'start_date',
					[
						'header' => __('Valid From Date'),
						'index'		=> 'product_options',
                        'filter'	=> false,
						'type'      => 'datetime',
						'renderer'  => 'Serole\Racparkpasses\Block\Adminhtml\Racparkpass\Renderer\Itemoption',
						'option_label'=>'Park Pass start date'
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
                        //'field' => 'item_id'
                    //]
                //],
                //'filter' => false,
                //'sortable' => false,
                //'index' => 'stores',
                //'header_css_class' => 'col-action',
                //'column_css_class' => 'col-action'
            //]
        //);
		

		
		   $this->addExportType($this->getUrl('racparkpasses/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('racparkpasses/*/exportExcel', ['_current' => true]),__('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('racparkpasses/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\Racparkpasses\Model\racparkpass|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'racparkpasses/*/edit',
            [
			'item_id' => $row->getId(),
			'order_number' => $row->getOrderNumber(),
			'fullname' => $row->getFullname()
			]
        );
		
    }
	
	protected function _customerNameFilter($collection, $column){
		$condition = $column->getFilter()->getCondition();
		$collection->getSelect()->where("CONCAT(billing.firstname,' ',billing.lastname) LIKE {$condition['like']} ");
		return $this;
	}    

	

}