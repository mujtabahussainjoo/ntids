<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate;

class Index extends Rate
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Shipping Rates'));
        $this->_view->renderLayout('root');
    }
}
