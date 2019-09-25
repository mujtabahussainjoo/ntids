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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Spam;

use Magento\Framework\Controller\ResultFactory;

class MassApprove extends \Mirasvit\Helpdesk\Controller\Adminhtml\Spam
{
    /**
     *
     */
    public function execute()
    {
        //        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        //        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $ids = $this->getRequest()->getParam('spam_id');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select spam(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $ticket = $this->ticketFactory->create()->load($id);
                    $ticket->markAsNotSpam();
                }
                $this->messageManager->addSuccess(
                    __(
                        'Total of %1 record(s) were successfully moved to the Tickets folder',
                        count($ids)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
