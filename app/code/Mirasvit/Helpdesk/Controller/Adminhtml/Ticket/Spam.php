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

class Spam extends \Mirasvit\Helpdesk\Controller\Adminhtml\Ticket
{
    /**
     * Move ticket to the spam folder.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $id = (int) $this->getRequest()->getParam('id');
        try {
            $ticket = $this->ticketFactory->create()->load($id);
            $ticket->markAsSpam();

            $this->messageManager->addSuccess(
                __('Ticket was moved to the Spam folder')
            );

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $resultPage->setPath('*/*/edit', ['id' => $this->getRequest()
                ->getParam('id'), ]);
            return $resultPage;
        }
        $resultPage->setPath('*/*/');
        return $resultPage;
    }
}
