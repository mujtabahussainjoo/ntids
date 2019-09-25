<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Quote;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Quote as RuleParentController;

class Duplicate extends RuleParentController
{
    /**
     * Create rule duplicate
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $this->_initRule();
        /** @var \MageWorx\ShippingRules\Model\Rule $rule */
        $rule = $this->coreRegistry->registry('current_promo_quote_rule');
        try {
            $newRule = clone $rule;
            $newRule->setId(null);
            $newRule->isObjectNew(true);
            $newRule->setData('is_active', 0);
            $this->ruleRepository->save($newRule);
            $this->messageManager->addSuccessMessage(__('You duplicated the rule.'));
            $resultRedirect->setPath('mageworx_shippingrules/*/edit', ['_current' => true, 'id' => $newRule->getId()]);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('mageworx_shippingrules/*/edit', ['_current' => true]);
        }

        return $resultRedirect;
    }
}
