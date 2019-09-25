<?php
namespace Serole\Sage\Block\Adminhtml\Sageintegration;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\Sage\Model\sageintegrationFactory
     */
    protected $_sageintegrationFactory;

    /**
     * @var \Serole\Sage\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\Sage\Model\sageintegrationFactory $sageintegrationFactory
     * @param \Serole\Sage\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\Sage\Model\SageintegrationFactory $SageintegrationFactory,
        \Serole\Sage\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_sageintegrationFactory = $SageintegrationFactory;
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
        $collection = $this->_sageintegrationFactory->create()->getCollection();
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
					'orderid',
					[
						'header' => __('Order Id'),
						'index' => 'orderid',
					]
				);
				
				$this->addColumn(
					'sales_header',
					[
						'header' => __('Header(order)'),
						'index' => 'sales_header',
					]
				);
				
				$this->addColumn(
					'sales_details',
					[
						'header' => __('Items(order)'),
						'index' => 'sales_details',
					]
				);
				
				$this->addColumn(
					'sales_serialcode',
					[
						'header' => __('Serialcodes(order)'),
						'index' => 'sales_serialcode',
					]
				);
				
				$this->addColumn(
					'payment_receipt',
					[
						'header' => __('Payment(order)'),
						'index' => 'payment_receipt',
					]
				);
				
				$this->addColumn(
					'credit_memo_header',
					[
						'header' => __('Header(CM)'),
						'index' => 'credit_memo_header',
					]
				);
				
				$this->addColumn(
					'credit_memo_details',
					[
						'header' => __('Items(CM)'),
						'index' => 'credit_memo_details',
					]
				);
				
				$this->addColumn(
					'credit_memo_serialcode',
					[
						'header' => __('Serialcodes(CM)'),
						'index' => 'credit_memo_serialcode',
					]
				);
				
				$this->addColumn(
					'credit_memo_receipt',
					[
						'header' => __('Payment(CM)'),
						'index' => 'credit_memo_receipt',
					]
				);
				
				$this->addColumn(
					'created_at',
					[
						'header' => __('Created Date'),
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
		

		
		   $this->addExportType($this->getUrl('sage/*/exportCsv', ['_current' => true]),__('CSV'));

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
        //$this->getMassactionBlock()->setTemplate('Serole_Sage::sageintegration/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('sageintegration');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('sage/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );
		
		$this->getMassactionBlock()->addItem(
            'pushorder',
            [
                'label' => __('Push Orders to Sage'),
                'url' => $this->getUrl('sage/*/pushOrders'),
                'confirm' => __('Are you sure you want to push the order to Sage?')
            ]
        );
		
		$this->getMassactionBlock()->addItem(
            'pushcreditmemo',
            [
                'label' => __('Push Credit Memos to Sage'),
                'url' => $this->getUrl('sage/*/pushCreditmemo'),
                'confirm' => __('Are you sure you want to push the credit memo to Sage? If yes make sure you have pushed the order before.')
            ]
        );
    
        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sage/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\Sage\Model\sageintegration|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'sage/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	

}