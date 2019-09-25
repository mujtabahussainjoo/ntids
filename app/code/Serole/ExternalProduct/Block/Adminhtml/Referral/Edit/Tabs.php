<?php
namespace Serole\ExternalProduct\Block\Adminhtml\Referral\Edit;

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
        $this->setId('referral_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Referral Information'));
    }
}