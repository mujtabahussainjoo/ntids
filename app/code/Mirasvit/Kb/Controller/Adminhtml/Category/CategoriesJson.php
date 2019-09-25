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



namespace Mirasvit\Kb\Controller\Adminhtml\Category;

class CategoriesJson extends \Mirasvit\Kb\Controller\Adminhtml\Category
{
    /**
     * Get tree node (Ajax version).
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->setIsTreeWasExpanded(true);
        } else {
            $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->setIsTreeWasExpanded(false);
        }
        $categoryId = (int) $this->getRequest()->getParam('id');
        if ($categoryId) {
            $this->getRequest()->setParam('id', $categoryId);

            $category = $this->_initCategory();
            if (!$category) {
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('kbase/*/', ['_current' => true, 'id' => null]);
            }
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            return $resultJson->setJsonData(
                $this->layoutFactory->create()->createBlock('Mirasvit\Kb\Block\Adminhtml\Category\Tree')
                    ->getTreeJson($category)
            );
        }
    }
}
