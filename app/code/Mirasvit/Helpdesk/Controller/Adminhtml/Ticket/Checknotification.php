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

class Checknotification extends \Mirasvit\Helpdesk\Controller\Adminhtml\Ticket
{
    /**
     * Do search of customers.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $user = $this->helpdeskUser->getHelpdeskUser();
        if ($this->getConfig()->getDesktopNotificationIsActive() && $user->getId()) {
            $messages = $this->desktopNotificationHelper->getUnreadMeassagesForUser($user);

            $return = [
                'messages' => $messages,
            ];

            if ($messages) {
                $return['new_tickets_cnt'] = $this->desktopNotificationHelper->getNewTicketsNumber();
                $return['new_messages_cnt'] = $this->desktopNotificationHelper->getUserMessagesNumber($user);
            }
        } else {
            $return = [
                'messages' => []
            ];
        }

        $resultPage->setData($return);
        return $resultPage;
    }
}
