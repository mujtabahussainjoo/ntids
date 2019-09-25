<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier as CarrierParentController;

class Delete extends CarrierParentController
{
    /**
     * Delete carrier action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->carrierRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the carrier'));
                $this->_redirect('mageworx_shippingrules/*/');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the carrier right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a carrier to delete.'));
        $this->_redirect('mageworx_shippingrules/*/');
    }
}
