<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone as ExtendedZoneParentController;
use \MageWorx\ShippingRules\Model\ExtendedZone as ExtendedZoneModel;

class Edit extends ExtendedZoneParentController
{
    /**
     * Pop-up Zone edit action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initZone();
        /** @var ExtendedZoneModel $model */
        $model = $this->coreRegistry->registry(ExtendedZoneModel::REGISTRY_KEY);
        $id = $model->getId();

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_initAction();
        $this->_addBreadcrumb(
            $id ? __('Edit Pop-up Zone') : __('New Pop-up Zone'),
            $id ? __('Edit Pop-up Zone') : __('New Pop-up Zone')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Pop-up Zone')
        );
        $this->_view->renderLayout();
    }
}
