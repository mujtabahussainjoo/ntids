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



namespace Mirasvit\Kb\Helper;

class ArticleList extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * List mode configuration path
     */
    const XML_PATH_LIST_MODE = 'article/frontend/list_mode';

    const VIEW_MODE_LIST = 'view';
    const VIEW_MODE_GRID = 'list';

    const DEFAULT_SORT_DIRECTION = 'asc';

    /**
     * Retrieve default per page values
     *
     * @return string (comma separated)
     */
    public function getDefaultLimitPerPageValue()
    {
        return $this->scopeConfig->getValue(
            'article/frontend/list_per_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default sort field
     *
     * @return null|string
     */
    public function getDefaultSortField()
    {
        return 'created_at';
    }
}
