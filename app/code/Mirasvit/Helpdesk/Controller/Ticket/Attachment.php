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

class Attachment extends \Mirasvit\Helpdesk\Controller\Ticket
{
    /**
     * Download attachments in frontend
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        $externalId = $this->getRequest()->getParam('id');
        $collection = $this->attachmentCollectionFactory->create()
            ->addFieldToFilter('external_id', $externalId);
        if (!$collection->count()) {
            return $resultPage->setContents('wrong URL');
        }
        $attachment = $collection->getFirstItem();

        // give our picture the proper headers...otherwise our page will be confused
        $resultPage->setHeader("Content-Disposition", "attachment; filename={$attachment->getName()}");
        $resultPage->setHeader("Content-length", $attachment->getSize());
        $resultPage->setHeader("Content-type", $attachment->getType());
        $resultPage->setContents($attachment->getBody());
        return $resultPage;
    }
}
