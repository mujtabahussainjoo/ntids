<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Method;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Method as MethodParentController;

class Index extends MethodParentController
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Shipping Methods'));
        $this->_view->renderLayout('root');
    }
}
