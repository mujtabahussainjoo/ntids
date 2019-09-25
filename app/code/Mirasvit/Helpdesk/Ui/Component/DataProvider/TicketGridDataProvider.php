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



namespace Mirasvit\Helpdesk\Ui\Component\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Mirasvit\Helpdesk\Logger\FatalLogger;
use Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TicketGridDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    private $logger;
    private $ticketCollectionFactory;
    private $messageManager;
    private $collectionFactory;
    private $regularFilter;
    private $registry;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        FatalLogger $logger,
        CollectionFactory $ticketCollectionFactory,
        ManagerInterface $messageManager,
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\UiComponent\DataProvider\RegularFilter $regularFilter,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->logger = $logger;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->messageManager = $messageManager;
        $this->collectionFactory = $collectionFactory;
        $this->regularFilter = $regularFilter;
        $this->registry = $registry;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() == 'status_id') {
            $filter->setField('main_table.status_id');
        }
        if ($filter->getField() == 'code') {
            $filter->setField('main_table.code');
        }
        if ($filter->getField() == 'subject') {
            $filter->setField('main_table.subject');
        }
        if ($filter->getField() == 'created_at') {
            $filter->setField('main_table.created_at');
        }
        if ($filter->getField() == 'user_id') {
            $filter->setField('main_table.user_id');
        }
        if ($filter->getField() == 'id') { // ticket grid on customer edit page
            $filter->setField('main_table.customer_id');
        }

        parent::addFilter($filter);
    }

    /**
     * Returns Search result
     *
     * @return \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection
     */
    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        if (!$this->isSearchFulltext($searchCriteria)) {
            return $this->reporting->search($searchCriteria)->joinStatuses();
        }

        /** @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Grid\Collection $ticketCollection */
        $ticketCollection = $this->collectionFactory->getReport($this->getSearchCriteria()->getRequestName());
        $ticketCollection->joinFields();
        $this->search($ticketCollection, $this->getSearchCriteria());

        $select = clone $ticketCollection->getSelect();
        $select->limitPage($this->getSearchCriteria()->getCurrentPage(), $this->getSearchCriteria()->getPageSize());

        $ticketCollection->getSelect()->reset();
        $ticketCollection->getSelect()->setPart(\Zend_Db_Select::COLUMNS, [
            ['main_table', '*', null],
        ]);
        $ticketCollection->getSelect()->setPart(\Zend_Db_Select::FROM, [
            'main_table' => [
                'joinType'      => 'from',
                'schema'        => null,
                'tableName'     => new \Zend_Db_Expr('(' . $select->__toString() . ')'),
                'joinCondition' => null,
            ],
        ]);
        $ticketCollection->getSelect()->setPart(\Zend_Db_Select::ORDER, [
            ['search_prior', 'DESC'],
        ]);

        return $ticketCollection;
    }

    /**
     * This method logs ticket with unsupported content
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $result = parent::searchResultToOutput($searchResult);

        if (!isset($result['items'])) {
            return $result;
        }

        $items = [];
        foreach ($result['items'] as $item) {
            json_encode($item);
            if (json_last_error() == JSON_ERROR_UTF8) {
                $this->logger->addCritical('JSON_ERROR_UTF8', $item);
                // we use this because Magento does not allow to display results and error message in the same time
                $this->messageManager->addErrorMessage('Some tickets can not be parsed. Please check logs.');
            } else {
                $items[] = $item;
            }
        }
        $result['items'] = $items;

        return $result;
    }

    /**
     * @param \Magento\Framework\Api\Search\SearchCriteria $searchCriteria
     * @return bool
     */
    private function isSearchFulltext($searchCriteria)
    {
        $result = false;
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            /** @var \Magento\Framework\Api\Filter $filter */
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getConditionType() == 'fulltext') {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool $searchCriteria
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function search($collection, $searchCriteria)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
        foreach ($searchCriteria->getSortOrders() as $sortOrder) {
            if ($sortOrder->getField()) {
                $collection->setOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }
        /** @var \Magento\Framework\Api\Search\FilterGroup $filterGroup */
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            /** @var \Magento\Framework\Api\Filter $filter */
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getConditionType() == 'fulltext') {
                    $collection->addSearchAttributes($this->getSearchAttributes(), $filter->getValue());
                } else {
                    $this->regularFilter->apply($collection, $filter);
                }
            }
        }

        return $collection;
    }

    /**
     * @return array
     */
    public function getSearchAttributes()
    {
        $attributes = [
            'main_table.subject'         => [
                'priority'        => 100,
                'selectStatement' => 'main_table.subject',
            ],
            'main_table.ticket_id'       => [
                'priority'        => 0,
                'selectStatement' => 'main_table.ticket_id',
            ],
            'main_table.description'     => [
                'priority'        => 0,
                'selectStatement' => 'main_table.description',
            ],
            'main_table.code'            => [
                'priority'        => 10,
                'selectStatement' => 'main_table.code',
            ],
            'main_table.order_id'        => [
                'priority'        => 0,
                'selectStatement' => 'main_table.order_id',
            ],
            'main_table.last_reply_name' => [
                'priority'        => 0,
                'selectStatement' => 'main_table.last_reply_name',
            ],
            'main_table.search_index'    => [
                'priority'        => 0,
                'selectStatement' => 'main_table.search_index',
            ],
            'user_name'                  => [
                'priority'        => 0,
                'selectStatement' => new \Zend_Db_Expr('CONCAT(firstname, " ", lastname)'),
            ],
            'email.body'                 => [
                'priority'        => 0,
                'selectStatement' => 'email.body',
            ],
            'customer_email'             => [
                'priority'        => 0,
                'selectStatement' => 'customer_email',
            ],
            'department.name'            => [
                'priority'        => 0,
                'selectStatement' => 'department.name',
            ],
            'status.name'                => [
                'priority'        => 0,
                'selectStatement' => 'status.name',
            ],
            'priority.name'              => [
                'priority'        => 0,
                'selectStatement' => 'priority.name',
            ],
        ];

        uasort($attributes, function ($elFirst, $elSecond) {
            if ($elFirst['priority'] == $elSecond['priority']) {
                return 0;
            }

            return $elFirst['priority'] < $elSecond['priority'] ? 1 : -1;
        });

        return $attributes;
    }
}
