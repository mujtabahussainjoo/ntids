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

class Delete extends \Mirasvit\Kb\Controller\Adminhtml\Category
{
    /**
     * Delete category action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $categoryId = (int) $this->getRequest()->getParam('id');
        $parentId = null;
        if ($categoryId) {
            try {
                /** @var \Mirasvit\Kb\Model\Category $category */
                $category = $this->categoryFactory->create()->load($categoryId);
                $parentId = $category->getParentId();
                $category->delete();
                $this->messageManager->addSuccess(__('You deleted the category.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('kbase/*/edit', ['_current' => true]);
            }
        }

        return $resultRedirect->setPath('kbase/*/', ['_current' => true, 'id' => $parentId]);
    }
}
