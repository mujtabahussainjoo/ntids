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

namespace Magedelight\MembershipSubscription\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel;

class Layer extends \Magento\Catalog\Model\Layer
{
    
    protected $_productCollections = [];
     
    /**
     * @var \Magento\Catalog\Model\Config
     */
    
    protected $catalogConfig;
      
    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;
    
    /**
     * @var \Magedelight\MembershipSubscription\Model\MembershipProductsFactory
     */
    protected $_membershipProductsFactory;
    
    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $_ruleCollectionFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule
     */
    protected $_catalogRule;
    
    /**
     * Product factory.
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;


    /**
     * @param ContextInterface $context
     * @param StateFactory $layerStateFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $catalogProduct
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productcollectionFactory
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $membershipProductsFactory
     * @param \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\CatalogRule\Model\Rule $catalogRule
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\ContextInterface $context,
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $catalogProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productcollectionFactory,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $membershipProductsFactory,
        \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\CatalogRule\Model\Rule $catalogRule,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );
        $this->request = $request;
        $this->catalogConfig = $catalogConfig;
        $this->productVisibility = $productVisibility;
        $this->mdproductcollection = $productcollectionFactory;
        $this->_membershipProductsFactory = $membershipProductsFactory;
        $this->_ruleCollectionFactory = $ruleCollectionFactory;
        $this->_catalogRule = $catalogRule;
        $this->_productFactory = $productFactory;
    }
    
    /*
     * get Product Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->request->getParam('id')])) {
            $collection = $this->_productCollections[$this->request->getParam('id')];
        } else {
            $memberproductId = $this->request->getParam('id');
            
            $model = $this->_membershipProductsFactory->create();

            $model->load($memberproductId, 'product_id');

            $customerGroupId = $model->getRelatedCustomerGroupId();

            $rules = $this->_ruleCollectionFactory->create();

            $rule_data = [];

            foreach ($rules as $rule) {
                if ($rule->getIsActive()) {
                    $rule_dd = $this->_catalogRule->load($rule->getId());
                    $customer_ids = $rule_dd->getData('customer_group_ids');
                    if (in_array($customerGroupId, $customer_ids)) {
                        $rule_data[] = $rule->getId();
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
            $collection = $this->_productFactory->create()->getCollection()
                                ->addAttributeToSelect(
                                    ('*')
                                )->addAttributeToFilter('entity_id', ['in' => $productIds]);

           
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->request->getParam('id')] = $collection;
        }

        return $collection;
    }
    
    
    public function prepareProductCollection($collection)
    {
        
            $collection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->setStore($this->_storeManager->getStore())
            ->addMinimalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());
       
        return $this;
    }
}
