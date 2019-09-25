<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Method;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Method as MethodParentController;
use \MageWorx\ShippingRules\Model\Carrier\Method;
use MageWorx\ShippingRules\Model\Carrier\Method as MethodModel;

class Edit extends MethodParentController
{
    /**
     * Method edit action
     *
     * @return void
     */
    public function execute()
    {
        $this->_init();
        $model = $this->coreRegistry->registry(MethodModel::CURRENT_METHOD);
        $id = $model->getId();

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_session->setData('mw_current_sMethod_id', $model->getId());
        $this->_initAction();
        $this->_addBreadcrumb($id ? __('Edit Method') : __('New Method'), $id ? __('Edit Method') : __('New Method'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getEntityId() ? $model->getTitle() : __('New Method')
        );
        $this->_view->renderLayout();
    }
}
