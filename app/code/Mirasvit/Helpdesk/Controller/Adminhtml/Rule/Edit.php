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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Mirasvit\Helpdesk\Controller\Adminhtml\Rule
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $rule = $this->_initRule();

        if ($rule->getId()) {
            $resultPage->getConfig()->getTitle()->prepend(__("Edit Rule '%1'", $rule->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(
                __('Workflow Rules'),
                __('Workflow Rules'),
                $this->getUrl('*/*/')
            );
            $this->_addBreadcrumb(
                __('Edit Rule '),
                __('Edit Rule ')
            );

            return $resultPage;
        } else {
            $this->messageManager->addError(__('The Rule does not exist.'));
            $this->_redirect('*/*/');
        }
    }
}
