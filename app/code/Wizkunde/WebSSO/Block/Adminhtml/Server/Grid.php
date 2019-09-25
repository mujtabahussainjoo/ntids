<?php

namespace Wizkunde\WebSSO\Block\Adminhtml\Server;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Wizkunde_WebSSO';
        $this->_controller = 'adminhtml_server';
        $this->_headerText = __('Servers');
        $this->_addButtonLabel = __('Add New Server');
        parent::_construct();
        $this->buttonList->add(
            'server_apply',
            [
                'label' => __('Server'),
                'onclick' => "location.href='" . $this->getUrl('sso/*/applyServer') . "'",
                'class' => 'apply'
            ]
        );
    }
}
