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
 * @package   mirasvit/module-kb
 * @version   1.0.49
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Model;

class Search extends \Magento\Framework\DataObject
{
    /**
     * @var \Mirasvit\Core\Helper\Text
     */
    protected $coreString;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $dbResource;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Core\Helper\Text                $coreString
     * @param \Magento\Framework\App\ResourceConnection $dbResource
     * @param \Magento\Framework\Model\Context          $context
     * @param array                                     $data
     */
    public function __construct(
        \Mirasvit\Core\Helper\Text $coreString,
        \Magento\Framework\App\ResourceConnection $dbResource,
        \Magento\Framework\Model\Context $context,
        array $data = []
    ) {
        $this->coreString = $coreString;
        $this->dbResource = $dbResource;
        $this->context = $context;
        parent::__construct($data);
    }

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected $collection = null;

    /**
     * @var array
     */
    protected $attributes = null;

    /**
     * @var string
     */
    protected $primaryKey = null;

    /**
     * @param collection $collection
     *
     * @return $this
     */
    public function setSearchableCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setSearchableAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $key
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->primaryKey = $key;
    }

    /**
     * @param string $query
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _getMatchedIds($query)
    {
        if (!is_array($this->attributes) || !count($this->attributes)) {
            throw new \Magento\Framework\Exception\LocalizedException('Searchable attributes not defined');
        }

        $query = $this->coreString->splitWords($query, true, 100);
        $select = $this->collection->getSelect();

        $having = [];
        foreach ($query as $word) {
            $subhaving = [];
            foreach ($this->attributes as $attr => $weight) {
                $subhaving[] = $this->_getCILike($attr, $word, ['position' => 'any']);
            }
            $having[] = '('.implode(' OR ', $subhaving).')';
        }

        $havingCondition = implode(' AND ', $having);

        if ($havingCondition != '') {
            $select->having($havingCondition);
        }

        $read = $this->dbResource->getConnection('core_read');

        $stmt = $read->query($select);
        $result = [];
        while ($row = $stmt->fetch(\Zend_Db::FETCH_ASSOC)) {
            $result[$row[$this->primaryKey]] = 0;
        }

        return $result;
    }

    /**
     * @param string|array|\Zend_Db_Expr $field
     * @param string                     $value
     * @param array                      $options
     * @param string                     $type
     *
     * @return string
     */
    protected function _getCILike($field, $value, $options = [], $type = 'LIKE')
    {
        $read = $this->dbResource->getConnection('core_read');
        $quotedField = $read->quoteIdentifier($field);

        return $quotedField.' '.$type.' "'.$this->_escapeLikeValue($value, $options).'"';
    }

    /**
     * @param string $value
     * @param array  $options
     *
     * @return mixed|string
     */
    protected function _escapeLikeValue($value, $options = [])
    {
        $value = addslashes($value);

        $from = [];
        $to = [];
        if (empty($options['allow_string_mask'])) {
            $from[] = '%';
            $to[] = '\%';
        }
        if ($from) {
            $value = str_replace($from, $to, $value);
        }

        if (isset($options['position'])) {
            switch ($options['position']) {
                case 'any':
                    $value = '%'.$value.'%';
                    break;
                case 'start':
                    $value = $value.'%';
                    break;
                case 'end':
                    $value = '%'.$value;
                    break;
            }
        }

        return $value;
    }

    /**
     * @param string     $query
     * @param collection $collection
     * @param string     $mainTableKeyField
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function joinMatched($query, $collection, $mainTableKeyField = 'e.entity_id')
    {
        $matchedIds = $this->_getMatchedIds($query);
        $this->_createTemporaryTable($matchedIds);

        $collection->getSelect()->joinLeft(
            ['tmp_table' => $this->_getTemporaryTableName()],
            '(tmp_table.entity_id='.$mainTableKeyField.')',
            ['relevance' => 'tmp_table.relevance']
        );

        $collection->getSelect()->where('tmp_table.id IS NOT NULL');

        return $this;
    }

    /**
     * @param array $matchedIds
     *
     * @return $this
     */
    protected function _createTemporaryTable($matchedIds)
    {
        $values = [];

        foreach ($matchedIds as $id => $relevance) {
            $values[] = '('.$id.','.$relevance.')';
        }

        $connection = $this->dbResource->getConnection('core_read');

        $temporaryTable = $connection->newTable($this->_getTemporaryTableName());
        $temporaryTable->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true]
        );
        $temporaryTable->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => false]
        );
        $temporaryTable->addColumn(
            'relevance',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => false]
        );

        // Drop the temporary table in case it already exists on this (persistent?) connection.
        $connection->dropTemporaryTable($this->_getTemporaryTableName());
        $connection->createTemporaryTable($temporaryTable);

        if (count($values)) {
            $query = 'INSERT INTO `'.$this->_getTemporaryTableName().'` (`entity_id`, `relevance`)'.
                ' VALUES '.implode(',', $values).';';
            $connection->query($query);
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function _getTemporaryTableName()
    {
        return 'kb_search_results';
    }
}
