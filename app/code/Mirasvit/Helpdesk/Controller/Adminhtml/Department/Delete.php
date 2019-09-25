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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Department;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Mirasvit\Helpdesk\Controller\Adminhtml\Department
{
    /**
     *
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id > 0) {
            try {
                $department = $this->departmentFactory->create();
                $department->getResource()->load($department, $id);
                $department->getResource()->delete($department);
                $this->messageManager->addSuccessMessage(
                    __('Department was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $id]);
            }
        }
        $this->_redirect('*/*/');
    }
}
