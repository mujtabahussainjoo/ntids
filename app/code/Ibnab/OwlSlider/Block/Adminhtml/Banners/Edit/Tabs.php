<?php

namespace Ibnab\OwlSlider\Block\Adminhtml\Banners\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs {
	protected function _construct() {
		parent::_construct();
		$this->setId('banners_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(__('Banners Information'));
	}
}
