<?php
/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\MembershipSubscription\Block\Product\ProductList;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Catalog product related items block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Related extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Framework\DataObject\IdentityInterface
{
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Collection
     */
    protected $_itemCollection;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Checkout cart
     *
     * @var \Magento\Checkout\Model\ResourceModel\Cart
     */
    protected $_checkoutCart;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    
    /**
     *
     * @var \Magedelight\MembershipSubscription\Model\MembershipProductsFactory
     */
    protected $_membershipProductsFactory;
    
    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     *
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $_ruleCollectionFactory;

    /**
     *
     * @var \Magento\CatalogRule\Model\Rule
     */
    protected $_catalogRule;
    
    /**
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $_customerGroup;
    
    
    /**
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $membershipProductsFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\CatalogRule\Model\Rule $catalogRule
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\ResourceModel\Cart $checkoutCart,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Request\Http $request,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $membershipProductsFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\CatalogRule\Model\Rule $catalogRule,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        array $data = []
    ) {
        
        $this->_checkoutCart = $checkoutCart;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_checkoutSession = $checkoutSession;
        $this->moduleManager = $moduleManager;
        $this->_request = $request;
        $this->_membershipProductsFactory = $membershipProductsFactory;
        $this->_objectManager = $objectManager;
        $this->_productFactory = $productFactory;
        $this->_ruleCollectionFactory = $ruleCollectionFactory;
        $this->_catalogRule = $catalogRule;
        $this->_customerGroup = $customerGroup;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return $this
     */
    protected function _prepareData()
    {
        $memberproductId = $this->getRequest()->getParam('id');
        
        $model = $this->_membershipProductsFactory->create();
        
        $model->load($memberproductId, 'product_id');
        
        $customerGroupId = $model->getRelatedCustomerGroupId();
        
        $rules = $this->_ruleCollectionFactory->create();
        
        $rule_data= [];
        
        $customerGroups = $this->_customerGroup->toOptionArray();
        foreach ($customerGroups as $group) {
            $allCustomerGroupId[] = $group['value'];
        }
       
        foreach ($rules as $rule) {
            if ($rule->getIsActive()) {
                $rule_dd = $this->_catalogRule->load($rule->getId());
                $customer_ids = $rule_dd->getData('customer_group_ids');
                if (in_array($customerGroupId, $customer_ids)) {
                    $rule_data[]  = $rule->getId();
                }
            }
        }
        
        foreach ($rule_data as $key => $value) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $collections = $objectManager->create('Magento\CatalogRule\Model\Rule');
            $catalog_rule = $collections->load($value);
            $relatedProductIds[] = $catalog_rule->getMatchingProductIds();
        }

        $productIds = [];
        if (!empty($relatedProductIds)) {
            foreach ($relatedProductIds as $relatedProductId) {
                foreach ($relatedProductId as $productId => $product) {
                    if (isset($product) and ! empty($product)) {
                        if ($product[1] == 1) {
                            $productIds[] = $productId;
                        }
                    }
                }
            }
        }
        
        $this->_itemCollection = $this->_productFactory->create()->getCollection()
                ->addAttributeToSelect(
                    ('*')
                )->addAttributeToFilter(
                    'entity_id',
                    ['in' => $productIds]
                );
        
        
        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        
        $this->_itemCollection->setPageSize(6)->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    /**
     * @return Collection
     */
    public function getItems()
    {
        return $this->_itemCollection;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getItems() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        
        return $identities;
    }

    /**
     * Find out if some products can be easy added to cart
     *
     * @return bool
     */
    public function canItemsAddToCart()
    {
        foreach ($this->getItems() as $item) {
            if (!$item->isComposite() && $item->isSaleable() && !$item->getRequiredOptions()) {
                return true;
            }
        }
       
        return false;
    }
    
    public function getCurrentProductId()
    {
       
        $currentProductId = $this->getRequest()->getParam('id');
       
        if ($currentProductId) {
            $url = $this->getUrl('md_membership/view/membership', ['id' => $currentProductId]);
            return $url;
        } else {
            return false;
        }
    }
}
