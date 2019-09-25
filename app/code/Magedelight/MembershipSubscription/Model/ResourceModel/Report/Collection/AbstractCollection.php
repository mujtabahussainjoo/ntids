<?php

/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\MembershipSubscription\Model\ResourceModel\Report\Collection;

class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * From date
     *
     * @var string
     */
    protected $_from = null;

    /**
     * To date
     *
     * @var string
     */
    protected $_to = null;

    /**
     * Period
     *
     * @var string
     */
    protected $_period = null;

    /**
     * Category
     *
     * @var string
     */
    protected $_membership_plan = null;

    
    protected $_type = null;

    /**
     * Store ids
     *
     * @var int|array
     */
    protected $_storesIds = 0;

    /**
     * Is totals
     *
     * @var bool
     */
    protected $_isTotals = false;

    /**
     * Is subtotals
     *
     * @var bool
     */
    protected $_isSubTotals = false;

    /**
     * Aggregated columns
     *
     * @var array
     */
    protected $_aggregatedColumns = [];

    /**
     * Order status
     *
     * @var string
     */
    protected $_orderStatus = null;
    
    /**
     *
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magedelight\MembershipSubscription\Model\ResourceModel\Report $resource
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magedelight\MembershipSubscription\Model\ResourceModel\Report $resource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->setModel('Magento\Reports\Model\Item');
    }

    
    /**
     * Set array of columns that should be aggregated
     * @codeCoverageIgnore
     *
     * @param array $columns
     * @return $this
     */
    public function setAggregatedColumns(array $columns)
    {


        $this->_aggregatedColumns = $columns;
        return $this;
    }

    /**
     * Retrieve array of columns that should be aggregated
     * @codeCoverageIgnore
     *
     * @return array
     */
    public function getAggregatedColumns()
    {
        return $this->_aggregatedColumns;
    }

    /**
     * Set date range
     * @codeCoverageIgnore
     *
     * @param mixed $from
     * @param mixed $to
     * @return $this
     */
    public function setDateRange($from = null, $to = null)
    {
        $this->_from = $from;
        $this->_to = $to;
        return $this;
    }
    
    /**
     * Apply needed aggregated table
     *
     * @return $this
     */
    protected function _applyAggregatedTable()
    {
        return $this;
    }

    /**
     * Apply date range filter
     *
     * @return $this
     */
    protected function _applyDateRangeFilter()
    {
        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases
        if ($this->_from !== null) {
            $this->getSelect()->where('sales_order.created_at >= ?', $this->_from);
        }
        if ($this->_to !== null) {
            $this->getSelect()->where('sales_order.created_at <= ?', $this->_to);
        }

        return $this;
    }

    /**
     * Set store ids
     *
     * @param mixed $storeIds (null, int|string, array, array may contain null)
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->_storesIds = $storeIds;
        return $this;
    }

    /**
     * Apply stores filter to select object
     *
     * @param \Magento\Framework\DB\Select $select
     * @return $this
     */
    protected function _applyStoresFilterToSelect(\Magento\Framework\DB\Select $select)
    {

         $nullCheck = false;
         $storeIds = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        return $this;
    }

    /**
     * Apply stores filter
     *
     * @return $this
     */
    protected function _applyStoresFilter()
    {
        return $this->_applyStoresFilterToSelect($this->getSelect());
    }

    
    /**
     * Getter/Setter for isTotals
     *
     * @param null|bool $flag
     * @return $this
     */
    public function isTotals($flag = null)
    {
        if ($flag === null) {
            return $this->_isTotals;
        }
        $this->_isTotals = $flag;
        return $this;
    }

    /**
     * Custom filters application ability
     *
     * @return $this
     */
    protected function _applyCustomFilter()
    {

        return $this;
    }

    /**
     * Set category name
     * @codeCoverageIgnore
     *
     * @param string $category
     * @return $this
     */
    public function setMembershipPlan($membership_plan)
    {
        
        $this->_membership_plan = $membership_plan;
        $this->getSelect()->where('sales_order.customer_group_id = ?', $this->_membership_plan);
        return $this;
    }

    
    /** Custom Query using join clause
     * Get data of according to category : category name, product name, order date, invoice,
     * subtotal, total, invoiced and refunded
     * @return $this
     */
    protected function _initSelect()
    {

        $this->getSelect()->from(
            ['sales_order' => $this->getTable('sales_order')],
            ['increment_id','status','customer_firstname','customer_lastname','sales_order.base_grand_total as base_grand_total' ,'total_qty_ordered','shipping_amount','((sales_order.base_grand_total)-(sales_order.base_subtotal)) AS total_discount']
        );
        
        return $this;
    }

    /**
     * Apply filters common to reports
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        
        parent::_beforeLoad();
        $this->_applyAggregatedTable();
        $this->_applyDateRangeFilter();
        $this->_applyStoresFilter();
        $this->_applyCustomFilter();
        
        //echo "<pre>"; print_r($this->getSelect()->__toString()); exit;
        return $this;
    }
}
