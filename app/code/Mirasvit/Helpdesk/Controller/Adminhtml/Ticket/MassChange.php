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
use Mirasvit\Helpdesk\Model\Config as Config;

class MassChange extends \Mirasvit\Helpdesk\Controller\Adminhtml\Ticket
{
    /**
     *
     */
    public function execute()
    {
        //        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        //        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $ids = $this->getRequest()->getParam('ticket_id');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select ticket(s)'));
        } else {
            try {
                $statusId = $this->getRequest()->getParam('status');
                $owner = $this->getRequest()->getParam('owner');
                $spam = $this->getRequest()->getParam('spam');
                $archive = $this->getRequest()->getParam('archive');
                foreach ($ids as $id) {
                    $ticket = $this->ticketFactory->create()
                        ->setIsMassDelete(true)
                        ->load($id);
                    $stateBefore = $ticket->getState();
                    if ($spam) {
                        $ticket->markAsSpam();
                        continue;
                    }
                    if ($archive) {
                        $ticket->setFolder(Config::FOLDER_ARCHIVE);
                    }
                    if ($statusId) {
                        $ticket->setStatusId($statusId);
                    }
                    if ($owner) {
                        $ticket->initOwner($owner);
                    }
                    $ticket->save();
                    $this->helpdeskHistory->changeTicket(
                        $ticket,
                        $stateBefore,
                        $ticket->getState(),
                        Config::USER,
                        ['user' => $this->context->getAuth()->getUser()]
                    );
                }
                if ($spam) {
                    $this->messageManager->addSuccess(
                        __(
                            'Total of %1 record(s) were moved to the Spam folder',
                            count($ids)
                        )
                    );
                } else {
                    $this->messageManager->addSuccess(
                        __(
                            'Total of %1 record(s) were successfully updated',
                            count($ids)
                        )
                    );
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
