<?php

namespace Serole\OrderAdminNotifications\Controller\Magento\Sales\Adminhtml\Order;


class Email extends \Magento\Sales\Controller\Adminhtml\Order\Email
{
	/**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::email';

    /**
     * Notify user
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
    */
    public function execute()
    {
        $order = $this->_initOrder();
		
		$billingEmail=$order->getBillingemail();
		if($billingEmail){ 
			$order->setCustomerEmail($billingEmail);
		}
        if ($order){
            try {
                $this->orderManagement->notify($order->getEntityId());
                $this->messageManager->addSuccessMessage(__('You sent the order email.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t send the email order right now.'));
                $this->logger->critical($e);
            }
            return $this->resultRedirectFactory->create()->setPath(
                'sales/order/view',
                [
                    'order_id' => $order->getEntityId()
                ]
            );
        }
        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
	
}
	
	