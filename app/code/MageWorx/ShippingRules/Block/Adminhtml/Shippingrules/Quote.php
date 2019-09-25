<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules;

class Quote extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'shippingrules_quote';
        $this->_headerText = __('MageWorx Shipping Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}
