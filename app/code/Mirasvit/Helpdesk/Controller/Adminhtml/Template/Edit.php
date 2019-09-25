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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Template;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Mirasvit\Helpdesk\Controller\Adminhtml\Template
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $template = $this->_initTemplate();

        if ($template->getId()) {
            $resultPage->getConfig()->getTitle()->prepend(__("Edit Template '%1'", $template->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(
                __('Quick Responses'),
                __('Quick Responses'),
                $this->getUrl('*/*/')
            );
            $this->_addBreadcrumb(
                __('Edit Template '),
                __('Edit Template ')
            );

            $resultPage->getLayout()
                ->getBlock('head')
                ;

            return $resultPage;
        } else {
            $this->messageManager->addError(__('The Template does not exist.'));
            $this->_redirect('*/*/');
        }
    }
}
