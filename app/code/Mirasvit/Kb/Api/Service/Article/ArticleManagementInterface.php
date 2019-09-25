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



namespace Mirasvit\Kb\Api\Service\Article;

interface ArticleManagementInterface
{
    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @param int                        $currentStore
     * @return bool
     */
    public function isAvailableForStore($article, $currentStore = 0);

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @param array                      $categories
     * @return array
     */
    public function getAvailableStores($article, $categories = []);
}