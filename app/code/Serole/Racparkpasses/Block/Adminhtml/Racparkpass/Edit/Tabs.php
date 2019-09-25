<?php
namespace Serole\Racparkpasses\Block\Adminhtml\Racparkpass\Edit;

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
        $this->setId('racparkpass_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Racparkpass Information'));
    }
}