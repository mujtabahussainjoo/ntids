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



namespace Mirasvit\Kb\Service\Category;

use Mirasvit\Kb\Controller\Adminhtml\Category;

class CategoryManagement implements \Mirasvit\Kb\Api\Service\Category\CategoryManagementInterface
{
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->storeManager  = $storeManager;
        $this->request       = $request;
        $this->layoutFactory = $layoutFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function initCategoryFromRequest($getRootInstead = false, $data = [])
    {
        if (!$data) {
            $data = $this->request->getParams();
        }
        $categoryId = (int) $this->getArrayParam($data, 'id', 0);
        $storeId    = (int) $this->getArrayParam($data, 'store', 0);

        $category = $this->objectManager->create('Mirasvit\Kb\Model\Category')
            ->setStoreId($storeId);

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

        if (isset($data['active_tab_id'])) {
            $this->objectManager->get('Magento\Backend\Model\Auth\Session')->setActiveTabId($data['active_tab_id']);
        }
        $this->objectManager->get('Magento\Framework\Registry')->register('current_category', $category);
        $this->objectManager->get('Magento\Cms\Model\Wysiwyg\Config')
            ->setStoreId($storeId);

        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailableForStore($category, $currentStore = 0)
    {
        $rootId   = $category->getParentRootCategory();
        $root     = $category->loadPathArray($rootId)[0];
        $storeIds = (array)$root->getStoreIds();
        if (!$currentStore) {
            $currentStore = $this->storeManager->getStore()->getId();
        }

        return in_array($currentStore, $storeIds) || in_array(0, $storeIds);
    }

    /**
     * @param array  $data
     * @param string $key
     * @param string $default
     * @return int|string|array|object
     */
    private function getArrayParam($data, $key, $default)
    {
        return isset($data[$key]) ? $data[$key] : $default;
    }
}
