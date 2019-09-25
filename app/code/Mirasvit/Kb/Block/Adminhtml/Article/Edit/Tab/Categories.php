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



namespace Mirasvit\Kb\Block\Adminhtml\Article\Edit\Tab;

class Categories extends \Mirasvit\Kb\Block\Adminhtml\Category\Tree
{
    /**
     * Construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('article/edit/categories.phtml');
    }

    /**
     * @return \Mirasvit\Kb\Model\Article
     */
    public function getArticle()
    {
        return $this->registry->registry('current_article');
    }

    /**
     * @return bool
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * @return array
     */
    protected function getCategoryIds()
    {
        if ($ids = $this->getArticle()->getCategoryIds()) {
            return $ids;
        } else {
            return [];
        }
    }

    /**
     * @return string
     */
    public function getIdsString()
    {
        return implode(',', $this->getCategoryIds());
    }

    /**
     * @param null $expanded
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getLoadTreeUrl($expanded = null)
    {
        return $this->getUrl('*/*/categoriesJson', ['_current' => true]);
    }

    /**
     * @param \Mirasvit\Kb\Model\Category $node
     * @return array
     */
    protected function _getNodeJson($node)
    {
        $item = [
            'id' => $node->getId(),
            'path' => $node->getPath(),
            'cls' => ($node->getIsActive() ? 'active' : 'no-active'),
            'text' => $this->buildNodeName($node),
            'checked' => in_array($node->getId(), $this->getCategoryIds()),
        ];

        if ($node->hasChildren()) {
            $item['children'] = [];
            foreach ($node->getAllChildren() as $child) {
                $item['children'][] = $this->_getNodeJson($child);
            }
            $item['expanded'] = true;
        }

        return $item;
    }
}
