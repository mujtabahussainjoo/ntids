<?php
namespace Serole\Smsauth\Block\Adminhtml\Smsauth\Edit;

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
        $this->setId('smsauth_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Smsauth Information'));
    }
}