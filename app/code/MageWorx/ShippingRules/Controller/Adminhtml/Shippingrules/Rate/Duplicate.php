<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate as RateParentController;
use MageWorx\ShippingRules\Model\Carrier\Method\Rate as RateModel;

class Duplicate extends RateParentController
{
    /**
     * Create rate duplicate
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $this->_init();
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method\Rate $rate */
        $rate = $this->coreRegistry->registry(RateModel::CURRENT_RATE);
        try {
            $newRate = clone $rate;
            $newRate->setId(null);
            $newRate->isObjectNew(true);
            $newRate->setData('active', 0);
            $this->rateRepository->save($newRate);
            $this->messageManager->addSuccessMessage(__('You duplicated the rate.'));
            $resultRedirect->setPath('mageworx_shippingrules/*/edit', ['_current' => true, 'id' => $newRate->getId()]);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect->setPath('mageworx_shippingrules/*/edit', ['_current' => true]);
        }

        return $resultRedirect;
    }
}
