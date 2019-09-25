<?php
namespace Serole_GiftMessage\Block\Adminhtml;

class Image extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_post';
        $this->_blockGroup = 'Serole_GiftMessage';
        $this->_headerText = __('Manage Gift Message');

        parent::_construct();

        if ($this->_isAllowedAction('Serole_GiftMessage::save')) {
            $this->buttonList->update('add', 'label', __('Add Gift Message'));
        } else {
            $this->buttonList->remove('add');
        }
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
