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



namespace Mirasvit\Kb\Controller\Category;

use Magento\Framework\Controller\ResultFactory;

class View extends \Mirasvit\Kb\Controller\Category
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($category = $this->_initCategory()) {
            /* @var \Magento\Backend\Model\View\Result\Page $page */
            $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

            $children = $category->getChildren();

            if ($children->count()) {
                $page->addPageLayoutHandles(['type' => 'with_children']);
            } else {
                $page->addPageLayoutHandles(['type' => 'without_children']);
            }

            $page->addPageLayoutHandles(['mode' => $category->getDisplayMode()]);

            // apply custom layout update
            $customLayoutUpdate = $category->getCustomLayoutUpdate();
            if ($customLayoutUpdate) {
                $page->addUpdate($customLayoutUpdate);
                $page->addPageLayoutHandles(['layout_update' => md5($customLayoutUpdate)]);
            }

            return $page;
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}
