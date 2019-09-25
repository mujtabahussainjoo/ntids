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



namespace Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection as TicketCollection;

/**
 * Collection for displaying grid of cms blocks.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends TicketCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @param \Mirasvit\Helpdesk\Model\SearchFactory                          $searchFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory
     * @param \Mirasvit\Helpdesk\Helper\Field                                 $helpdeskField
     * @param \Mirasvit\Helpdesk\Helper\Permission                            $helpdeskPermission
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface       $entityFactory
     * @param \Psr\Log\LoggerInterface                                        $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface    $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                       $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb            $mainTable
     * @param string                                                          $eventPrefix
     * @param object                                                          $eventObject
     * @param object                                                          $resourceModel
     * @param string                                                          $model
     * @param \Magento\Framework\DB\Adapter\AdapterInterface                  $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb            $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\SearchFactory $searchFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Mirasvit\Helpdesk\Helper\Field $helpdeskField,
        \Mirasvit\Helpdesk\Helper\Permission $helpdeskPermission,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->helpdeskPermission = $helpdeskPermission;
        parent::__construct(
            $searchFactory,
            $ticketCollectionFactory,
            $helpdeskField,
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     *
     * @return void
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|bool
     */
    public function getSearchCriteria()
    {
        return false;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[]|array $items
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * @param int $ticketId
     * @return int|bool
     */
    public function getNextTicket($ticketId)
    {
        $collection = $this->applyDefaultOrder();
        $tickets = $collection->getColumnValues('ticket_id');

        $next = false;
        $hit = 0;
        foreach ($tickets as $id) {
            if ($hit) {
                $next = $id;
                break;
            }
            if ($ticketId == $id) {
                $hit = 1;
            }
        }

        return $next;
    }

    /**
     * @param int $ticketId
     * @return int|bool
     */
    public function getPrevTicket($ticketId)
    {
        $collection = $this->applyDefaultOrder();
        $tickets = $collection->getColumnValues('ticket_id');

        $prev = false;
        foreach ($tickets as $id) {
            if ($ticketId == $id) {
                break;
            }
            $prev = $id;
        }

        return $prev;
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        if ($permission = $this->helpdeskPermission->getPermission()) {
            $departmentIds = $permission->getDepartmentIds();
            if (!in_array(0, $departmentIds)) {
                $select = $this->getSelect();
                $select->where('main_table.department_id in (' . implode(',', $departmentIds) . ')');
            }
        }
    }
}
