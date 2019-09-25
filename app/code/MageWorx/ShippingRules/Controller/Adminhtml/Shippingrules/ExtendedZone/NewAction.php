<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone as ExtendedZoneParentController;

class NewAction extends ExtendedZoneParentController
{
    /**
     * New Pop-up Zone action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
