<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Region;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Region as RegionParentController;

class Index extends RegionParentController
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Country Regions'));
        $this->_view->renderLayout('root');
    }
}
