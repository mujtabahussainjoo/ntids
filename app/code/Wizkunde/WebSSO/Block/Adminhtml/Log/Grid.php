<?php

namespace Wizkunde\WebSSO\Block\Adminhtml\Log;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Wizkunde_WebSSO';
        $this->_controller = 'adminhtml_log';
        $this->_headerText = __('Audit Log');
        parent::_construct();

        $this->removeButton('add');

        $this->buttonList->add(
            'log_apply',
            [
                'label' => __('Log'),
                'onclick' => "location.href='" . $this->getUrl('sso/*/applyLog') . "'",
                'class' => 'apply'
            ]
        );
    }
}
