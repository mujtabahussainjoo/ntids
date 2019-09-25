<?php

namespace Ibnab\OwlSlider\Block\Adminhtml\Sliders\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs {
	protected function _construct() {
		parent::_construct();
		$this->setId('sliders_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(__('Sliders Information'));
	}
}
