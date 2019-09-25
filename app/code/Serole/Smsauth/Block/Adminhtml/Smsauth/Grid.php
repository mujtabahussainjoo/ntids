<?php
namespace Serole\Smsauth\Block\Adminhtml\Smsauth;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\Smsauth\Model\smsauthFactory
     */
    protected $_smsauthFactory;

    /**
     * @var \Serole\Smsauth\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\Smsauth\Model\smsauthFactory $smsauthFactory
     * @param \Serole\Smsauth\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\Smsauth\Model\SmsauthFactory $SmsauthFactory,
        \Serole\Smsauth\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_smsauthFactory = $SmsauthFactory;
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
        $collection = $this->_smsauthFactory->create()->getCollection();
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
						'header' => __('Customer Email'),
						'index' => 'customer_email',
					]
				);
				
				$this->addColumn(
					'customer_phone',
					[
						'header' => __('Customer Phone'),
						'index' => 'customer_phone',
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
					'quote_id',
					[
						'header' => __('Quote Id'),
						'index' => 'quote_id',
					]
				);
				
				$this->addColumn(
					'cart_total',
					[
						'header' => __('Cart Total'),
						'index' => 'cart_total',
					]
				);
				
				$this->addColumn(
					'cart_total_qty',
					[
						'header' => __('Total Qty in Cart'),
						'index' => 'cart_total_qty',
					]
				);
				
				$this->addColumn(
					'cart_details',
					[
						'header' => __('Cart Details'),
						'index' => 'cart_details',
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
					
					
				
				


		

		
		   $this->addExportType($this->getUrl('smsauth/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('smsauth/*/exportExcel', ['_current' => true]),__('Excel XML'));

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
        //$this->getMassactionBlock()->setTemplate('Serole_Smsauth::smsauth/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('smsauth');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('smsauth/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('smsauth/*/massStatus', ['_current' => true]),
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
        return $this->getUrl('smsauth/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\Smsauth\Model\smsauth|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		return '#';
    }

	

}