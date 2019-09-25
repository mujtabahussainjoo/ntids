<?php
namespace Serole\MemberList\Block\Adminhtml\Memberlist;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\MemberList\Model\memberlistFactory
     */
    protected $_memberlistFactory;

    /**
     * @var \Serole\MemberList\Model\Status
     */
    protected $_status;
	
	
	protected $_storeManager;
	
	protected $_scopeConfig;
	
	Protected $_helper;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\MemberList\Model\memberlistFactory $memberlistFactory
     * @param \Serole\MemberList\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\MemberList\Model\MemberlistFactory $MemberlistFactory,
        \Serole\MemberList\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConf,
		\Serole\MemberList\Helper\Data $helper,
        array $data = []
    ) {
		$this->_scopeConfig = $scopeConf;
        $this->_memberlistFactory = $MemberlistFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
		$this->_storeManager = $storeManager;
		$this->_helper = $helper;
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
		$accessibleWebsites = $this->_helper->getAccessibleWebsites();
		$accessibleWebsitesList = implode(",",$accessibleWebsites);
		
        $collection = $this->_memberlistFactory->create()->getCollection();
		$collection->addFieldToFilter('store', array('in' => $accessibleWebsitesList));
		
		//echo $collection->getSelect();
		
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
                'header' => __('id'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


		
				$this->addColumn(
					'member_number',
					[
						'header' => __('Member Number'),
						'index' => 'member_number',
					]
				);
				
				$this->addColumn(
					'first_name',
					[
						'header' => __('First Name'),
						'index' => 'first_name',
					]
				);
				
				$this->addColumn(
					'last_name',
					[
						'header' => __('Last Name'),
						'index' => 'last_name',
					]
				);
				
						
				$this->addColumn(
					'store',
					[
						'header' => __('Store'),
						'index' => 'store',
						'type' => 'options',
						'options' => $this->_helper->getStoreArray()
					]
				);
						
						
				$this->addColumn(
					'customer_group',
					[
						'header' => __('Customer Group'),
						'index' => 'customer_group',
					]
				);
				


		
        //$this->addColumn(
         //   'edit',
          //  [
          //      'header' => __('Edit'),
            //    'type' => 'action',
              //  'getter' => 'getId',
            //    'actions' => [
            //      [
            //          'caption' => __('Edit'),
            //          'url' => [
            //              'base' => '*/*/edit'
            //          ],
            //          'field' => 'id'
            //      ]
            //  ],
            //  'filter' => false,
            //  'sortable' => false,
            //  'index' => 'stores',
            //  'header_css_class' => 'col-action',
            //  'column_css_class' => 'col-action'
           // ]
        //);
		

		
		   $this->addExportType($this->getUrl('memberlist/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('memberlist/*/exportExcel', ['_current' => true]),__('Excel XML'));

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
        //$this->getMassactionBlock()->setTemplate('Serole_MemberList::memberlist/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('memberlist');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('memberlist/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('memberlist/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\MemberList\Model\memberlist|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'memberlist/*/edit',
            ['id' => $row->getId()]
        );
		
    }

}