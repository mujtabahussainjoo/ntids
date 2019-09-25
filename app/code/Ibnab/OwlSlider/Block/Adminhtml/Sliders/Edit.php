<?php

namespace Ibnab\OwlSlider\Block\Adminhtml\Sliders;

/**
 * Slider block edit form container
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container {
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * @param Context $context
	 * @param array $data
	 */
	public function __construct(
		\Magento\Backend\Block\Widget\Context $context,
		\Magento\Framework\Registry $registry,
		array $data = []
	) {
		$this->_coreRegistry = $registry;
		parent::__construct($context, $data);
	}
	protected function _construct() {
		$this->_objectId = 'slider_id';
		$this->_blockGroup = 'Ibnab_OwlSlider';
		$this->_controller = 'adminhtml_sliders';

		parent::_construct();

		$this->buttonList->update('save', 'label', __('Save Slider'));
		$this->buttonList->update('delete', 'label', __('Delete'));
                
                if($this->getSlider()){
		if ($this->getSlider()->getId()) {
			$this->buttonList->add(
				'create_banner',
				[
					'label' => __('Create Banner'),
					'class' => 'add',
					'onclick' => 'setLocation(\'' . $this->getCreateBannerUrl() . '\')',
				],
				1
			);
		}}
                if($this->getSlider()){
		if ($this->getSlider()->getId()) {
			$this->buttonList->add(
				'sliders_preview',
				[
					'label' => __('Draft Preview'),
					'class' => 'add',
					//'onclick' => 'openBannerPopupWindow(\'' . $this->getCreateBannerUrl() . '\')',
				],
				1
			);
		}}

		$this->buttonList->add(
			'save_and_continue',
			[
				'label' => __('Save and Continue Edit'),
				'class' => 'save',
				'data_attribute' => [
					'mage-init' => [
						'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
					],
				],
			],
			10
		);

	}

	public function getSlider() {
		return $this->_coreRegistry->registry('owlslider_slider');
	}

	/**
	 * Add elements in layout
	 *
	 * @return $this
	 */
	protected function _prepareLayout() {

		return parent::_prepareLayout();
	}

	/**
	 * Retrieve the save and continue edit Url.
	 *
	 * @return string
	 */
	protected function _getSaveAndContinueUrl() {
		return $this->getUrl(
			'*/*/save',
			['_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}']
		);
	}

	public function getCreateBannerUrl() {
		return $this->getUrl('*/banners/new', ['current_slider_id' => $this->getSlider()->getId()]);
	}
}
