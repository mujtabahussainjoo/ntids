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



namespace Mirasvit\Kb\Service\Category\CategoryManagement;

use Mirasvit\Kb\Controller\Adminhtml\Category;

class Save implements \Mirasvit\Kb\Api\Service\Category\CategoryManagement\SaveInterface
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function saveCategory($data, $category = null)
    {
        if (!$category) {
            $category = $this->objectManager->create('Mirasvit\Kb\Model\Category');
        }
        if (!empty($data['isAjax'])) {
            $data['general'] = $data;
        }
        if ($data) {
            $category->addData($data['general']);
            if (!empty($data['seo'])) {
                $category->addData($data['seo']);
            }
            if (!empty($data['design'])) {
                $category->addData($data['design']);
            }
            if (!$category->getId()) {
                $parentId = (isset($data['parent'])) ? $data['parent'] : false;
                if (!$parentId) {
                    $parentId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
                }
                $parentCategory = $this->objectManager->create('Magento\Catalog\Model\Category')->load($parentId);
                $category->setPath($parentCategory->getPath());
                $category->setParentId($parentId);
            }

            $category->save();
        }

        return $category;
    }
}
