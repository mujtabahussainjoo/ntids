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



namespace Mirasvit\ReportApi\Processor;

use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Mirasvit\ReportApi\Api\RequestInterface;

class Request extends AbstractSimpleObject implements RequestInterface
{
    const TABLE        = 'table';
    const COLUMNS      = 'columns';
    const FILTERS      = 'filters';
    const DIMENSIONS   = 'dimensions';
    const SORT_ORDERS  = 'sort_orders';
    const PAGE_SIZE    = 'page_size';
    const CURRENT_PAGE = 'current_page';
    const QUERY        = 'query';

    private $serviceOutputProcessor;

    private $requestProcessor;

    public function __construct(
        RequestProcessor $requestProcessor,
        ServiceOutputProcessor $serviceOutputProcessor,
        array $data = []
    ) {
        $this->requestProcessor       = $requestProcessor;
        $this->serviceOutputProcessor = $serviceOutputProcessor;

        foreach ([self::COLUMNS, self::FILTERS, self::SORT_ORDERS] as $key) {
            $data[$key] = isset($data[$key]) ? $data[$key] : [];
        }

        parent::__construct($data);
    }

    public function setTable($table)
    {
        return $this->setData(self::TABLE, $table);
    }

    public function setColumns(array $columns)
    {
        foreach ($columns as $idx => $column) {
            $columns[$idx] = $this->checkColumn($column);
        }

        return $this->setData(self::COLUMNS, $columns);
    }

    private function checkColumn($column)
    {
        if (count(explode('|', $column)) == 1 && $this->getTable() && $column != 'pk') {
            $column = $this->getTable() . '|' . $column;
        }

        return $column;
    }

    public function getTable()
    {
        return $this->_get(self::TABLE);
    }

    public function getColumns()
    {
        return $this->_get(self::COLUMNS);
    }

    public function addColumn($column)
    {
        return $this->addData(self::COLUMNS, [$column]);
    }

    private function addData($key, $data)
    {
        return $this->setData($key, array_unique(array_merge_recursive(
            $this->_get($key),
            $data
        )));
    }

    public function setFilters(array $filters)
    {
        foreach ($filters as $idx => $filter) {
            $filter->setColumn($this->checkColumn($filter->getColumn()));
        }

        return $this->setData(self::FILTERS, $filters);
    }

    public function getFilters()
    {
        return $this->_get(self::FILTERS);
    }

    public function addFilter($column, $value, $condition = 'eq', $group = '')
    {
        return $this->addData(self::FILTERS, [new RequestFilter([
            RequestFilter::COLUMN         => $this->checkColumn($column),
            RequestFilter::VALUE          => $value,
            RequestFilter::CONDITION_TYPE => $condition,
            RequestFilter::GROUP          => $group,
        ])]);
    }

    public function setDimensions($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        foreach ($columns as $idx => $column) {
            $columns[$idx] = $this->checkColumn($column);
        }

        return $this->setData(self::DIMENSIONS, $columns);
    }

    public function getDimensions()
    {
        return $this->_get(self::DIMENSIONS) ? $this->_get(self::DIMENSIONS) : [];
    }

    public function setSortOrders(array $sortOrders)
    {
        return $this->setData(self::SORT_ORDERS, $sortOrders);
    }

    public function getSortOrders()
    {
        return $this->_get(self::SORT_ORDERS) ? $this->_get(self::SORT_ORDERS) : [];
    }

    public function addSortOrder($column, $direction)
    {
        return $this->addData(self::SORT_ORDERS, [new RequestSortOrder([
            RequestSortOrder::COLUMN    => $this->checkColumn($column),
            RequestSortOrder::DIRECTION => $direction,
        ])]);
    }

    public function setPageSize($size)
    {
        return $this->setData(self::PAGE_SIZE, $size);
    }

    public function getPageSize()
    {
        return $this->_get(self::PAGE_SIZE) ? $this->_get(self::PAGE_SIZE) : 10000000000;
    }

    public function setCurrentPage($page)
    {
        return $this->setData(self::CURRENT_PAGE, $page);
    }

    public function getCurrentPage()
    {
        return $this->_get(self::CURRENT_PAGE) ? $this->_get(self::CURRENT_PAGE) : 1;
    }

    public function setQuery($query)
    {
        return $this->setData(self::QUERY, $query);
    }

    public function getQuery()
    {
        return $this->_get(self::QUERY);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return print_r($this->toArray(), true);
    }

    public function toArray()
    {
        return $this->serviceOutputProcessor->convertValue($this, RequestInterface::class);
    }

    public function process()
    {
        return $this->requestProcessor->process($this);
    }
}
