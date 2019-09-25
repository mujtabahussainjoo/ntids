<?php
namespace Serole\Handoversso\Block\Adminhtml\Signature\Edit;

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
        $this->setId('signature_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Signature Information'));
    }
}