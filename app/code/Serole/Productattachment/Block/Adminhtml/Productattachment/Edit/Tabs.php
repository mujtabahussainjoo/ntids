<?php
namespace Serole\Productattachment\Block\Adminhtml\Productattachment\Edit;

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
        $this->setId('productattachment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Productattachment Information'));
    }
}