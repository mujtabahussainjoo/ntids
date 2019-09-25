<?php
namespace Serole\Digitalglue\Block\Adminhtml\Digitalglue\Edit;

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
        $this->setId('digitalglue_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Digitalglue Information'));
    }
}