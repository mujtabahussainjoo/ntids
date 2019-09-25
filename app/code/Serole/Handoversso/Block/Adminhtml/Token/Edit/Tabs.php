<?php
namespace Serole\Handoversso\Block\Adminhtml\Token\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('token_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Token Information'));
    }
}