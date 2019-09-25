<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Category;

use Magento\Framework\Event\ObserverInterface;

class SaveAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\Rolepermissions\Model\ResourceModel\Rule\CollectionFactory
     */
    private $ruleCollectionFactory;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Amasty\Rolepermissions\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
    ) {
        $this->request = $request;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $categoryId = (int) $this->request->getParam('id');
        if (!$categoryId) { // New category
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $observer->getCategory();

            $this->updateSubcategoryPermissions($category);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     */
    private function updateSubcategoryPermissions($category)
    {
        /** @var \Amasty\Rolepermissions\Model\ResourceModel\Rule\Collection $ruleCollection */
        $ruleCollection = $this->ruleCollectionFactory->create();
        $ruleCollection->addCategoriesFilter($category->getParentId());

        foreach ($ruleCollection->getItems() as $rule) {
            // joined value is in string
            $categories = explode(',', $rule->getCategories());
            $categories[] = $category->getId();

            $rule->setCategories(array_unique($categories))
                ->save();
        }
    }
}
