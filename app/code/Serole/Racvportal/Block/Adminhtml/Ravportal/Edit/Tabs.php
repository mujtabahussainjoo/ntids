<?php
namespace Serole\Racvportal\Block\Adminhtml\Ravportal\Edit;

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
        $this->setId('ravportal_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Ravportal Information'));
    }
}