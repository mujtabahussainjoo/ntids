<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier as CarrierParentController;

class Index extends CarrierParentController
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Carriers'));
        $this->_view->renderLayout('root');
    }
}
