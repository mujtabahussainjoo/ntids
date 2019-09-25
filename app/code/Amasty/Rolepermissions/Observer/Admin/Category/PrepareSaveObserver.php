<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Category;

use Magento\Framework\Event\ObserverInterface;

class PrepareSaveObserver implements ObserverInterface
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rule = $this->helper->currentRule();

        if ($rule->getProducts() || $rule->getScopeStoreviews()) {
            if (false === $rule->getAllowedProductIds()) {
                return;
            }
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $observer->getCategory();

            $new = $category->getPostedProducts();

            if (is_null($new)) {
                $new = [];
            }

            $old = [];
            foreach ($category->getProductCollection() as $id => $product) {
                $old[$id] = $product->getCatIndexPosition();
            }

            $ids = $this->helper->combine(
                array_keys($old),
                array_keys($new),
                $rule->getAllowedProductIds()
            );

            $priorities = $new + $old;

            foreach ($priorities as $k => $v) {
                if (!in_array($k, $ids)) {
                    unset($priorities[$k]);
                }
            }

            $category->setPostedProducts($priorities);
        }
    }
}
