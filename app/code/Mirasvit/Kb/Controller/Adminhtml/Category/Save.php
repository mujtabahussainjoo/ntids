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

class Save extends \Magento\Backend\App\Action
{
    public function __construct(
        \Mirasvit\Kb\Helper\Form\Article\Category $categoryHelper,
        \Mirasvit\Kb\Api\Service\Category\CategoryManagement\SaveInterface $categorySaveService,
        \Mirasvit\Kb\Api\Service\Category\CategoryManagementInterface $categoryManagement,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->categoryHelper = $categoryHelper;
        $this->categorySaveService = $categorySaveService;
        $this->categoryManagement = $categoryManagement;
        $this->registry = $registry;
        $this->context = $context;
        $this->layoutFactory = $layoutFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $category = $this->categoryManagement->initCategoryFromRequest(false, $this->getRequest()->getParams());

        if (!$category) {
            return $resultRedirect->setPath('kbase/*/', ['_current' => true, 'id' => null]);
        }

        $refreshTree = false;
        if ($data = $this->getRequest()->getParams()) {
            try {
                $data = array_merge($data, $this->getRequest()->getPostValue());
                $category = $this->categorySaveService->saveCategory($data, $category);
                $this->registry->unregister('kb_current_category');
                $this->messageManager->addSuccess(__('You saved the category.'));
                $refreshTree = true;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setCategoryData($data);
            }
        }

        if ($this->getRequest()->getPost('return_session_messages_only')) {
            $category->load($category->getId());
            // to obtain truncated category name
            /** @var $block \Magento\Framework\View\Element\Messages */
            $block = $this->layoutFactory->create()->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages(true));

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            $stores = $this->categoryHelper->getStores($category);

            $category = $category->getData();
            $category['entity_id'] = $category['category_id'];
            $category['parent'] = $category['parent_id'];
            $category['stores'] = $stores;

            return $resultJson->setData(
                [
                    'messages' => $block->getGroupedHtml(),
                    'error'    => !$refreshTree,
                    'category' => $category,
                ]
            );
        }

        $redirectParams = [
            '_current' => true,
            'id'       => $category->getId(),
        ];
        if ($storeId = $this->getRequest()->getParam('store')) {
            $redirectParams['store'] = $storeId;
        }

        return $resultRedirect->setPath(
            'kbase/*/edit',
            $redirectParams
        );
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Kb::kb_category');
    }
}
