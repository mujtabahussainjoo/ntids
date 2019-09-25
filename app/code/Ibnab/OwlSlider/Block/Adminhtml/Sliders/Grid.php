<?php

namespace Ibnab\OwlSlider\Block\Adminhtml\Sliders;

use Ibnab\OwlSlider\Model\Source\Status;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {



	/**
	 * Registry object
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * @param \Magento\Backend\Block\Template\Context     $context            
	 * @param \Magento\Backend\Helper\Data                $backendHelper      
	 * @param \Ibnab\MageUnslider\Model\SlidersFactory    $slidersFactory      
	 * @param \Magento\Framework\Registry                 $coreRegistry       
	 * @param array                                       $data               
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Backend\Helper\Data $backendHelper,
		\Magento\Framework\Registry $coreRegistry,
		array $data = []
	) {
		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context, $backendHelper, $data);
	}

       /**
       * @return void
       */
	protected function _construct() {
		parent::_construct();
		$this->setId('slidersGrid');
		$this->setDefaultSort('slider_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

      /**
      * @return $this
      */
	protected function _prepareCollection() {
		$collection = $this->_slidersFactory->create()->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * @return $this
	 */
	protected function _prepareColumns() {
		$this->addColumn(
			'id',
			[
				'header' => __('Slider ID'),
				'type' => 'number',
				'index' => 'id',
				'header_css_class' => 'col-id',
				'column_css_class' => 'col-id',
			]
		);
		$this->addColumn(
			'title',
			[
				'header' => __('Title'),
				'index' => 'title',
				'class' => 'xxx',
				'width' => '50px',
			]
		);

		$this->addColumn(
			'edit',
			[
				'header' => __('Edit'),
				'type' => 'action',
				'getter' => 'getId',
				'actions' => [
					[
						'caption' => __('Edit'),
						'url' => [
							'base' => '*/*/edit',
						],
						'field' => 'slider_id',
					],
				],
				'filter' => false,
				'sortable' => false,
				'index' => 'stores',
				'header_css_class' => 'col-action',
				'column_css_class' => 'col-action',
			]
		);
		$this->addExportType('*/*/exportCsv', __('CSV'));
		$this->addExportType('*/*/exportXml', __('XML'));
		$this->addExportType('*/*/exportExcel', __('Excel'));

		return parent::_prepareColumns();
	}

	/**
	 * @return $this
	 */
	protected function _prepareMassaction() {
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('slider');

		$this->getMassactionBlock()->addItem(
			'delete',
			[
				'label' => __('Delete'),
				'url' => $this->getUrl('bannerslideradmin/*/massDelete'),
				'confirm' => __('Are you sure?'),
			]
		);

		$statuses = Status::getStatus();

		array_unshift($statuses, ['label' => '', 'value' => '']);
		$this->getMassactionBlock()->addItem(
			'status',
			[
				'label' => __('Change status'),
				'url' => $this->getUrl('bannerslideradmin/*/massStatus', ['_current' => true]),
				'additional' => [
					'visibility' => [
						'name' => 'status',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => __('Status'),
						'values' => $statuses,
					],
				],
			]
		);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGridUrl() {
		return $this->getUrl('*/*/grid', array('_current' => true));
	}
	public function getRowUrl($row) {
		return $this->getUrl(
			'*/*/edit',
			array('id' => $row->getId())
		);
	}
}
