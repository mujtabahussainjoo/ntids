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



namespace Mirasvit\Helpdesk\Controller\Ticket;

use Magento\Framework\Controller\ResultFactory;

class Getopen extends \Mirasvit\Helpdesk\Controller\Ticket
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl('/customer/account');
            return $redirect;
        }

        $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson = $this->resultJsonFactory->create();

        $customer = $this->_getSession()->getCustomer();
        $amount = $this->helpdeskTicket->getOpenTicketsAmount($customer->getId());
        if ($amount) {
            $amount = $this->helpdeskTicket->formatTicketLabel($amount);
        }
        $response = $resultJson->setData(['amount' => $amount]);
        return $response;
    }
}
