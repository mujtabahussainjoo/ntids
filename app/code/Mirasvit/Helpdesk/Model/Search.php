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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Model;

class Search extends \Magento\Framework\DataObject
{
    /**
     * @var \Mirasvit\Core\Api\TextHelperInterface
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
     * @param \Mirasvit\Core\Api\TextHelperInterface           $coreString
     * @param \Magento\Framework\App\ResourceConnection $dbResource
     * @param \Magento\Framework\Model\Context          $context
     * @param array                                     $data
     */
    public function __construct(
        \Mirasvit\Core\Api\TextHelperInterface $coreString,
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
     * @var null
     */
    protected $_collection = null;

    /**
     * @var null
     */
    protected $_attributes = null;

    /**
     * @var null
     */
    protected $_primaryKey = null;

    /**
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     *
     * @return $this
     */
    public function setSearchableCollection($collection)
    {
        $this->_collection = $collection;

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setSearchableAttributes($attributes)
    {
        $this->_attributes = $attributes;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->_primaryKey = $key;
    }

    /**
     * @param string $query
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _getMatchedIds($query)
    {
        if (!is_array($this->_attributes) || !count($this->_attributes)) {
            throw new \Magento\Framework\Exception\LocalizedException('Searchable attributes not defined');
        }

        $query = $this->coreString->splitWords($query, true, 100);
        $select = $this->_collection->getSelect();

        $having = [];
        foreach ($query as $word) {
            $subhaving = [];
            foreach ($this->_attributes as $attr => $weight) {
                $subhaving[] = $this->_getCILike($attr, $word, ['position' => 'any']);
            }
            $having[] = '('.implode(' OR ', $subhaving).')';
        }

        $havingCondition = implode(' AND ', $having);

        if ($havingCondition != '') {
            $select->having($havingCondition);
        }

        $read = $this->dbResource->getConnection('core_read');
        $read->query('SET group_concat_max_len = 4294967295;'); //we need this, because we use group_concat in query
        $stmt = $read->query($select);
        $result = [];
        while ($row = $stmt->fetch(\Zend_Db::FETCH_ASSOC)) {
            $result[$row[$this->_primaryKey]] = 0;
        }

        return $result;
    }

    /**
     * @param string $field
     * @param string $value
     * @param array  $options
     * @param string $type
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
     * @return object|string
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
     * @param string                                        $query
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     * @param string                                        $mainTableKeyField
     *
     * @return $this
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
     * @param int[] $matchedIds
     * @return $this
     */
    protected function _createTemporaryTable($matchedIds)
    {
        $values = [];

        foreach ($matchedIds as $id => $relevance) {
            $values[] = '('.$id.','.$relevance.')';
        }

        $connection = $this->dbResource->getConnection('core_read');

        $query = '';
        $query .= 'CREATE TEMPORARY TABLE IF NOT EXISTS `'.$this->_getTemporaryTableName().'` ('
                .'`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `entity_id` int(11) unsigned NOT NULL,
                `relevance` int(11) unsigned NOT NULL,
                PRIMARY KEY (`id`)
                )ENGINE=MYISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

        if (count($values)) {
            $query .= 'INSERT INTO `'.$this->_getTemporaryTableName().'` (`entity_id`, `relevance`)'.
                'VALUES '.implode(',', $values).';';
        }
        foreach (explode(';', $query) as $q) {
            if (!trim($q)) {
                continue;
            }
            $connection->query($q);
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function _getTemporaryTableName()
    {
        return 'search_results';
    }
}
