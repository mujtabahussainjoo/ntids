<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Quote;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Quote as RuleParentController;

class Edit extends RuleParentController
{
    /**
     * Shipping rule edit action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initRule();
        /** @var \MageWorx\ShippingRules\Model\Rule $model */
        $model = $this->coreRegistry->registry('current_promo_quote_rule');
        $id = $model->getId();

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $model->getActions()->setJsFormObject('rule_actions_fieldset');

        $this->_initAction();
        $this->_view->getLayout()
            ->getBlock('shippingrules_quote_edit')
            ->setData('action', $this->getUrl('mageworx_shippingrules/*/save'));

        $this->_addBreadcrumb($id ? __('Edit Rule') : __('New Rule'), $id ? __('Edit Rule') : __('New Rule'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? $model->getName() : __('New Shipping Rule')
        );
        $this->_view->renderLayout();
    }
}
