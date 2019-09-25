<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone;

use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone as ExtendedZoneParentController;

class Delete extends ExtendedZoneParentController
{
    /**
     * Delete Pop-up Zone action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->zoneRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the zone.'));
                $this->_redirect('mageworx_shippingrules/*/');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the zone right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a zone to delete.'));
        $this->_redirect('mageworx_shippingrules/*/');
    }
}
