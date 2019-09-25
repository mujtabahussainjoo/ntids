<?php

namespace Ibnab\OwlSlider\Block\Adminhtml;

class Sliders extends \Magento\Backend\Block\Widget\Grid\Container {
	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function _construct() {

		$this->_controller = 'adminhtml_sliders';
		$this->_blockGroup = 'Ibnab_OwlSlider';
		$this->_headerText = __('Manage Sliders');
		$this->_addButtonLabel = __('Add New Slider');
		parent::_construct();
	}
}
