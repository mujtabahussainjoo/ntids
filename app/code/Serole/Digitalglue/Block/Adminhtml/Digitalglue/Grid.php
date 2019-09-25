<?php
namespace Serole\Digitalglue\Block\Adminhtml\Digitalglue;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\Digitalglue\Model\digitalglueFactory
     */
    protected $_digitalglueFactory;

    /**
     * @var \Serole\Digitalglue\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\Digitalglue\Model\digitalglueFactory $digitalglueFactory
     * @param \Serole\Digitalglue\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\Digitalglue\Model\DigitalglueFactory $DigitalglueFactory,
        \Serole\Digitalglue\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_digitalglueFactory = $DigitalglueFactory;
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
        $collection = $this->_digitalglueFactory->create()->getCollection();
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
					'referencenumber',
					[
						'header' => __('Order Id'),
						'index' => 'referencenumber',
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
					'magento_sku',
					[
						'header' => __('Magento SKU'),
						'index' => 'magento_sku',
					]
				);
				
				$this->addColumn(
					'quantity',
					[
						'header' => __('Quantity'),
						'index' => 'quantity',
					]
				);
				
				$this->addColumn(
					'statuscode',
					[
						'header' => __('Status Code'),
						'index' => 'statuscode',
					]
				);
				
				$this->addColumn(
					'statusmessage',
					[
						'header' => __('Status Message'),
						'index' => 'statusmessage',
					]
				);
				
				$this->addColumn(
					'receiptnumber',
					[
						'header' => __('Receipt Number'),
						'index' => 'receiptnumber',
					]
				);
				
				$this->addColumn(
					'amount',
					[
						'header' => __('Amount'),
						'index' => 'amount',
					]
				);
				
				$this->addColumn(
					'expirydate',
					[
						'header' => __('Expiry Date'),
						'index' => 'expirydate',
					]
				);
				
				$this->addColumn(
					'costperunit',
					[
						'header' => __('Cost Per Unit'),
						'index' => 'costperunit',
					]
				);
				
				$this->addColumn(
					'rrpperunit',
					[
						'header' => __('RRP Per Unit'),
						'index' => 'rrpperunit',
					]
				);
				
				$this->addColumn(
					'includesgst',
					[
						'header' => __('Includes Gst'),
						'index' => 'includesgst',
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
		

		
		   $this->addExportType($this->getUrl('digitalglue/*/exportCsv', ['_current' => true]),__('CSV'));

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
        //$this->getMassactionBlock()->setTemplate('Serole_Digitalglue::digitalglue/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('digitalglue');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('digitalglue/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('digitalglue/*/massStatus', ['_current' => true]),
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
        return $this->getUrl('digitalglue/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\Digitalglue\Model\digitalglue|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'digitalglue/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	

}