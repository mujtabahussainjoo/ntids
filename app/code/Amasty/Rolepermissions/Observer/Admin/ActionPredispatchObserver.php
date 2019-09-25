<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;

class ActionPredispatchObserver implements ObserverInterface
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\CatalogRule\Model\Rule $catalogRule
     */
    private $catalogRule;

    /**
     * @var \Magento\SalesRule\Model\Rule $cartRule
     */
    private $cartRule;

    /**
     * ActionPredispatchObserver constructor.
     *
     * @param \Magento\Framework\Registry                 $registry
     * @param \Amasty\Rolepermissions\Helper\Data         $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\CatalogRule\Model\Rule             $catalogRule
     * @param \Magento\SalesRule\Model\Rule               $catRule
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\CatalogRule\Model\Rule $catalogRule,
        \Magento\SalesRule\Model\Rule $catRule
    ) {
        $this->_coreRegistry = $registry;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->catalogRule = $catalogRule;
        $this->cartRule = $catRule;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var RequestInterface $request */
        $request = $observer->getRequest();

        if ($this->isWysiwygImages($request->getControllerName())) {
            return;
        }

        $rule = $this->helper->currentRule();

        if ($request->getActionName() == 'edit'
            && $rule->getAttributes()
        ) {
            $conditionWarningMessage = __('There are attributes used in conditions which you don\'t have access to. Rule cannot be deleted or saved.');

            if ($request->getRouteName() == 'catalog_rule') {
                /** @var \Magento\CatalogRule\Model\Rule $catalogRule */
                $catalogRule = $this->catalogRule->load($request->getParam('id'));
                $this->_coreRegistry->register('catalog_price_rule', $catalogRule, true);
                if (!$rule->isAttributesInRole($catalogRule, $rule::CATALOG)) {
                    $this->messageManager->addWarningMessage($conditionWarningMessage);
                }
            }

            if ($request->getRouteName() == 'sales_rule') {
                /** @var \Magento\SalesRule\Model\Rule $cartRule */
                $cartRule = $this->cartRule->load($request->getParam('id'));
                $this->_coreRegistry->register('cart_price_rule', $cartRule, true);
                if (!$rule->isAttributesInRole($cartRule, $rule::CART)) {
                    $this->messageManager->addWarningMessage($conditionWarningMessage);
                }
            }
        }

        if ($request->getControllerName() == 'product_set'
            && $request->getActionName() == 'index'
        ) {
            if ($this->helper->restrictAttributeSets()) {
                $this->messageManager->addWarningMessage(__('Attributes required to create an attribute set are restricted'));
            }
        }

        if (!$rule || !$rule->getScopeStoreviews()) {
            return;
        }

        if ($storeId = $request->getParam('store')) {
			if(!is_int($storeId))
			{
				$storeId_en = $storeId."_en";
				
				$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();    
				$storeManager = $objectManager->create("\Magento\Store\Model\StoreManagerInterface");
				
					$stores = $storeManager->getStores(true, false);
					foreach($stores as $store){
					if($store->getCode() === $storeId || $store->getCode() === $storeId_en){
						$storeId = $store->getId();
					}
				   }
			}
            if (is_array($storeId)) {
                $storeId = $storeId['store_id'];
            }
			
            if (!in_array($storeId, $rule->getScopeStoreviews())) {
                $this->helper->redirectHome();
            }
        } elseif ($websiteId = $request->getParam('website')) {
            if (is_array($websiteId)) {
                $websiteId = $websiteId['website_id'];
            }

            if (!$rule->hasScopeWebsites() || !in_array($websiteId, $rule->getScopeWebsites())) {
                $this->helper->redirectHome();
            }
        } elseif ($group = $request->getParam('group')) {
            if (is_array($group)) {
                $websiteId = $group['website_id'];

                if (!$rule->hasScopeWebsites() || !in_array($websiteId, $rule->getScopeWebsites())) {
                    $this->helper->redirectHome();
                }
            }
        }
    }

    private function isWysiwygImages($controllerName)
    {
        $isWysiwygImages = false;

        $wysiwygImageControllers = ['wysiwyg_images', 'cms_wysiwyg_images'];
        if (in_array($controllerName, $wysiwygImageControllers)) {
            $isWysiwygImages = true;
        }

        return $isWysiwygImages;
    }
}
