<?php
namespace Serole\MemberList\Block\Adminhtml\Memberlist\Edit;

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
        $this->setId('memberlist_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Memberlist Information'));
    }
}