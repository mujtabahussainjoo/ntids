<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Zone;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Zone as ParentZoneController;
use MageWorx\ShippingRules\Model\Zone as ZoneModel;

class Edit extends ParentZoneController
{
    /**
     * Shipping zone edit action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initZone();
        /** @var ZoneModel $model */
        $model = $this->coreRegistry->registry(ZoneModel::CURRENT_ZONE);
        $id = $model->getId();

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('zone_conditions_fieldset');
        $model->getActions()->setJsFormObject('zone_actions_fieldset');

        $this->_initAction();
        $this->_view->getLayout()
            ->getBlock('shippingrules_zone_edit')
            ->setData('action', $this->getUrl('mageworx_shippingrules/*/save'));

        $this->_addBreadcrumb(
            $id ? __('Edit location group') : __('New location group'),
            $id ? __('Edit location group') : __('New location group')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New location group')
        );
        $this->_view->renderLayout();
    }
}
