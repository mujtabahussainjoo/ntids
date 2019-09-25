<?php
namespace Serole\Cashback\Block\Adminhtml\Usedpoints\Edit;

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
        $this->setId('usedpoints_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Used points Information'));
    }
}