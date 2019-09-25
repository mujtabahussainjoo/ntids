<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Framework\Event\ObserverInterface;

class SaveBeforeObserver implements ObserverInterface
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

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResource;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->authorization = $authorization;
        $this->authSession = $authSession;
        $this->productResource = $productResource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->request->getModuleName() == 'api') {
            return;
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getProduct();

        if (!$this->authorization->isAllowed('Amasty_Rolepermissions::save_products')) {
            $this->helper->redirectHome();
        }

        if (!$this->authorization->isAllowed('Amasty_Rolepermissions::product_owner')
            && $this->authSession->getUser()) {
            $product->unsetData('amrolepermissions_owner');
        }

        $rule = $this->helper->currentRule();

        if (!$rule) {
            return;
        }

        if (!$rule->checkProductPermissions($product)
            && !$rule->checkProductOwner($product)
        ) {
            $this->helper->redirectHome();
        }

        if ($rule->getScopeStoreviews()) {
            $orig = $this->productResource->getWebsiteIds($product);
            $new = $product->getData('website_ids');

            if ($orig != $new && !is_null($new)) {
                $ids = $this->helper->combine($orig, $new, $rule->getPartiallyAccessibleWebsites());
                $product->setWebsiteIds($ids);
            }

            if (!$product->getId()) {
                $product->setData('amrolepermissions_disable');
            }
        }

        if ($rule->getCategories()) {
            $ids = $this->helper->combine(
                $product->getOrigData('category_ids'),
                $product->getCategoryIds(),
                $rule->getCategories()
            );
            $product->setCategoryIds($ids);
        }
    }
}
