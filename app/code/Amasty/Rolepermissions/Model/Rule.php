<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Model;

use Amasty\Rolepermissions\Api\Data\RuleInterface;
use Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Products;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

/**
 * @method array|null getRoles() allowed roles
 * @method array|null getProducts() allowed products
 * @method array|null getCategories() allowed categories
 * @method array|null getScopeWebsites() allowed websites
 * @method array|null getScopeStoreviews() allowed stores
 * @method array|null getAttributes() allowed attributes
 * @method $this setRoles(array $roles)
 * @method $this setProducts(array $products)
 * @method $this setCategories(array $categories)
 * @method $this setScopeWebsites(array $websites)
 * @method $this setScopeStoreviews(array $storeviews)
 * @method $this setAttributes(array $attributes)
 */
class Rule extends \Magento\Framework\Model\AbstractModel implements RuleInterface
{
    const PRODUCT_ACCESS_MODE_ANY = '';

    const PRODUCT_ACCESS_MODE_MY = 0;

    const CATALOG = 'CatalogRule';

    const CART = 'SalesRule';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Amasty\Rolepermissions\Helper\Data $helper
     */
    protected $helper;

    /**
     * Rule constructor.
     *
     * @param \Magento\Framework\Model\Context           $context
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Model\Auth\Session        $authSession
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory      $productFactory
     * @param \Amasty\Rolepermissions\Helper\Data        $helper
     * @param array                                      $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Amasty\Rolepermissions\Helper\Data $helper,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_authSession = $authSession;
        $this->_productFactory = $productFactory;
        $this->helper = $helper;

        return parent::__construct($context, $registry, null, null, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Rolepermissions\Model\ResourceModel\Rule');
        $this->setIdFieldName('id');
    }

    public function loadByRole($roleId)
    {
        if (!$roleId) {
            return $this;
        }

        $this->load($roleId, 'role_id');

        $websites = $this->getScopeWebsites();

        if (!empty($websites)) {
            $stores = $this->_storeManager->getStores();

            $ids = [];

            foreach ($stores as $id => $store) {
                if (in_array($store->getWebsiteId(), $websites)) {
                    $ids[] = $id;
                }
            }

            $this->setScopeStoreviews($ids);
        }

        return $this;
    }

    public function getPartiallyAccessibleWebsites()
    {
        if (!$this->hasData('partial_ws')) {

            if ($this->getScopeWebsites()) {
                $websites = $this->getScopeWebsites();
            } else {
                if (!$this->getScopeStoreviews()) {
                    $websites = array_keys($this->_storeManager->getWebsites());
                } else {
                    $websitesMap = [];
                    foreach ($this->_storeManager->getStores() as $store) {
                        if (in_array($store->getId(), $this->getScopeStoreviews())) {
                            $websitesMap[$store->getWebsiteId()] = true;
                        }
                    }

                    $websites = array_keys($websitesMap);
                }
            }

            $this->setData('partial_ws', $websites);
        }

        return $this->getData('partial_ws');
    }

    public function restrictProductCollection(ProductCollection $collection)
    {
        $ruleConditions = [];
        $adapter = $this->getResource()->getConnection();
        $userId = $this->_authSession->getUser()->getId();
        $collection->addAttributeToSelect('amrolepermissions_owner', 'left');
        $allowOwn = false;
        $groupOwned = false;

        switch ($this->getProductAccessMode()) {
            case Products::MODE_ANY:
                break;
            case Products::MODE_SELECTED:
                $ruleConditions [] = $adapter->quoteInto(
                    'e.entity_id IN (?)',
                    $this->getProducts()
                );
                break;
            case Products::MODE_MY:
                $allowOwn = true;
                break;
            case Products::MODE_SCOPE:
                $groupOwned = true;
                break;
        }

        switch ($this->getCategoryAccessMode()) {
            case \Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Categories::MODE_SELECTED:
                if (!array_key_exists('cp', $collection->getSelect()->getPart(\Magento\Framework\DB\Select::FROM))) {
                    $collection->getSelect()
                        ->joinLeft(
                            ['cp' => $collection->getTable('catalog_category_product')],
                            'cp.product_id = e.entity_id',
                            []
                        );

                    $ruleConditions [] = $adapter->quoteInto(
                        'cp.category_id IN (?)',
                        $this->getCategories()
                    );
                    break;
                }
        }

        if ($this->getScopeAccessMode()) {
            $allowedWebsites = $this->getPartiallyAccessibleWebsites();

            $websiteTable = $collection
                ->getResource()
                ->getTable('catalog_product_website');

            $fromSelect = $collection->getSelect()->getPart('from');
            if (!isset($fromSelect['am_product_website'])) {
                $collection->getSelect()
                    ->join(
                        ['am_product_website' => $websiteTable],
                        'am_product_website.product_id = e.entity_id',
                        []
                    );
                $ruleConditions [] = $adapter->quoteInto(
                    'am_product_website.website_id IN (?)',
                    $allowedWebsites
                );
            }
        }

        $ownerCondition = $adapter->quoteInto(
            'at_amrolepermissions_owner.value = ?',
            $userId
        );

        if ($ruleConditions) {
            $ruleConditionsSql = implode(' AND ', $ruleConditions);
            $combinedCondition = "($ownerCondition OR ($ruleConditionsSql))";
            $collection->getSelect()->where($combinedCondition);
        }
        if ($allowOwn) {
            $collection->getSelect()->where($ownerCondition);
        }
        if ($groupOwned) {
            $users = $this->getCurrentRoleUsersId();
            $collection->addAttributeToFilter('amrolepermissions_owner', ['in' => $users]);
        }

        $collection->getSelect()->distinct();
    }

    public function getAllowedProductIds()
    {
        switch ($this->getProductAccessMode()) {
            case Products::MODE_ANY:
                return false;
            case Products::MODE_SELECTED:
                return $this->getProducts();
            case Products::MODE_SCOPE:
                $users = $this->getCurrentRoleUsersId();

                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
                $collection = $this->_productFactory->create();
                $collection->addAttributeToFilter('amrolepermissions_owner', ['in' => $users]);

                return $collection->getColumnValues('entity_id');

            case Products::MODE_MY:
                $userId = $this->_authSession->getUser()->getId();

                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
                $collection = $this->_productFactory->create()
                    ->addAttributeToFilter('amrolepermissions_owner', $userId);

                return $collection->getColumnValues('entity_id');
        }

        return false;
    }

    /**
     * Return users for current role
     *
     * @return array
     */
    private function getCurrentRoleUsersId()
    {
        if (!$this->_getData('current_role_user_ids')) {
            $this->setData(
                'current_role_user_ids',
                $this->_authSession->getUser()->getRole()->getRoleUsers()
            );
        }

        return $this->_getData('current_role_user_ids');
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function checkProductOwner($product)
    {
        $userId = $this->_authSession->getUser()->getId();

        return $product->getAmrolepermissionsOwner() == $userId;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function checkProductPermissions($product)
    {
        if ($this->getProductAccessMode() == Products::MODE_ANY || !$this->getProducts() || !$product->getId()) {
            return true;
        }

        return in_array($product->getId(), $this->getProducts());
    }

    public function getCollectionConfig()
    {
        if (!$this->hasData('collection_config')) {
            $config = [
                'external' => [
                    'store' => [
                        'Magento\Cms\Model\ResourceModel\Page' => $this->getResource()->getTable('cms_page_store'),
                        'Magento\Cms\Model\ResourceModel\Block' => $this->getResource()->getTable('cms_block_store'),
                        'Magento\Review\Model\ResourceModel\Rating' => $this->getResource()->getTable('rating_store'),
                        'Magento\Review\Model\ResourceModel\Review' => $this->getResource()->getTable('review_store'),
                        'Magento\Paypal\Model\ResourceModel\Billing\Agreement' => $this->getResource()->getTable('checkout_agreement_store')
                    ],
                    'website' => [
                        'Magento\CatalogRule\Model\ResourceModel\Rule' => $this->getResource()->getTable('catalogrule_website'),
                        'Magento\SalesRule\Model\ResourceModel\Rule' => $this->getResource()->getTable('salesrule_website')
                    ]
                ],
                'internal' => [
                    'store' => [
                        'Magento\Widget\Model\ResourceModel\Widget\Instance' => 'store_ids',
                    ],
                    'website' => [
                        'Magento\Customer\Model\ResourceModel\Customer' => 'website_id'
                    ]
                ]
            ];

            if ($this->getLimitOrders()) {
                $config['internal']['store']['Magento\Sales\Model\ResourceModel\Order'] = 'main_table.store_id';
            }

            if ($this->getLimitInvoices()) {
                $config['internal']['store']['Magento\Sales\Model\ResourceModel\Order\Invoice'] = 'main_table.store_id';
            }

            if ($this->getLimitShipments()) {
                $config['internal']['store']['Magento\Sales\Model\ResourceModel\Order\Shipment'] =
                    'main_table.store_id';
            }

            if ($this->getLimitMemos()) {
                $config['internal']['store']['Magento\Sales\Model\ResourceModel\Order\Creditmemo'] =
                    'main_table.store_id';
            }

            $this->setData('collection_config', $config);
        }

        return $this->getData('collection_config');
    }

    public function isAttributesInRole($priceRule, $type)
    {
        $isAttributesInRole = true;

        if (is_object($priceRule)) {
            $this->_registry->register('its_amrolepermissions', true, true);

            $priceRuleClone = clone $priceRule;
            $priceRuleAttrCodes = $this->_getRuleAttributeCodes($priceRuleClone, $type);
            $ruleAttrCodes = $this->helper->getAllowedAttributeCodes();

            if (is_array($ruleAttrCodes)) {
                foreach ($priceRuleAttrCodes as $priceRuleAttrCode) {
                    if (!in_array($priceRuleAttrCode, $ruleAttrCodes)) {
                        $isAttributesInRole = false;
                        break;
                    }
                }
            }

            $this->_registry->unregister('its_amrolepermissions');
        }

        return $isAttributesInRole;
    }

    protected function _getRuleAttributeCodes($rule, $type)
    {
        $attributeCodes = [];

        $ruleConditions = $rule->getConditions()->getConditions();

        $productType = 'Magento\\' . $type . '\Model\Rule\Condition\Product';

        $combineType1 = '';
        $combineType2 = '';
        if ($type == self::CART) {
            $combineType1 = 'Magento\SalesRule\Model\Rule\Condition\Product\Subselect';
            $combineType2 = 'Magento\SalesRule\Model\Rule\Condition\Product\Found';
        } elseif ($type == self::CATALOG) {
            $combineType1 = 'Magento\CatalogRule\Model\Rule\Condition\Combine';
        }

        if (!empty($ruleConditions)) {
            foreach ($ruleConditions as $ruleCondition) {
                if ($ruleCondition->getType() == $productType) {
                    $attributeCodes[] = $ruleCondition->getAttribute();
                }
                if ($ruleCondition->getType() == $combineType1
                    || $ruleCondition->getType() == $combineType2
                ) {
                    if (is_array($ruleCondition->getConditions())) {
                        foreach ($ruleCondition->getConditions() as $condition) {
                            $attributeCodes =
                                array_merge($attributeCodes, $this->_getCombineAttributes($condition, $type));
                        }
                    }
                }
            }
        }

        return $attributeCodes;
    }

    protected function _getCombineAttributes($condition, $type)
    {
        $productType = 'Magento\\' . $type . '\Model\Rule\Condition\Product';
        $combineType = 'Magento\\' . $type . '\Model\Rule\Condition\Combine';
        $combineAttributes = [];

        if ($condition->getType() == $productType) {
            $combineAttributes[] = $condition->getAttribute();
        } elseif ($condition->getType() == $combineType) {
            foreach ($condition->getConditions() as $subCondition) {
                $combineAttributes =
                    array_merge($combineAttributes, $this->_getCombineAttributes($subCondition, $type));
            }
        }

        return $combineAttributes;
    }

    /**
     * Get allowed admin users
     *
     * @return array
     */
    public function getAllowedUsers()
    {
        if (!$this->getRoles()) {
            return [];
        }

        return $this->getResource()->getAllowedUsersByRoles($this->getRoles());
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleId()
    {
        return $this->_getData(RuleInterface::ROLE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRoleId($roleId)
    {
        $this->setData(RuleInterface::ROLE_ID, $roleId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimitOrders()
    {
        return $this->_getData(RuleInterface::LIMIT_ORDERS);
    }

    /**
     * {@inheritdoc}
     */
    public function setLimitOrders($limitOrders)
    {
        $this->setData(RuleInterface::LIMIT_ORDERS, $limitOrders);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimitInvoices()
    {
        return $this->_getData(RuleInterface::LIMIT_INVOICES);
    }

    /**
     * {@inheritdoc}
     */
    public function setLimitInvoices($limitInvoices)
    {
        $this->setData(RuleInterface::LIMIT_INVOICES, $limitInvoices);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimitShipments()
    {
        return $this->_getData(RuleInterface::LIMIT_SHIPMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setLimitShipments($limitShipments)
    {
        $this->setData(RuleInterface::LIMIT_SHIPMENTS, $limitShipments);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimitMemos()
    {
        return $this->_getData(RuleInterface::LIMIT_MEMOS);
    }

    /**
     * {@inheritdoc}
     */
    public function setLimitMemos($limitMemos)
    {
        $this->setData(RuleInterface::LIMIT_MEMOS, $limitMemos);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductAccessMode()
    {
        return $this->_getData(RuleInterface::PRODUCT_ACCESS_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductAccessMode($productAccessMode)
    {
        $this->setData(RuleInterface::PRODUCT_ACCESS_MODE, $productAccessMode);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryAccessMode()
    {
        return $this->_getData(RuleInterface::CATEGORY_ACCESS_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryAccessMode($categoryAccessMode)
    {
        $this->setData(RuleInterface::CATEGORY_ACCESS_MODE, $categoryAccessMode);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopeAccessMode()
    {
        return $this->_getData(RuleInterface::SCOPE_ACCESS_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setScopeAccessMode($scopeAccessMode)
    {
        $this->setData(RuleInterface::SCOPE_ACCESS_MODE, $scopeAccessMode);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeAccessMode()
    {
        return $this->_getData(RuleInterface::ATTRIBUTE_ACCESS_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeAccessMode($attributeAccessMode)
    {
        $this->setData(RuleInterface::ATTRIBUTE_ACCESS_MODE, $attributeAccessMode);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleAccessMode()
    {
        return $this->_getData(RuleInterface::ROLE_ACCESS_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setRoleAccessMode($roleAccessMode)
    {
        $this->setData(RuleInterface::ROLE_ACCESS_MODE, $roleAccessMode);

        return $this;
    }

}
