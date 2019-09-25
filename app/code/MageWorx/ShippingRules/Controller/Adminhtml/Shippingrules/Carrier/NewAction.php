<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier as CarrierParentController;

class NewAction extends CarrierParentController
{
    /**
     * New action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
