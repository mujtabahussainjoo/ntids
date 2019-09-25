<?php
namespace Serole\Productattachment\Block\Adminhtml\Productattachment;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Serole\Productattachment\Model\productattachmentFactory
     */
    protected $_productattachmentFactory;

    /**
     * @var \Serole\Productattachment\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Serole\Productattachment\Model\productattachmentFactory $productattachmentFactory
     * @param \Serole\Productattachment\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Serole\Productattachment\Model\ProductattachmentFactory $ProductattachmentFactory,
        \Serole\Productattachment\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_productattachmentFactory = $ProductattachmentFactory;
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
        $collection = $this->_productattachmentFactory->create()->getCollection();
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
			'name',
			[
				'header' => __('Name'),
				'index' => 'name',
			]
		);
		
		$this->addColumn(
			'file',
			[
				'header' => __('File'),
				'index' => 'file',
			]
		);

        $this->addColumn(
            'sku',
            [
                'header' => __('Product SKU'),
				'filter' => false,
                'renderer' => 'Serole\Productattachment\Block\Adminhtml\Productattachment\Renderer\ProductList',

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
		

		
		   $this->addExportType($this->getUrl('productattachment/*/exportCsv', ['_current' => true]),__('CSV'));

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
        //$this->getMassactionBlock()->setTemplate('Serole_Productattachment::productattachment/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('productattachment');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('productattachment/*/massDelete'),
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
        return $this->getUrl('productattachment/*/index', ['_current' => true]);
    }

    /**
     * @param \Serole\Productattachment\Model\productattachment|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'productattachment/*/edit',
            ['id' => $row->getId()]
        );
		
    }
	
	protected function _filterCollection($collection, $column)
	{
		echo $value = trim($column->getFilter()->getValue());
	 exit;
		$this->getCollection()->getSelect()->where(
			// do filter 
		);
	 
		return $this;
	}

	

}