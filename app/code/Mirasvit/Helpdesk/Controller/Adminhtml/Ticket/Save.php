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

class Save extends \Mirasvit\Helpdesk\Controller\Adminhtml\Ticket
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($data = $this->getRequest()->getParams()) {
            if (!isset($data['customer_email']) && isset($data['store_id'])) {
                $params = ['store_id' => $data['store_id']];
                if (!empty($data['customer_id'])) {
                    $params['customer_id'] = $data['customer_id'];
                }
                $resultRedirect->setPath('*/*/add', $params);

                return $resultRedirect;
            }
            try {
                $user = $this->context->getAuth()->getUser();
                $ticket = $this->helpdeskProcess->createOrUpdateFromBackendPost($data, $user);

                if ($data['reply'] != '' && $data['reply_type'] != Config::MESSAGE_INTERNAL) {
                    $this->messageManager->addSuccessMessage(__('Message was successfully sent'));
                } else {
                    $this->messageManager->addSuccessMessage(__('Ticket was successfully updated'));
                }
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('*/*/edit', ['id' => $ticket->getId()]);

                    return $resultRedirect;
                }

                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->backendSession->setFormData($data);
                if ($this->getRequest()->getParam('id')) {
                    $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                } else {
                    $resultRedirect->setPath('*/*/add');
                }

                return $resultRedirect;
            }
        }
        $this->messageManager->addErrorMessage(__('Unable to find ticket to save'));
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }
}
