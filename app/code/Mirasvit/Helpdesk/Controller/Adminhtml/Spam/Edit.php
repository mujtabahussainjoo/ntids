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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Spam;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Mirasvit\Helpdesk\Controller\Adminhtml\Spam
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $model = $this->_initModel();

        if ($model->getId()) {
            $resultPage->getConfig()->getTitle()->prepend(__("Edit Spam '%1'", $model->getName()));
            $this->_initAction();
            $this->_addBreadcrumb(
                __('Spam Folder'),
                __('Spam Folder'),
                $this->getUrl('*/*/')
            );
            $this->_addBreadcrumb(
                __('Edit Spam '),
                __('Edit Spam ')
            );

            $resultPage->getLayout()
                ->getBlock('head')
                ;
            $this->_addContent($resultPage->getLayout()->createBlock('\Mirasvit\Helpdesk\Block\Adminhtml\Spam\Edit'));

            return $resultPage;
        } else {
            $this->messageManager->addError(__('The spam does not exist.'));
            $this->_redirect('*/*/');
        }
    }
}
