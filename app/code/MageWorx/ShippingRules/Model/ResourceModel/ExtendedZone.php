<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use MageWorx\ShippingRules\Model\ExtendedZone as ExtendedZoneModel;

class ExtendedZone extends AbstractDb
{
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
     * @var array
     */
    protected $storeIds = [];

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param string|null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        $connectionName = null
    ) {
        $this->string = $string;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ExtendedZoneModel::EXTENDED_ZONE_TABLE_NAME, 'entity_id');
    }

    /**
     * Add store ids to Pop-up Zone data after load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->loadStoreIds($object);

        return parent::_afterLoad($object);
    }

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
     * Retrieve store ids of specified zone
     *
     * @param int $zoneId
     * @return array
     */
    public function getStoreIds($zoneId)
    {
        return $this->getAssociatedEntityIds($zoneId, 'store');
    }

    /**
     * Retrieve rule's associated entity Ids by entity type
     *
     * @param int $ruleId
     * @param string $entityType
     * @return array
     */
    public function getAssociatedEntityIds($ruleId, $entityType)
    {
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        $select = $this->getConnection()->select()->from(
            $this->getTable($entityInfo['associations_table']),
            [$entityInfo['entity_id_field']]
        )->where(
            $entityInfo['object_id_field'] . ' = ?',
            $ruleId
        );

        return $this->getConnection()->fetchCol($select);
    }


    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of zone's associated entity by specified entity type
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

    /**
     * Bind Pop-up Zone to the store(s).
     * Save zone's associated store labels.
     *
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->hasStoreLabels()) {
            $this->saveStoreLabels($object->getId(), $object->getStoreLabels());
        }

        if ($object->hasStoreId()) {
            $storeIds = $object->getStoreId();
            if (!is_array($storeIds)) {
                $storeIds = explode(',', (string)$storeIds);
            }
            $this->bindRuleToEntity($object->getId(), $storeIds, 'store');
        }

        return parent::_afterSave($object);
    }

    /**
     * Bind specified zone to entities
     *
     * @param int[]|int|string $objectIds
     * @param int[]|int|string $entityIds
     * @param string $entityType
     * @return $this
     * @throws \Exception
     */
    public function bindRuleToEntity($objectIds, $entityIds, $entityType)
    {
        $this->getConnection()->beginTransaction();

        try {
            $this->_multiplyBunchInsert($objectIds, $entityIds, $entityType);
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }

        $this->getConnection()->commit();

        return $this;
    }

    /**
     * Multiply zone ids by entity ids and insert
     *
     * @param int|[] $objectIds
     * @param int|[] $entityIds
     * @param string $entityType
     * @return $this
     */
    protected function _multiplyBunchInsert($objectIds, $entityIds, $entityType)
    {
        if (empty($objectIds) || empty($entityIds)) {
            return $this;
        }
        if (!is_array($objectIds)) {
            $objectIds = [(int)$objectIds];
        }
        if (!is_array($entityIds)) {
            $entityIds = [(int)$entityIds];
        }
        $data = [];
        $count = 0;
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
        foreach ($objectIds as $objectId) {
            foreach ($entityIds as $entityId) {
                $data[] = [
                    $entityInfo['entity_id_field'] => $entityId,
                    $entityInfo['object_id_field'] => $objectId,
                ];
                $count++;
                if ($count % 1000 == 0) {
                    $this->getConnection()->insertOnDuplicate(
                        $this->getTable($entityInfo['associations_table']),
                        $data,
                        [$entityInfo['object_id_field']]
                    );
                    $data = [];
                }
            }
        }
        if (!empty($data)) {
            $this->getConnection()->insertOnDuplicate(
                $this->getTable($entityInfo['associations_table']),
                $data,
                [$entityInfo['object_id_field']]
            );
        }

        $this->getConnection()->delete(
            $this->getTable($entityInfo['associations_table']),
            $this->getConnection()->quoteInto(
                $entityInfo['object_id_field'] . ' IN (?) AND ',
                $objectIds
            ) . $this->getConnection()->quoteInto(
                $entityInfo['entity_id_field'] . ' NOT IN (?)',
                $entityIds
            )
        );
        return $this;
    }

    /**
     * Save zone labels for different store views
     *
     * @param int $entityId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveStoreLabels($entityId, $labels)
    {
        $deleteByStoreIds = [];
        $table = $this->getTable(ExtendedZoneModel::EXTENDED_ZONE_LABELS_TABLE_NAME);
        $connection = $this->getConnection();

        $data = [];
        foreach ($labels as $storeId => $label) {
            if ($label != '') {
                $data[] = ['zone_id' => $entityId, 'store_id' => $storeId, 'label' => $label];
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $connection->beginTransaction();
        try {
            if (!empty($data)) {
                $connection->insertOnDuplicate($table, $data, ['label']);
            }

            if (!empty($deleteByStoreIds)) {
                $connection->delete($table, ['zone_id=?' => $entityId, 'store_id IN (?)' => $deleteByStoreIds]);
            }
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing zone labels
     *
     * @param int $entityId
     * @return array
     */
    public function getStoreLabels($entityId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(ExtendedZoneModel::EXTENDED_ZONE_LABELS_TABLE_NAME),
            ['store_id', 'label']
        )->where(
            'zone_id = :zone_id'
        );

        return $this->getConnection()->fetchPairs($select, [':zone_id' => $entityId]);
    }

    /**
     * Get zone label by specific store id
     *
     * @param int $entityId
     * @param int $storeId
     * @return string
     */
    public function getStoreLabel($entityId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable(ExtendedZoneModel::EXTENDED_ZONE_LABELS_TABLE_NAME),
            'label'
        )->where(
            'zone_id = :zone_id'
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );

        return $this->getConnection()->fetchOne($select, [':zone_id' => $entityId, ':store_id' => $storeId]);
    }

    protected $arrayFields = [
        'countries_id'
    ];

    /**
     * Perform actions before object save
     *
     * @param AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        foreach ($this->arrayFields as $fieldName) {
            $data = $object->getData($fieldName);
            if (is_array($data)) {
                $object->setData($fieldName, implode(',', $data));
            }
        }
        if (!$object->getId()) {
            $object->setId(null);
            $object->isObjectNew(true);
        }
        $object->setData('updated_at', time());
        parent::_beforeSave($object);

        return $this;
    }
}
