<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Quote;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Quote as RuleParentController;

class NewAction extends RuleParentController
{
    /**
     * New shipping rule action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
