<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Rule extends AbstractDb
{
    /**
     * @var \Magento\Framework\DataObject
     */
    private $associatedEntityMap;

    /**
     * @var \Magento\Framework\DB\Helper
     */
    private $dbHelper;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\DataObject $associatedEntityMap,
        \Magento\Framework\DB\Helper $dbHelper,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->associatedEntityMap = $associatedEntityMap;
        $this->dbHelper = $dbHelper;
    }

    protected function _construct()
    {
        $this->_init('amasty_amrolepermissions_rule', 'id');
    }

    /**
     * @see Amasty/Rolepermissions/etc/di.xml
     * @return array
     */
    public function getReferenceConfig()
    {
        return $this->associatedEntityMap->getData();
    }

    /**
     * @param string $entityType
     *
     * @return array
     */
    public function getReferenceConfigEntity($entityType)
    {
        return $this->associatedEntityMap->getData($entityType);
    }

    /**
     * @param string $roles comma separated role IDs
     *
     * @return array
     */
    public function getAllowedUsersByRoles($roles)
    {
        $table = $this->getTable('authorization_role');
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $table,
            ['user_id']
        )->where(
            "{$table}.parent_id IN (?)",
            $roles
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param \Amasty\Rolepermissions\Model\Rule $object
     *
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $ruleId = $object->getId();
        if ($ruleId) {
            foreach ($this->getReferenceConfig() as $entityType => $referenceConfig) {
                if ($object->getData($referenceConfig['modeColumn']) != $referenceConfig['modeValue']) {
                    continue;
                }
                $select = $this->getReferenceSelect($ruleId, $entityType);
                $data = $this->getConnection()->fetchCol($select);

                $object->setData($entityType, $data);
                $object->setOrigData($entityType, $data);
            }
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param int    $ruleId
     * @param string $entityType
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getReferenceSelect($ruleId, $entityType)
    {
        $connection = $this->getConnection();
        $config = $this->getReferenceConfig();

        $referenceConfig = $config[$entityType];

        $select = $connection->select()
            ->from($this->getTable($referenceConfig['table']), [$referenceConfig['column']])
            ->where('rule_id = ?', $ruleId);

        return $select;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param string                       $entityType
     *
     * @return $this
     */
    public function joinRelation(\Magento\Framework\DB\Select $select, $entityType)
    {
        $referenceConfig = $this->getReferenceConfigEntity($entityType);

        $alias = $referenceConfig['table'];

        $fromPart = $select->getPart(\Zend_Db_Select::FROM);

        if (isset($fromPart[$alias])) {
            // avoid double join
            return $this;
        }

        $select->join(
            [$alias => $this->getTable($referenceConfig['table'])],
            sprintf('%s.rule_id = main_table.id', $alias),
            []
        );
        $select->group('main_table.id');

        $this->dbHelper->addGroupConcatColumn(
            $select,
            $entityType,
            sprintf('DISTINCT %s.%s', $alias, $referenceConfig['column'])
        );

        return $this;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param int[]|int                    $entityIds
     * @param string                       $entityType
     *
     * @return $this
     */
    public function addRelationFilter(\Magento\Framework\DB\Select $select, $entityIds, $entityType)
    {
        $this->joinRelation($select, $entityType);
        $referenceConfig = $this->getReferenceConfigEntity($entityType);

        $column = sprintf('%s.%s', $referenceConfig['table'], $referenceConfig['column']);
        if (is_array($entityIds)) {
            $select->where($column . ' IN (?)', $entityIds);
        } else {
            $select->where($column . ' = ?', $entityIds);
        }

        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param \Amasty\Rolepermissions\Model\Rule $object
     *
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $ruleId = $object->getId();
        foreach ($this->getReferenceConfig() as $entityType => $referenceConfig) {
            $table = $this->getTable($referenceConfig['table']);
            $select = $this->getReferenceSelect($ruleId, $entityType);

            if ($object->getData($referenceConfig['modeColumn']) != $referenceConfig['modeValue']
                || !$object->getData($entityType) || !array_filter($object->getData($entityType))
            ) {
                $query = $connection->deleteFromSelect($select, $table);
                $connection->query($query);
                continue;
            }

            $oldData = $connection->fetchCol($select);
            $newData = $object->getData($entityType);
            if (is_string($newData)) {
                $newData = explode(',', $newData);
            }
            $toDelete = array_diff($oldData, $newData);
            $toInsert = array_diff($newData, $oldData);

            if (!empty($toDelete)) {
                $deleteSelect = clone $select;
                $deleteSelect->where($referenceConfig['column'] . ' IN (?)', $toDelete);
                $query = $connection->deleteFromSelect($deleteSelect, $table);
                $connection->query($query);
            }
            if (!empty($toInsert)) {
                $insertArray = [];
                foreach ($toInsert as $value) {
                    if ($value) {
                        $insertArray[] = ['rule_id' => $ruleId, $referenceConfig['column'] => $value];
                    }
                }
                $connection->insertMultiple($table, $insertArray);
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Load all attributes, which are allowed in this rule
     *
     * @param int $ruleId
     *
     * @return array
     */
    public function getAllowedAttributes($ruleId)
    {
        $select = $this->getReferenceSelect($ruleId, 'attributes');
        $connection = $this->getConnection();
        $result = $connection->fetchCol($select);

        return $result;
    }
}
