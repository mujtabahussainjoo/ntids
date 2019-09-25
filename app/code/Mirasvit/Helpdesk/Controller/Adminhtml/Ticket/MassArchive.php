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

class MassArchive extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory,
        \Mirasvit\Helpdesk\Helper\Permission $helpdeskPermission,
        \Mirasvit\Helpdesk\Repository\Ticket\FolderRepository $folderRepository,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->filter             = $filter;
        $this->context            = $context;
        $this->collectionFactory  = $collectionFactory;
        $this->folderRepository   = $folderRepository;
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

        if (!$this->getRequest()->getParams('namespace')) {
            return $resultRedirect->setPath('*/*/');
        }

        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
        foreach ($collection as $ticket) {
            $this->helpdeskPermission->checkReadTicketRestrictions($ticket);
            $this->folderRepository->markAsArchive($ticket);
        }

        $this->messageManager->addSuccessMessage(__('Total of %1 record(s) were moved to Archive.', $collectionSize));


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
