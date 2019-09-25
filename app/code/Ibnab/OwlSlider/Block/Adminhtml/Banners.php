<?php

namespace Ibnab\OwlSlider\Block\Adminhtml;

class Banners extends \Magento\Backend\Block\Widget\Grid\Container {
	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function _construct() {

		$this->_controller = 'adminhtml_banners';
		$this->_blockGroup = 'Ibnab_OwlSlider';
		$this->_headerText = __('Manage Banners');
		$this->_addButtonLabel = __('Add New Banner');
		parent::_construct();
	}
}
