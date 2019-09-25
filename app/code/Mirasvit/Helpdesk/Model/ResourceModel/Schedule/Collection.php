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



namespace Mirasvit\Helpdesk\Model\ResourceModel\Schedule;

/**
 *
 *
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'schedule_id';//@codingStandardsIgnoreLine

    /**
     * @var \Magento\Framework\Data\Collection\EntityFactoryInterface
     */
    protected $entityFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategyInterface
     */
    protected $fetchStrategy;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var object
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected $resource;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface               $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $resource
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->entityFactory = $entityFactory;
        $this->logger = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->connection = $connection;
        $this->resource = $resource;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Helpdesk\Model\Schedule', 'Mirasvit\Helpdesk\Model\ResourceModel\Schedule');
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('mst_helpdesk_schedule_store')}`
                AS `schedule_store_table`
                WHERE main_table.schedule_id = schedule_store_table.whs_schedule_id
                AND schedule_store_table.whs_store_id in (?))", [0, $storeId]);

        return $this;
    }

    /**
     * @return $this
     */
    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('is_active', true);

        return $this;
    }

    /**
     * Filter by is_active, active_to
     *
     * @return $this
     */
    public function addAvailableScheduleFilter()
    {
        $this->addIsActiveFilter();
        $now = (new \DateTime('now', new \DateTimeZone('UTC')))
            ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $this->getSelect()
            ->where("('$now' <= main_table.active_to OR isnull(main_table.active_to))");

        return $this;
    }

    /**
     * Filter by is_active, active_from, active_to
     *
     * @return $this
     */
    public function addCurrentFilter()
    {
        $this->addIsActiveFilter();
        $now = (new \DateTime('now', new \DateTimeZone('UTC')))
            ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $this->getSelect()
            ->where(
                "(main_table.active_from <= '$now' OR isnull(main_table.active_from)) AND " .
                " ('$now' <= main_table.active_to OR isnull(main_table.active_to))");

        return $this;
    }

    /**
     * Filter by is_active, active_from, active_to
     *
     * @param int $includeNearestDays
     *
     * @return $this
     */
    public function addCurrentScheduleFilter($includeNearestDays = 0)
    {
        $this->addIsActiveFilter();

        $date = (new \DateTime('now', new \DateTimeZone('UTC')));
        $end = $date->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $start = $date->add(new \DateInterval('P'.$includeNearestDays.'D'))
            ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $this->getSelect()
            ->where(
                "(main_table.active_from <= '$start' OR isnull(main_table.active_from)) AND " .
                "('$end' <= main_table.active_to OR isnull(main_table.active_to))");

        return $this;
    }

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     *
     */
    public function _afterLoad()
    {
        if ($this->storeId) {
            foreach ($this as $item) {
                $item->setStoreId($this->storeId);
            }
        }
    }

     /************************/
}
