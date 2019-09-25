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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Gateway;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Mirasvit\Helpdesk\Controller\Adminhtml\Gateway
{
    /**
     * Save gateway.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($data = $this->getRequest()->getParams()) {
            $gateway = $this->_initGateway();
            $gateway->addData($this->prepareData($data));
            try {
                $gateway->save();
                $fetchHelper = $this->helpdeskFetch;
                if ($fetchHelper->connect($gateway)) {
                    $this->messageManager->addSuccess(__('Gateway was successfully saved. Connection is established.'));
                    $fetchHelper->close();
                }
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $gateway->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Mirasvit_Ddeboer_Imap_Exception_AuthenticationFailedException $e) {
                $message = $e->getMessage();
                $message .= ' ('.$this->helpdeskCheckenv->checkGateway($gateway).')';
                $this->messageManager->addErrorMessage($message);
                $this->backendSession->setFormData($data);

                $id = $this->getRequest()->getParam('id') ?: $gateway->getId();

                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->backendSession->setFormData($data);

                if ($this->getRequest()->getParam('id')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                } else {
                    return $resultRedirect->setPath('*/*/add');
                }
            }
        }
        $this->messageManager->addError(__('Unable to find Gateway to save'));

        return $resultRedirect->setPath('*/*/');
    }
}
