<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Zone;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Zone as ParentZoneController;

class NewAction extends ParentZoneController
{
    /**
     * New shipping zone action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
