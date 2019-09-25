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

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @param \Magento\Backend\App\Action\Context                             $context
     * @param \Magento\Ui\Component\MassAction\Filter                         $filter
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory
     * @param \Mirasvit\Helpdesk\Helper\Permission                            $helpdeskPermission
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory,
        \Mirasvit\Helpdesk\Helper\Permission $helpdeskPermission
    ) {
        $this->context = $context;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->helpdeskPermission = $helpdeskPermission;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$this->helpdeskPermission->isTicketRemoveAllowed()) {
            $this->messageManager->addErrorMessage(__('You don\'t have permission for this operation.'));

            return $resultRedirect->setPath('*/*/');
        }
        if (!$this->getRequest()->getParams('namespace')) {
            return $resultRedirect->setPath('*/*/');
        }

        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $ticket) {
            $ticket->delete();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));


        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Helpdesk::helpdesk_ticket');
    }
}
