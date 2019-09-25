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

use Magento\Framework\Data\OptionSourceInterface;

class CategoryOptions implements OptionSourceInterface
{

    public function __construct(\Mirasvit\Kb\Helper\Form\Article\Category $articleCategoryHelper)
    {
        $this->articleCategoryHelper = $articleCategoryHelper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $categoryTree = $this->articleCategoryHelper->getCategoriesTree();

        $content = [];
        $content = $this->buildOptions($content, $categoryTree);

        array_push($content, ['value' => 0, 'label' => __('All Categories')->getText()]);

        return $content;
    }

    /**
     * @param array $content
     * @param array $categoryTree
     * @return array
     */
    protected function buildOptions($content, $categoryTree)
    {
        foreach ($categoryTree as $category) {
            $prefix = str_repeat(' ', ($category['level'] - 1) * 3);
            if (isset($category['optgroup'])) {
                $content[] = [
                    'label' => $prefix . $category['label'],
                    'value' => $this->buildOptions([], $category['optgroup']),
                ];
            } else {
                $content[] = ['value' => $category['value'], 'label' => $prefix . $category['label']];
            }
        }

        return $content;
    }
}
