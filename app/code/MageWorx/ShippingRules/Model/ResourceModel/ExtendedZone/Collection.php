<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone;

use Magento\Framework\Exception\LocalizedException;
use Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection;
use Magento\Store\Model\Store;
use MageWorx\ShippingRules\Model\ExtendedZone as ExtendedZoneModel;

class Collection extends AbstractCollection
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Store associated with Pop-up Zone entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'store' => [
            'associations_table' => ExtendedZoneModel::EXTENDED_ZONE_STORE_TABLE_NAME,
            'object_id_field' => 'zone_id',
            'entity_id_field' => 'store_id',
        ],
    ];

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;

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
     * Set resource model and determine field mapping
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'MageWorx\ShippingRules\Model\ExtendedZone',
            'MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone'
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
        $this->_setIdFieldName('entity_id');
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
    protected function _toOptionArray($valueField = 'entity_id', $labelField = 'name', $additional = [])
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
     * Limit zones collection by specific stores
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
            'store_table.store_id',
            [
                ['eq' => $storeId],
                ['eq' => '0'],
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
        if ($field == 'store') {
            return $this->addStoreFilter($condition);
        }

        parent::addFieldToFilter($field, $condition);

        return $this;
    }

    /**
     * Join store table
     *
     * @throws LocalizedException
     */
    protected function joinStoreTable()
    {
        $entityInfo = $this->_getAssociatedEntityInfo('store');
        if (!$this->getFlag('is_store_table_joined')) {
            $this->setFlag('is_store_table_joined', true);
            $this->getSelect()->joinLeft(
                ['store_table' => $this->getTable($entityInfo['associations_table'])],
                'main_table.entity_id = store_table.' . $entityInfo['object_id_field'],
                []
            );
        }
    }

    /**
     * Perform operations after collection load
     *
     * @return AbstractCollection
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
        $ids = $this->getColumnValues('entity_id');
        if (!empty($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                [
                    ExtendedZoneModel::EXTENDED_ZONE_STORE_TABLE_NAME =>
                        $this->getTable(ExtendedZoneModel::EXTENDED_ZONE_STORE_TABLE_NAME),
                ]
            )->where(ExtendedZoneModel::EXTENDED_ZONE_STORE_TABLE_NAME . '.zone_id IN (?)', $ids);

            $result = $connection->fetchAll($select);
            if ($result) {
                $data = [];
                foreach ($result as $storeData) {
                    $data[$storeData['zone_id']][] = $storeData['store_id'];
                }
                $this->addStoresDataToItems($data);
            }
        }
    }

    /**
     * Add stores to each item
     *
     * @param array $data
     */
    protected function addStoresDataToItems($data)
    {
        foreach ($this as $item) {
            $linkedId = $item->getData('entity_id');
            if (!isset($data[$linkedId])) {
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
                ->setData('store_id', $data[$linkedId]);
        }
    }

    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of zone's associated entity by specified entity type
     *
     * @param string $entityType
     *
     * @throws LocalizedException
     * @return array
     */
    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        throw new LocalizedException(
            __('There is no information about associated entity type "%1".', $entityType)
        );
    }
}
