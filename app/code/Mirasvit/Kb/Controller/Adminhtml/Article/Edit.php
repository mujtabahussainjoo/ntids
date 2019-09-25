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
 * @package   mirasvit/module-kb
 * @version   1.0.49
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Controller\Adminhtml\Article;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Mirasvit\Kb\Controller\Adminhtml\Article
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

            $this->initPage($resultPage)->getConfig()->getTitle()
                ->prepend(__("Edit Article '%1'", $model->getName()));

            $this->_addContent($resultPage->getLayout()->createBlock('Mirasvit\Kb\Block\Adminhtml\Article\Edit'))
                ->_addLeft($resultPage->getLayout()->createBlock('Mirasvit\Kb\Block\Adminhtml\Article\Edit\Tabs'));

            return $resultPage;
        } else {
            $this->messageManager->addError(__('The article does not exist.'));
            $this->_redirect('*/*/');
        }
    }
}
