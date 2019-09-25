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

class Postexternal extends \Mirasvit\Helpdesk\Controller\Ticket
{
    /**
     * Post of message from not logged in customer.
     *
     * @return void
     */
    public function execute()
    {
        $session = $this->_getSession();
        $customer = $session->getCustomer();
        $ticket = $this->_initExternalTicket();
        if (!$ticket) {
            $this->_forward('no_route');
            return;
        }
        if ($customer->getId() == 0) {
            $customer = new \Magento\Framework\DataObject();
            $customer->setName($ticket->getCustomerName());
            $customer->setEmail($ticket->getCustomerEmail());
        }

        $this->postTicket($ticket, $customer);
        $this->_redirect($ticket->getExternalUrl());
    }
}
