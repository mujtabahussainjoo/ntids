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

class Move extends \Mirasvit\Kb\Controller\Adminhtml\Category
{
    /**
     * @return $this
     * @throws \Exception
     */
    public function execute()
    {
        /*
         * New parent category identifier
         */
        $parentNodeId = $this->getRequest()->getPost('pid', false);
        /*
         * Category id after which we have put our category
         */
        $prevNodeId = $this->getRequest()->getPost('aid', false);

        /** @var $block \Magento\Framework\View\Element\Messages */
        $block = $this->layoutFactory->create()->getMessagesBlock();
        $error = false;

        try {
            $category = $this->_initCategory();
            if ($category === false) {
                throw new \Exception(__('Category is not available for requested store.'));
            }
            $category->move($parentNodeId, $prevNodeId);
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            $error = true;
            $this->messageManager->addError(__('There was a category move error. %1', $e->getMessage()));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $error = true;
            $this->messageManager->addError(__('There was a category move error.'));
        }

        if (!$error) {
            $this->messageManager->addSuccess(__('You moved the category'));
        }

        $block->setMessages($this->messageManager->getMessages(true));
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'messages' => $block->getGroupedHtml(),
            'error' => $error,
        ]);
    }
}
