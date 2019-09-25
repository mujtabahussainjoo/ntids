<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier as CarrierParentController;
use MageWorx\ShippingRules\Model\Carrier as CarrierModel;

class Edit extends CarrierParentController
{
    /**
     * Carrier edit action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initCarrier();
        $model = $this->coreRegistry->registry(CarrierModel::CURRENT_CARRIER);
        $id = $model->getId();

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_initAction();
        $breadcrumb = $id ? __('Edit Carrier') : __('New Carrier');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);

        $title = $model->getCarrierId() ? $model->getName() : __('New Carrier');
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_view->renderLayout();
    }
}
