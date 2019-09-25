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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Field;

use Mirasvit\Helpdesk\Api\Data\FieldInterface;

class Save extends \Mirasvit\Helpdesk\Controller\Adminhtml\Field
{
    /**
     *
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getParams()) {
            if (empty($data['id']) && empty($data[FieldInterface::ID])) { // check if field already exists
                $code = $this->fieldFactory->create()->getResource()->normalize($data[FieldInterface::KEY_CODE]);
                $field = $this->fieldHelper->getFieldByCode($code);
                if ($field && $field->getId()) {
                    $this->messageManager->addErrorMessage(__('Field with the same code already exists'));
                    $this->backendSession->setFormData($data);
                    $this->_redirect('*/*/add');

                    return;
                }
            }

            $field = $this->_initField();
            $field->addData($this->prepareData($data));
            try {
                $field->save();

                $this->messageManager->addSuccessMessage(__('Field was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $field->getId(), 'store' => $field->getStoreId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->backendSession->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

                return;
            }
        }
        $this->messageManager->addErrorMessage(__('Unable to find Field to save'));
        $this->_redirect('*/*/');
    }
}
