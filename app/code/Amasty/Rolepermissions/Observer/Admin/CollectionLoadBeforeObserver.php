<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\DB\Select;
use Magento\Store\Model\Website;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\Category;

class CollectionLoadBeforeObserver implements ObserverInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $entity */
    protected $entity;

    /** @var \Amasty\Rolepermissions\Helper\Data\Proxy $helper */
    protected $helper;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var Website
     */
    private $website;


    /**
     * CollectionLoadBeforeObserver constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $entity
     * @param \Amasty\Rolepermissions\Helper\Data\Proxy $helper
     * @param Website $website
     * @param Store $store
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $entity,
        \Amasty\Rolepermissions\Helper\Data\Proxy $helper,
        Website $website,
        Store $store
    ) {
        $this->_coreRegistry = $registry;
        $this->entity = $entity;
        $this->helper = $helper;
        $this->website = $website;
        $this->store = $store;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getCollection();

        /** @var \Amasty\Rolepermissions\Model\Rule $rule */
        $rule = $this->_coreRegistry->registry('current_amrolepermissions_rule');

        if (!$rule) {
            return;
        }

        $config = $rule->getCollectionConfig();

        if ($rule->getScopeStoreviews()) {
            //restriction by stores on All Stores page
            if ($collection instanceof \Magento\Store\Model\ResourceModel\Store\Collection
                || $collection instanceof \Magento\Store\Model\ResourceModel\Website\Collection
            ) {
                /** Sometimes we shouldn't change store collection to load default values from default stores */
                if (!$this->_coreRegistry->registry('am_dont_change_collection')) {
                    $collectionClone = clone $collection;//for not loading of main collection before filters
                    $collectionData = $collectionClone->getData();
                    if (isset($collectionData[0])) {
                        if (key_exists('store_id', $collectionData[0])) {
                            $allowedStoreViews = $rule->getScopeStoreviews();
                            $collection->addFieldToFilter('store_id', ['in' => $allowedStoreViews]);
                        }
                    }
                } else {
                    $this->_coreRegistry->unregister('am_dont_change_collection');
                }
            }
            //restriction by stores on reports -> by customers page
            if ($collection instanceof \Magento\Reports\Model\ResourceModel\Review\Customer\Collection) {
                $collection->getSelect()
                    ->joinLeft(
                        ['review_detail' => $collection->getTable('review_detail')],
                        'review_detail.review_id = main_table.review_id',
                        []
                    )
                    ->joinLeft(
                        ['customer_entity' => $collection->getTable('customer_entity')],
                        'customer_entity.entity_id = review_detail.customer_id',
                        ['store_id']
                    );
                $allowedStoreViews = $rule->getScopeStoreviews();
                $collection->addFieldToFilter('customer_entity.store_id', ['in' => $allowedStoreViews]);
            }

            if ($collection instanceof \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection) {
                $collection->getSelect()->join(
                    ['am_rp_order' => $collection->getTable('sales_order')],
                    'am_rp_order.entity_id = main_table.order_id',
                    []
                );

                $config = ['internal' => ['store' => ['Magento\Sales\Model\ResourceModel\Order\Payment\Transaction' => 'am_rp_order.store_id']]];
            } elseif ($collection instanceof \Magento\Review\Model\ResourceModel\Review\Product\Collection) {
                $config = ['internal' => ['store' => ['Magento\Catalog\Model\ResourceModel\Product' => 'store.store_id']]];
            } elseif ($collection instanceof \Amasty\Groupcat\Model\ResourceModel\Rule\Collection) {
                $config = ['internal' => ['store' => ['\Amasty\Groupcat\Model\ResourceModel\Rules' => 'main_table.stores']]];
            } elseif ($collection instanceof \Magento\Reports\Model\ResourceModel\Product\Sold\Collection) {
                $config = ['internal' => ['store' => ['Magento\Sales\Model\ResourceModel\Order' => 'order_items.store_id']]];
            }

            foreach ($config as $joinType => $scopes) {
                foreach ($scopes as $scope => $collectionsConfig) {
                    foreach ($collectionsConfig as $modelType => $field) {
                        if (!($collection->getResource() instanceof $modelType)) {
                            continue;
                        }

                        if ($scope == 'store') {
                            $ids = $rule->getScopeStoreviews();
                            $ids[] = 0;
                        } else {
                            $ids = $rule->getPartiallyAccessibleWebsites();
                        }

                        if ($joinType == 'internal') {
                            $select = $collection->getSelect();
                            if (false === strpos($field, '.')) {
                                if ($alias = $this->getMainAlias($select)) {
                                    $field = "$alias.$field";
                                }
                            }
                            // sets intersection
                            $conditionSql = "";
                            foreach ($ids as $id) {
                                $conditionSql .= " OR $id IN ($field)";
                            }

                            $select->where("0 IN ($field) $conditionSql");
                        } else {
                            $fields = $collection->getConnection()->describeTable($field);
                            $idField = $collection->getResource()->getIdFieldName();
                            $idField = isset($fields[$idField]) ? $idField : 'row_id';
                            $select = $collection->getSelect();
                            $storeSelect = clone $select;
                            $storeSelect->reset()
                                ->from(['amrolepermissions_join' => $collection->getResource()->getTable($field)], $idField)
                                ->where("amrolepermissions_join.{$scope}_id IN (?)", $ids);
                            $select->where("main_table.$idField IN ($storeSelect)");
                        }

                        return;
                    }
                }
            }
        }

        if ($collection instanceof \Magento\Catalog\Model\ResourceModel\Category\Collection) {

            $ruleCategories = $rule->getCategories();
            if ($ruleCategories) {
                $ruleCategories = $this->helper->getParentCategoriesIds($ruleCategories);
                $collection->addFieldToFilter('entity_id', ['in' => $ruleCategories]);
            } else {
                $rootCategories = [];
                /** Hide categories from another store */
                if ($rule->getScopeWebsites() || $rule->getScopeStoreviews()) {
                    $storeIds = $rule->getScopeStoreviews();
                    if ($websites = $rule->getScopeWebsites()) {
                        foreach ($websites as $websiteId) {
                            $website = $this->website->load($websiteId);
                            $storeIds[] = $website->getStoreIds();
                        }
                    }
                    $storeIds = array_values($storeIds);
                    foreach ($storeIds as $storeId) {
                        /** @var \Magento\Store\Model\Store $store */
                        $store = $this->store->load($storeId);
                        if ($categoryRoot = $store->getRootCategoryId()) {
                            $rootCategories[] = $categoryRoot;
                        };
                    }
                }
                if ($rootCategories) {
                    $rootCategories = array_unique($rootCategories);
                    $allRootCategoryIds = $this->getRootCategoryIds($collection);
                    $deniedCategories = array_diff($allRootCategoryIds, $rootCategories);
                    if ($deniedCategories) {
                        $collection->getSelect()
                            ->where('e.entity_id NOT IN (?)', $deniedCategories);
                    }
                }
            }
        }

        $ruleAttributes = $rule->getAttributes();
        if ($ruleAttributes && !$this->_coreRegistry->registry('its_amrolepermissions')) {
            if ($collection instanceof \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection) {
                $collection->addFieldToFilter('main_table.attribute_id', ['in' => $ruleAttributes]);
            }
        }
    }

    /**
     * @param Select $select
     * @return bool|int|string
     */
    protected function getMainAlias(Select $select)
    {
        $from = $select->getPart(\Zend_Db_Select::FROM);
        foreach ($from as $alias => $data) {
            if ($data['joinType'] == 'from') {
                return $alias;
            }
        }
        return false;
    }

    /**
     * Get all root categories id
     *
     * @param $collection
     * @return array
     */
    private function getRootCategoryIds($collection)
    {
        $connection = $collection->getConnection();
        $select = $connection->select()->from(
            $collection->getMainTable(),
            'entity_id'
        )->where('parent_id = ?', Category::TREE_ROOT_ID);

        return $connection->fetchCol($select);
    }
}
