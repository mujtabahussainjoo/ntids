<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ShippingRules\Helper\Data as Helper;

abstract class AbstractResourceModel extends AbstractDb
{
    const ALL_STORE_ID = '0';

    protected $priceFields = [];

    /**
     * Magento string lib
     *
     * @var StringUtils
     */
    protected $string;

    /**
     * @var \MageWorx\ShippingRules\Helper\Data
     */
    protected $helper;

    /**
     * @var array
     */
    protected $storeIds = [];

    /**
     * Associated entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [];

    /**
     * Store ids cache
     *
     * @var array
     */
    protected $storeIdsCache;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param StringUtils $string
     * @param \MageWorx\ShippingRules\Helper\Data $helper
     * @param StoreManagerInterface $storeManager
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        StringUtils $string,
        Helper $helper,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        $this->string = $string;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Add customer group ids and store ids to rule data after load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);
        $this->loadStoreIds($object);

        return $this;
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        foreach ($this->getPriceFields() as $field) {
            $data = $object->getData($field);
            $updatedData = $this->helper->getAmount($data);
            $object->setData($field, (float)$updatedData);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Save carrier's associated store labels.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->hasStoreIds()) {
            $storeIds = $object->getStoreIds();
            if (!is_array($storeIds)) {
                $storeIds = explode(',', (string)$storeIds);
            }

            $storeIds = $this->filterStoreIds($storeIds);
            if (in_array(static::ALL_STORE_ID, $storeIds) || empty($storeIds)) {
                $storeIds = [static::ALL_STORE_ID];
            }
            $object->setStoreIds($storeIds);

            $this->bindToEntity($object->getId(), $storeIds, 'store');
        }

        if ($object->hasStoreLabels()) {
            $this->saveStoreLabels($object->getId(), $object->getStoreLabels());
        }

        return parent::_afterSave($object);
    }

    /**
     * Returns fields with a price type (with $)
     *
     * @return array
     */
    public function getPriceFields()
    {
        return $this->priceFields;
    }

    /**
     * Filter store ids input column to prevent FK error when unexcited store was imported
     *
     * @param array $ids
     * @return array
     */
    protected function filterStoreIds($ids = [])
    {
        if (!$this->storeIdsCache) {
            $stores = $this->storeManager->getStores(true);
            $this->storeIdsCache = array_keys($stores);
        }

        $result = [];
        foreach ($ids as $key => $id) {
            if (in_array($id, $this->storeIdsCache)) {
                $result[] = $id;
            }
        }

        $result = array_unique($result);

        return $result;
    }

    ///////////////////////////////////////////////// STORE LABELS /////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Save entity labels for different store views
     *
     * @param int $entityId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveStoreLabels($entityId, $labels)
    {
        $deleteByStoreIds = [];
        $tableStoreLabels = $this->getStoreLabelsTable();
        $connection = $this->getConnection();

        $data = [];
        // Clear labels for the only existing store ids
        $storeIds = array_keys($labels);
        $filteredStoreIds = $this->filterStoreIds($storeIds);
        if (!empty($filteredStoreIds)) {
            foreach ($labels as $labelStoreId => $storeLabel) {
                if (in_array($labelStoreId, $filteredStoreIds)) {
                    $filteredLabels[$labelStoreId] = $storeLabel;
                }
            }
        } else {
            $filteredLabels = [];
        }

        foreach ($filteredLabels as $storeId => $label) {
            if ($label != '') {
                $data[] = [
                    $this->getStoreLabelsTableRefId() => $entityId,
                    'store_id' => $storeId,
                    'label' => $label
                ];
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $connection->beginTransaction();
        try {
            if (!empty($data)) {
                $connection->insertOnDuplicate($tableStoreLabels, $data, ['label']);
            }

            if (!empty($deleteByStoreIds)) {
                $connection->delete(
                    $tableStoreLabels,
                    [
                        $this->getStoreLabelsTableRefId() . '=?' => $entityId,
                        'store_id IN (?)' => $deleteByStoreIds
                    ]
                );
            }
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing entities labels
     *
     * @param int $entityId
     * @return array
     */
    public function getStoreLabels($entityId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getStoreLabelsTable(),
            ['store_id', 'label']
        )->where(
            $this->getStoreLabelsTableRefId() . ' = :' . $this->getStoreLabelsTableRefId()
        );
        return $this->getConnection()->fetchPairs($select, [':' . $this->getStoreLabelsTableRefId() => $entityId]);
    }

    /**
     * Get entity label by specific store id
     *
     * @param int $entityId
     * @param int $storeId
     * @return string
     */
    public function getStoreLabel($entityId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getStoreLabelsTable(),
            'label'
        )->where(
            $this->getStoreLabelsTableRefId() . ' = :' . $this->getStoreLabelsTableRefId()
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );

        return $this->getConnection()->fetchOne(
            $select,
            [
                ':' . $this->getStoreLabelsTableRefId() => $entityId,
                ':store_id' => $storeId
            ]
        );
    }

    /**
     * Get store labels table
     *
     * @return string
     */
    abstract protected function getStoreLabelsTable();

    /**
     * Get reference id column name from the labels table
     *
     * @return string
     */
    abstract protected function getStoreLabelsTableRefId();

    ////////////////////////////////////////////// ASSOCIATED ENTITIES /////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param AbstractModel $object
     * @return void
     */
    public function loadStoreIds(AbstractModel $object)
    {
        $this->storeIds = (array)$this->getStoreIds($object->getId());
        $object->setData('store_ids', $this->storeIds);
    }

    /**
     * Retrieve store ids of specified carrier
     *
     * @param int $id
     * @return array
     */
    public function getStoreIds($id)
    {
        return $this->getAssociatedEntityIds($id, 'store');
    }

    /**
     * Bind specified rules to entities
     *
     * @param int[]|int|string $ids
     * @param int[]|int|string $entityIds
     * @param string $entityType
     * @return $this
     * @throws \Exception
     */
    public function bindToEntity($ids, $entityIds, $entityType)
    {
        $this->getConnection()->beginTransaction();

        try {
            $this->_multiplyBunchInsert($ids, $entityIds, $entityType);
        } catch (\Exception $e) {
            $this->getConnection()->rollback();
            throw $e;
        }

        $this->getConnection()->commit();

        return $this;
    }

    /**
     * Multiply rule ids by entity ids and insert
     *
     * @param int|[] $ids
     * @param int|[] $entityIds
     * @param string $entityType
     * @return $this
     */
    protected function _multiplyBunchInsert($ids, $entityIds, $entityType)
    {
        if (empty($ids) || empty($entityIds)) {
            return $this;
        }
        if (!is_array($ids)) {
            $ids = [(int)$ids];
        }
        if (!is_array($entityIds)) {
            $entityIds = [(int)$entityIds];
        }
        $data = [];
        $count = 0;
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
        foreach ($ids as $id) {
            foreach ($entityIds as $entityId) {
                $data[] = [
                    $entityInfo['entity_id_field'] => $entityId,
                    $entityInfo['ref_id_field'] => $id,
                ];
                $count++;
                if ($count % 1000 == 0) {
                    $this->getConnection()->insertOnDuplicate(
                        $this->getTable($entityInfo['associations_table']),
                        $data,
                        [$entityInfo['ref_id_field']]
                    );
                    $data = [];
                }
            }
        }
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate(
                $this->getTable($entityInfo['associations_table']),
                $data,
                [$entityInfo['ref_id_field']]
            );
        }

        $this->getConnection()->delete(
            $this->getTable($entityInfo['associations_table']),
            $this->getConnection()->quoteInto(
                $entityInfo['ref_id_field'] . ' IN (?) AND ',
                $ids
            ) . $this->getConnection()->quoteInto(
                $entityInfo['entity_id_field'] . ' NOT IN (?)',
                $entityIds
            )
        );
        
        return $this;
    }

    /**
     * Unbind specified entity from entities
     *
     * @param int[]|int|string $ids
     * @param int[]|int|string $entityIds
     * @param string $entityType
     * @return $this
     */
    public function unbindFromEntity($ids, $entityIds, $entityType)
    {
        $connection = $this->getConnection();
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        if (!is_array($entityIds)) {
            $entityIds = [(int)$entityIds];
        }
        if (!is_array($ids)) {
            $ids = [(int)$ids];
        }

        $where = [];
        if (!empty($ids)) {
            $where[] = $connection->quoteInto($entityInfo['ref_id_field'] . ' IN (?)', $ids);
        }
        if (!empty($entityIds)) {
            $where[] = $connection->quoteInto($entityInfo['entity_id_field'] . ' IN (?)', $entityIds);
        }

        $connection->delete($this->getTable($entityInfo['associations_table']), implode(' AND ', $where));

        return $this;
    }

    /**
     * Retrieve associated entity Ids by entity type
     *
     * @param int $id
     * @param string $entityType
     * @return array
     */
    public function getAssociatedEntityIds($id, $entityType)
    {
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        $select = $this->getConnection()->select()->from(
            $this->getTable($entityInfo['associations_table']),
            [$entityInfo['entity_id_field']]
        )->where(
            $entityInfo['ref_id_field'] . ' = ?',
            $id
        );

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Retrieve customer group ids of specified entity
     *
     * @param int $id
     * @return array
     */
    public function getCustomerGroupIds($id)
    {
        return $this->getAssociatedEntityIds($id, 'customer_group');
    }

    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of entity's associated entity by specified entity type
     *
     * @param string $entityType
     * @return array
     * @throws LocalizedException
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