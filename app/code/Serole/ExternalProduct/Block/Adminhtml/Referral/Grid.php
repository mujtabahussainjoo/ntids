<?php
namespace Serole\ExternalProduct\Block\Adminhtml\Referral;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\ExternalProduct\Model\referralFactory
     */
    protected $_referralFactory;

    /**
     * @var \Serole\ExternalProduct\Model\Status
     */
    protected $_status;
	
	
	protected $_storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\ExternalProduct\Model\referralFactory $referralFactory
     * @param \Serole\ExternalProduct\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\ExternalProduct\Model\ReferralFactory $ReferralFactory,
        \Serole\ExternalProduct\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_referralFactory = $ReferralFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
		$this->_storeManager = $storeManager;
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
        $collection = $this->_referralFactory->create()->getCollection();
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
					'customerid',
					[
						'header' => __('Customer Id'),
						'index' => 'customerid',
					]
				);
				
				$this->addColumn(
					'productid',
					[
						'header' => __('Product Id'),
						'index' => 'productid',
					]
				);
				
				$this->addColumn(
					'linkurl',
					[
						'header' => __('Link URL'),
						'index' => 'linkurl',
					]
				);
				
						
						$this->addColumn(
							'store',
							[
								'header' => __('Store'),
								'index' => 'store',
								'type' => 'options',
								'options' => $this->getStoreArray()
							]
						);
				
				$this->addColumn(
					'status',
					[
						'header' => __('Status'),
						'index' => 'status',
					]
				);
				
						
						
				$this->addColumn(
					'date_clicked',
					[
						'header' => __('Clicked date'),
						'index' => 'date_clicked',
						'type'      => 'datetime',
					]
				);
					
					


		

		
		   $this->addExportType($this->getUrl('externalproduct/*/exportCsv', ['_current' => true]),__('CSV'));

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
        //$this->getMassactionBlock()->setTemplate('Serole_ExternalProduct::referral/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('referral');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('externalproduct/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('externalproduct/*/massStatus', ['_current' => true]),
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
        return $this->getUrl('externalproduct/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\ExternalProduct\Model\referral|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		return '#';
    }

	
	static public function getOptionArray5()
	{
		$data_array=array(); 
		$data_array[0]='Option1';
		$data_array[1]='Option2';
		return($data_array);
	}
	static public function getValueArray5()
	{
		$data_array=array();
		foreach(\Serole\ExternalProduct\Block\Adminhtml\Referral\Grid::getOptionArray5() as $k=>$v){
		   $data_array[]=array('value'=>$k,'label'=>$v);		
		}
		return($data_array);

	}
	public function getStoreArray()
	{
		$data_array=array();
		
		$stores = $this->getStores();
		
		
		foreach($stores as $store)
		{
			$data_array[$store->getCode()] = $store->getName();
		}

		return($data_array);
	}
	
	
	protected function getStores() {
		
      return $this->_storeManager->getStores();
	
    }
		

}