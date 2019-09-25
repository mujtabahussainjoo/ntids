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


namespace Mirasvit\Kb\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

use Mirasvit\Kb\Model\CategoryFactory;
use Mirasvit\Kb\Helper\Form\Article\Category as ArticleCategory;

/**
 * Class Category
 */
class Category extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     * @param string $storeKey
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        ArticleCategory $articleCategoryHelper,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = [],
        $storeKey = 'category_id'
    ) {
        $this->articleCategoryHelper = $articleCategoryHelper;
        $this->categoryFactory       = $categoryFactory;
        $this->storeKey              = $storeKey;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fieldName])) {
                    $item[$fieldName] = $this->prepareItem($item);
                }
            }
        }

        return $dataSource;
    }

    /**
     * Modilied to display All Stores for 0
     * {@inheritdoc}
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        if (!empty($item[$this->storeKey])) {
            $origCategories = explode(',', $item[$this->storeKey]);
        }

        if (empty($origCategories)) {
            return '';
        }
        if (!is_array($origCategories)) {
            $origCategories = [$origCategories];
        }

        $categoryTree = $this->articleCategoryHelper->getCategoriesTree();

        $content = '';
        $content = $this->buildOptions($content, $categoryTree, $origCategories);

        return $content;
    }

    /**
     * @param array $content
     * @param array $categoryTree
     * @param array $origCategories
     * @return array
     */
    protected function buildOptions($content, $categoryTree, $origCategories)
    {
        foreach ($categoryTree as $category) {
            $prefix = str_repeat('&nbsp;', ($category['level'] - 1) * 3);
            $label = $category['label'];
            if (in_array($category['value'], $origCategories)) {
                $label = '<b>' . $label . '</b>';
            }
            $content .= $prefix . $label .'<br>';
            if (isset($category['optgroup'])) {
                $content = $this->buildOptions($content, $category['optgroup'], $origCategories);
            }
        }

        return $content;
    }
}
