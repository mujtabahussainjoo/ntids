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



namespace Mirasvit\Kb\Api\Service\Category;

interface CategoryManagementInterface
{
    /**
     * Initialize requested category and put it into registry.
     * Root category can be returned, if inappropriate store/category is specified.
     *
     * @param bool  $getRootInstead
     * @param array $data
     *
     * @return \Magento\Catalog\Model\Category|false
     */
    public function initCategoryFromRequest($getRootInstead = false, $data = []);

    /**
     * @param \Mirasvit\Kb\Model\Category $category
     * @param int                         $currentStore
     * @return bool
     */
    public function isAvailableForStore($category, $currentStore = 0);
}