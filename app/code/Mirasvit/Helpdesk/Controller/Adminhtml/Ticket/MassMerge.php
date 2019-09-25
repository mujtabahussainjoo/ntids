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

class MassMerge extends \Magento\Backend\App\Action
{
    /**
     * @param \Magento\Backend\App\Action\Context                             $context
     * @param \Magento\Ui\Component\MassAction\Filter                         $filter
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory
     * @param \Mirasvit\Helpdesk\Helper\Process                               $helpdeskProcess
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory,
        \Mirasvit\Helpdesk\Helper\Process $helpdeskProcess
    ) {
        $this->context = $context;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->helpdeskProcess = $helpdeskProcess;

        parent::__construct($context);
    }

    /**
     *
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $ids = $collection->getAllIds();
        if (!is_array($ids) || count($ids) < 2) {
            $this->messageManager->addErrorMessage(__('Please select 2 or more tickets'));
        } else {
            $this->helpdeskProcess->mergeTickets($ids);
            $this->messageManager->addSuccessMessage(
                __(
                    'Total of %1 record(s) were successfully merged',
                    count($ids)
                )
            );
        }
        $this->_redirect('*/*/index');
    }
}
