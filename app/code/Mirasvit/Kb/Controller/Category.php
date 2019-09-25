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



namespace Mirasvit\Kb\Controller;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;

abstract class Category extends Action
{
    public function __construct(
        \Mirasvit\Kb\Api\Service\Category\CategoryManagementInterface $categoryManagementInterface,
        \Mirasvit\Kb\Model\CategoryFactory $categoryFactory,
        ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->categoryManagementInterface = $categoryManagementInterface;
        $this->categoryFactory             = $categoryFactory;
        $this->resultForwardFactory        = $resultForwardFactory;
        $this->registry                    = $registry;
        $this->context                     = $context;
        $this->resultFactory               = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return \Mirasvit\Kb\Model\Category|false
     */
    protected function _initCategory()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $category = $this->categoryFactory->create()->load($id);
            $isAvailable = $this->categoryManagementInterface->isAvailableForStore($category);

            if ($category->getId() > 0 && $isAvailable) {
                $this->registry->register('kb_current_category', $category);

                return $category;
            }
        }

        return false;
    }
}
