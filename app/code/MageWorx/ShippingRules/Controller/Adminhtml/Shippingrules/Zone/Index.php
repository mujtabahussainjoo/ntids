<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Zone;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Zone as ParentZoneController;

class Index extends ParentZoneController
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Locations Groups (Shipping Zones)'));
        $this->_view->renderLayout('root');
    }
}
