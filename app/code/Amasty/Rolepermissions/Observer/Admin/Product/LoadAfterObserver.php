<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Framework\Event\ObserverInterface;

class LoadAfterObserver implements ObserverInterface
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->authorization = $authorization;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->request->getModuleName() == 'api') {
            return;
        }

        if ($this->request->getModuleName() !== 'catalog' && $this->request->getControllerName() !== 'product') {
            return;
        }

        $rule = $this->helper->currentRule();

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getProduct();

        if ($product->getData('_edit_mode')
            && !$rule->checkProductOwner($product)
        ) { // Indexer fix
            if (!$rule->checkProductPermissions($product)) {
                $this->helper->redirectHome();
            }

            $categories = $rule->getCategories();
            if ($categories) {
                $productCategories = $product->getCategoryIds();
                if (!array_intersect($productCategories, $categories)) {
                    $this->helper->redirectHome();
                }
            }
        }

        if (!$this->authorization->isAllowed('Amasty_Rolepermissions::delete_products')) {
            $product->setIsDeleteable(false);
        }
    }
}
