<?php
namespace Serole\Sage\Block\Adminhtml\Sageintegration\Edit;

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
        $this->setId('sageintegration_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Sageintegration Information'));
    }
}