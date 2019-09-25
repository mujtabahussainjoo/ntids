<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report-api
 * @version   1.0.16
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Handler;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select\SelectRenderer;
use Mirasvit\ReportApi\Api\Config\ColumnInterface;
use Mirasvit\ReportApi\Api\Config\FieldInterface;
use Mirasvit\ReportApi\Api\Config\RelationInterface;
use Mirasvit\ReportApi\Api\Config\SelectInterface;
use Mirasvit\ReportApi\Api\Config\TableInterface;
use Mirasvit\ReportApi\Config\Schema;
use Mirasvit\ReportApi\Service\SelectService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Select extends \Magento\Framework\DB\Select implements SelectInterface
{
    /**
     * @var ColumnInterface[]
     */
    private $usedColumnsPool = [];

    /**
     * @var string[]
     */
    private $joinedTablesPool = [];

    /**
     * @var RelationInterface[]
     */
    private $usedRelationsPool = [];

    /**
     * @var \Magento\Framework\Module\Resource
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var TableInterface
     */
    private $baseTable;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var SelectService
     */
    private $selectService;

    public function __construct(
        ResourceConnection $resource,
        Schema $schema,
        SelectService $selectService,
        SelectRenderer $selectRenderer
    ) {
        $this->schema        = $schema;
        $this->selectService = $selectService;
        $this->resource      = $resource;

        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $adapter */
        $adapter = $resource->getConnection();

        parent::__construct($adapter, $selectRenderer);
    }

    /**
     * @param TableInterface $table
     * @return $this
     */
    public function setBaseTable($table)
    {
        $this->baseTable  = $table;
        $this->connection = $this->resource->getConnection($this->baseTable->getConnectionName());

        $this->joinedTablesPool[] = $table->getName();

        $this->from(
            [$table->getName() => $this->resource->getTableName($table->getName())],
            []
        );

        return $this;
    }

    public function addFieldToSelect(FieldInterface $field, $alias = null)
    {
        $field->join($this);

        $alias = $alias ? $alias : $field->getName();

        $this->columns([
            $alias => $field->toDbExpr(),
        ]);

        return $this;
    }

    /**
     * @param ColumnInterface $column
     * @param string          $alias
     * @return $this
     */
    public function addColumnToSelect(ColumnInterface $column, $alias = null)
    {
        //        $this->validateColumn($column);
        $this->usedColumnsPool[] = $column;

        $column->join($this);

        foreach ($column->getFields() as $field) {
            $field->join($this);
        }

        $alias = $alias ? $alias : $column->getIdentifier();

        $this->columns([
            $alias => $column->toDbExpr(),
        ]);

        return $this;
    }

    public function addFieldToGroup(FieldInterface $field)
    {
        $field->join($this);

        $this->group($field->toDbExpr());

        return $this;
    }

    /**
     * @param ColumnInterface $column
     * @return $this
     */
    public function addColumnToGroup(ColumnInterface $column)
    {
        $column->join($this);

        foreach ($column->getFields() as $field) {
            $field->join($this);
        }

        $this->usedColumnsPool[] = $column;

        $this->group($column->toDbExpr());

        return $this;
    }

    /**
     * @param ColumnInterface      $column
     * @param integer|string|array $condition
     * @return $this
     */
    public function addColumnToFilter(ColumnInterface $column, $condition)
    {
        //        $this->validateColumn($column);

        $this->usedColumnsPool[] = $column;

        $column->join($this);

        foreach ($column->getFields() as $field) {
            $field->join($this);
        }

        $conditionSql = $this->connection->prepareSqlCondition($column->toDbExpr(), $condition);

        if (strpos($conditionSql, 'COUNT(') !== false
            || strpos($conditionSql, 'AVG(') !== false
            || strpos($conditionSql, 'SUM(') !== false
            || strpos($conditionSql, 'CONCAT(') !== false
            || strpos($conditionSql, 'MIN(') !== false
            || strpos($conditionSql, 'MAX(') !== false
        ) {
            $this->having($conditionSql);
        } elseif ($condition) {
            $this->where($conditionSql);
        }


        return $this;
    }

    /**
     * @param ColumnInterface $column
     * @param string          $direction
     * @return $this
     */
    public function addColumnToOrder(ColumnInterface $column, $direction)
    {
        $column->join($this);

        foreach ($column->getFields() as $field) {
            $field->join($this);
        }

        $this->validateColumn($column);
        $this->usedColumnsPool[] = $column;

        $this->order(new \Zend_Db_Expr($column->toDbExpr() . ' ' . $direction));

        return $this;
    }

    private function validateColumn(ColumnInterface $column)
    {
        return true;
        if ($this->selectService->getRelationType($this->baseTable, $column->getTable()) !== RelationInterface::TYPE_ONE) {
            throw new \LogicException("Wrong column for select: {$column->getIdentifier()}");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assemble()
    {
        if (!count($this->_parts[self::FROM])) {
            $this->columns([
                new \Zend_Db_Expr(1),
            ]);
        }

        $query = parent::assemble();

        return $query;
    }

    /**
     * @param TableInterface $table
     * @return bool
     */
    public function joinTable($table)
    {
        if (in_array($table->getName(), $this->joinedTablesPool)) {
            return true;
        }

        $relations = $this->selectService->joinWay($this->baseTable, $table);
        //        $relations = array_reverse($relations);
        $isJoined = $relations ? true : false;

        /** @var RelationInterface $relation */
        foreach ($relations as $relation) {
            if (!in_array($relation->getRightTable()->getName(), $this->joinedTablesPool)) {
                $isJoined = $this->doJoinTable($relation->getRightTable(), $relation) ? $isJoined : false;
            }

            if (!in_array($relation->getLeftTable()->getName(), $this->joinedTablesPool)) {
                $isJoined = $this->doJoinTable($relation->getLeftTable(), $relation) ? $isJoined : false;
            }
        }

        return $isJoined;
    }

    /**
     * Join $tbl to current select based on relation condition.
     * @param TableInterface    $table
     * @param RelationInterface $relation
     * @return bool
     */
    private function doJoinTable(TableInterface $table, RelationInterface $relation)
    {
        $this->selectService->replicateTable($table, $this->baseTable);

        if ($this->leftJoin(
            [$table->getName() => $table->isTmp()
                ? $table->getName()
                : $this->resource->getTableName($table->getName()),
            ],
            $relation->getCondition(),
            []
        )) {
            $this->usedRelationsPool[] = $relation;
        }

        return $this;
    }

    /**
     * @param array  $name
     * @param string $cond
     * @param string $cols
     * @return bool
     */
    public function leftJoin($name, $cond, $cols = '*')
    {
        if (count($this->joinedTablesPool) > 50) {
            throw new \LogicException("Too many tables for join");
        }

        $n = implode('-', array_merge(array_keys($name), array_values($name)));

        if (!in_array($n, $this->joinedTablesPool)) {
            $this->joinedTablesPool[] = $n;

            parent::joinLeft($name, $cond, $cols);

            return true;
        }

        return false;
    }

    /**
     * @param array  $name
     * @param string $cond
     * @param string $cols
     * @return bool
     */
    public function rightJoin($name, $cond, $cols = '*')
    {
        if (count($this->joinedTablesPool) > 50) {
            throw new \LogicException("Too many tables for join");
        }

        $n = implode('-', array_merge(array_keys($name), array_values($name)));

        if (!in_array($n, $this->joinedTablesPool)) {
            $this->joinedTablesPool[] = $n;

            parent::joinRight($name, $cond, $cols);

            return true;
        }

        return false;
    }

    //    /**
    //     * @param TableInterface $table
    //     * @param RelationInterface[] $relations
    //     * @return RelationInterface[]
    //     */
    //    protected function joinWay($table, $relations = [])
    //    {
    //        if (in_array($table->getName(), $this->joinedTablesPool)) {
    //            return $relations;
    //        }
    //
    //
    //        // check direct relation
    //        foreach ($this->provider->getRelations() as $relation) {
    //            if (in_array($relation, $relations)) {
    //                continue;
    //            }
    //
    //            // Direct relation
    //            if ($relation->getLeftTable() === $table
    //                && in_array($relation->getRightTable()->getName(), $this->joinedTablesPool)
    //            ) {
    //                return array_merge($relations, [$relation]);
    //            }
    //
    //            // Direct relation
    //            if ($relation->getRightTable() === $table
    //                && in_array($relation->getLeftTable()->getName(), $this->joinedTablesPool)
    //            ) {
    //                return array_merge($relations, [$relation]);
    //            }
    //
    //        }
    //
    //        foreach ($this->provider->getRelations() as $relation) {
    //            if (in_array($relation, $relations)) {
    //                continue;
    //            }
    //
    //            if ($relation->getLeftTable() === $table) {
    //                if ($result = $this->joinWay($relation->getRightTable(), array_merge($relations, [$relation]))) {
    //                    return $result;
    //                }
    //            }
    //
    //            if ($relation->getRightTable() === $table) {
    //                if ($result = $this->joinWay($relation->getLeftTable(), array_merge($relations, [$relation]))) {
    //                    return $result;
    //                }
    //            }
    //        }
    //
    //        return [];
    //    }
}
