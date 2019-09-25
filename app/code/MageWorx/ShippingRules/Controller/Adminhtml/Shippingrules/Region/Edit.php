<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Region;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Region as RegionParentController;
use \MageWorx\ShippingRules\Model\Region;

class Edit extends RegionParentController
{
    /**
     * Region edit action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initRegion();
        /** @var \MageWorx\ShippingRules\Model\Region $model */
        $model = $this->coreRegistry->registry(Region::CURRENT_REGION);
        $id = $model->getId();

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_initAction();
        $this->_addBreadcrumb($id ? __('Edit Region') : __('New Region'), $id ? __('Edit Region') : __('New Region'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $id ? $model->getName() : __('New Region')
        );
        $this->_view->renderLayout();
    }
}
