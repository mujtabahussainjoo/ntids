<?php
namespace Serole\Handoversso\Block\Adminhtml\Token;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\Handoversso\Model\tokenFactory
     */
    protected $_tokenFactory;

    /**
     * @var \Serole\Handoversso\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\Handoversso\Model\tokenFactory $tokenFactory
     * @param \Serole\Handoversso\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\Handoversso\Model\TokenFactory $TokenFactory,
        \Serole\Handoversso\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_tokenFactory = $TokenFactory;
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
        $collection = $this->_tokenFactory->create()->getCollection();
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
					'token',
					[
						'header' => __('Token'),
						'index' => 'token',
					]
				);
				
				$this->addColumn(
					'email',
					[
						'header' => __('Email'),
						'index' => 'email',
					]
				);
				
				$this->addColumn(
					'firstname',
					[
						'header' => __('First Name'),
						'index' => 'firstname',
					]
				);
				
				$this->addColumn(
					'lastname',
					[
						'header' => __('Last Name'),
						'index' => 'lastname',
					]
				);
				
				$this->addColumn(
					'ssoid',
					[
						'header' => __('Single Signon Id'),
						'index' => 'ssoid',
					]
				);
				
				$this->addColumn(
					'dob',
					[
						'header' => __('DOB'),
						'index' => 'dob',
					]
				);
				
						
				$this->addColumn(
					'status',
					[
						'header' => __('Status'),
						'index' => 'status',
						'type' => 'options',
						'options' => \Serole\Handoversso\Block\Adminhtml\Token\Grid::getOptionArray7()
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
					'updated_at',
					[
						'header' => __('Updated At'),
						'index' => 'updated_at',
						'type'      => 'datetime',
					]
				);
					
					


		

		
		   $this->addExportType($this->getUrl('handoversso/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('handoversso/*/exportExcel', ['_current' => true]),__('Excel XML'));

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
        //$this->getMassactionBlock()->setTemplate('Serole_Handoversso::token/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('token');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('handoversso/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('handoversso/*/massStatus', ['_current' => true]),
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
        return $this->getUrl('handoversso/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\Handoversso\Model\token|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		return '#';
    }

	
		static public function getOptionArray7()
		{
            $data_array=array(); 
			$data_array[1]='Enabled';
			$data_array[0]='Disabled';
            return($data_array);
		}
		static public function getValueArray7()
		{
            $data_array=array();
			foreach(\Serole\Handoversso\Block\Adminhtml\Token\Grid::getOptionArray7() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}