<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Ticket;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Mirasvit\Helpdesk\Controller\Adminhtml\Ticket
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $ticket = $this->_initTicket();

        $data = $this->backendSession->getFormData(true);
        if (!empty($data)) {
            $ticket->setData($data);
        }
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $ticket->setCustomerId($customerId);
            $customer = $this->customerFactory->create()->load($customerId);
            if ($customer->getId()) {
                $ticket->setCustomerEmail($customer->getEmail());
            }
        } elseif ($orderId = $this->getRequest()->getParam('order_id')) {
            $ticket->initFromOrder($orderId);
        }

        if ($this->storeManager->isSingleStoreMode()) {
            $ticket->setStoreId($this->storeManager->getStore(true)->getId());
        } elseif (count($this->storeManager->getStores()) == 1) {
            $stores = $this->storeManager->getStores();
            $store = array_pop($stores);
            $ticket->setStoreId($store->getId());
        } elseif ($storeId = $this->getRequest()->getParam('store_id')) {
            $ticket->setStoreId($storeId);
        } elseif ($storeId = $this->helpdeskUser->getHelpdeskUser()->getStoreId()) {
            $ticket->setStoreId($storeId);
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('New Ticket'));
        $resultPage->setActiveMenu('helpdesk');
        $resultPage->addBreadcrumb(__('Ticket Manager'), __('Ticket Manager'), $this->getUrl('*/*/'));
        $resultPage->addBreadcrumb(__('Add Ticket '), __('Add Ticket'));

        return $resultPage;
    }
}
