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



namespace Mirasvit\Kb\Controller\Adminhtml;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Category extends \Magento\Backend\App\Action
{
    /**
     * @var \Mirasvit\Kb\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Mirasvit\Kb\Model\CategoryFactory                          $categoryFactory
     * @param \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\Registry                                 $registry
     * @param \Magento\Framework\Json\Helper\Data                         $jsonEncoder
     * @param \Magento\Backend\App\Action\Context                         $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory           $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory                  $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory            $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory                       $layoutFactory
     */
    public function __construct(
        \Mirasvit\Kb\Model\CategoryFactory $categoryFactory,
        \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\Helper\Data $jsonEncoder,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->context = $context;
        $this->layoutFactory = $layoutFactory;
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
     * @return $this
     * @todo should be same as Article controller
     */
    protected function _initAction()
    {
        $this->_setActiveMenu('Mirasvit_Kb::kb');

        return $this;
    }

    /**
     * Initialize requested category and put it into registry.
     * Root category can be returned, if inappropriate store/category is specified.
     *
     * @param bool $getRootInstead
     *
     * @return \Magento\Catalog\Model\Category|false
     * @deprecated use instead CategoryManagementInterface->initCategoryFromRequest
     */
    protected function _initCategory($getRootInstead = false)
    {
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        $storeId = (int) $this->getRequest()->getParam('store');
        $category = $this->_objectManager->create('Mirasvit\Kb\Model\Category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = 1;
                if (!in_array($rootId, $category->getPathIds())) {
                    // load root category instead wrong one
                    if ($getRootInstead) {
                        $category->load($rootId);
                    } else {
                        return false;
                    }
                }
            }
        }

        $activeTabId = (string) $this->getRequest()->getParam('active_tab_id');
        if ($activeTabId) {
            $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->setActiveTabId($activeTabId);
        }
        $this->_objectManager->get('Magento\Framework\Registry')->register('current_category', $category);
        $this->_objectManager->get('Magento\Cms\Model\Wysiwyg\Config')
            ->setStoreId($this->getRequest()->getParam('store'));

        return $category;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Kb::kb_category');
    }

    /************************/
}
