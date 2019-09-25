<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ResourceModel;

use Magento\Store\Model\Store;

abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $_associatedEntitiesMap = [];

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->date = $date;
        $this->storeManager = $storeManager;
    }

    /**
     * Join store table
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function joinStoreTable()
    {
        $entityInfo = $this->_getAssociatedEntityInfo('store');
        if (!$this->getFlag('is_store_table_joined')) {
            $this->setFlag('is_store_table_joined', true);
            $this->getSelect()->joinLeft(
                ['store' => $this->getTable($entityInfo['associations_table'])],
                'main_table.' . $entityInfo['main_table_id_field'] . ' = store.' .
                $entityInfo['linked_table_id_field'],
                []
            );
        }
    }


    /**
     * Perform operations after collection load
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|$this
     */
    protected function _afterLoad()
    {
        $this->joinStoreTable();
        $this->addStoreData();
        return parent::_afterLoad();
    }

    /**
     * Adds store data to the items
     */
    protected function addStoreData()
    {
        $mainTableIdField = $this->_associatedEntitiesMap['store']['main_table_id_field'];
        $associatedTable = $this->_associatedEntitiesMap['store']['associations_table'];
        $linkedTableIdFieldName = $this->_associatedEntitiesMap['store']['linked_table_id_field'];
        $storeIdFieldName = $this->_associatedEntitiesMap['store']['entity_id_field'];

        $ids = $this->getColumnValues($mainTableIdField);
        if (count($ids)) {
            $connection = $this->getConnection();

            $select = $connection->select()->from(
                [
                    $associatedTable => $this->getTable($associatedTable)
                ]
            )->where($associatedTable . '.' . $linkedTableIdFieldName . ' IN (?)', $ids);

            $result = $connection->fetchAll($select);
            $data = [];
            if ($result) {
                foreach ($result as $storeData) {
                    $data[$storeData[$linkedTableIdFieldName]][] = $storeData[$storeIdFieldName];
                }
            }
            $this->addStoresDataToItems($data);
        }
    }

    /**
     * Add stores to each item
     *
     * @param array $data
     */
    protected function addStoresDataToItems($data)
    {
        $mainTableIdField = $this->_associatedEntitiesMap['store']['main_table_id_field'];

        foreach ($this as $item) {
            $linkedId = $item->getData($mainTableIdField);
            if (!isset($data[$linkedId]) || !$data[$linkedId]) {
                $item->setData('store_ids', [0]);
                continue;
            }

            $storeIdKey = array_search(Store::DEFAULT_STORE_ID, $data[$linkedId], true);
            if ($storeIdKey !== false) {
                $stores = $this->storeManager->getStores(false, true);
                $storeId = current($stores)->getId();
                $storeCode = key($stores);
            } else {
                $storeId = current($data[$linkedId]);
                $store = $this->storeManager->getStore($storeId);
                $storeCode = $store->getCode();
            }

            $item->setData('_first_store_id', $storeId)
                ->setData('store_code', $storeCode)
                ->setData('store_ids', $data[$linkedId]);
        }
    }

    /**
     * Limit entity collection by specific stores
     *
     * @param int|int[]|Store $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->joinStoreTable();
        if ($storeId instanceof Store) {
            $storeId = $storeId->getId();
        }

        parent::addFieldToFilter(
            'store.store_id',
            [
                ['eq' => $storeId],
                ['eq' => '0'],
                ['null' => true]
            ]
        );

        $this->getSelect()->distinct(true);

        return $this;
    }

    /**
     * Provide support for store id filter
     *
     * @param string $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_ids') {
            return $this->addStoreFilter($condition);
        }

        parent::addFieldToFilter($field, $condition);
        return $this;
    }

    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of entities associated entity by specified entity type
     *
     * @param string $entityType
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        throw new \Magento\Framework\Exception\LocalizedException(
            __('There is no information about associated entity type "%1".', $entityType)
        );
    }

    /**
     * Convert items array to array for select options
     *
     * return items array
     * array(
     *      $index => array(
     *          'value' => mixed
     *          'label' => mixed
     *      )
     * )
     *
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'entity_id', $labelField = 'title', $additional = [])
    {
        $res = [];
        $additional['value'] = $valueField;
        $additional['label'] = $labelField;

        foreach ($this as $item) {
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
            }
            $res[] = $data;
        }

        return $res;
    }

    /**
     * Let do something before add loaded item in collection
     *
     * @TODO Check this strange afterLoad later, may be we should remove it
     *
     * @param \Magento\Framework\DataObject $item
     * @return \Magento\Framework\DataObject
     */
    protected function beforeAddLoadedItem(\Magento\Framework\DataObject $item)
    {
        if ($item instanceof \Magento\Framework\Model\AbstractModel) {
            /** @var \MageWorx\ShippingRules\Model\Carrier\AbstractModel $item */
            $this->getResource()->unserializeFields($item);
            $this->getResource()->afterLoad($item);
            $item->afterLoad();
            $item->setOrigData();
            $item->setHasDataChanges(false);
        }

        return parent::beforeAddLoadedItem($item);
    }
}
