<?php
namespace Serole\Cashback\Block\Adminhtml\Carddetails\Edit;

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
        $this->setId('carddetails_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Carddetails Information'));
    }
}