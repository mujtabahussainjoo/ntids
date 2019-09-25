<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate;

use \MageWorx\ShippingRules\Model\Carrier\Method\Rate as RateModel;

class Edit extends \MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate
{
    /**
     * Rate edit action
     *
     * @return void
     */
    public function execute()
    {
        $this->_init();
        /** @var RateModel $model */
        $model = $this->coreRegistry->registry(RateModel::CURRENT_RATE);
        $id = $model->getId();

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_initAction();
        $this->_addBreadcrumb($id ? __('Edit Rate') : __('New Rate'), $id ? __('Edit Rate') : __('New Rate'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTitle() : __('New Rate')
        );
        $this->_view->renderLayout();
    }
}
