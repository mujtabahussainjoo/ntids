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

class Source extends \Mirasvit\Helpdesk\Controller\Adminhtml\Ticket
{
    /**
     * Show text source code of email/message
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        $id = (int) $this->getRequest()->getParam('message_id');
        $message = $this->messageFactory->create()->load($id);
        $ticket = $message->getTicket();
        $this->helpdeskPermission->checkReadTicketRestrictions($ticket);

        $resultPage->setContents('<pre>'.htmlentities($message->getBody()).'</pre>');
        return $resultPage;
    }
}
