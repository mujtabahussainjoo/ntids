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



namespace Mirasvit\Helpdesk\Test\Unit\Mocks\Lib;

trait CollectionTrait
{
    protected $items = [];

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @param string $attribute
     * @param null   $condition
     *
     * @return $this
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        $this->items = array_filter($this->items, function ($item) use ($attribute, $condition) {
            return  $item->getData($attribute) == $condition;
        });

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->items = array_filter($this->items, function ($item) use ($storeId) {
            return in_array($storeId, $item->getData('store_ids'));
        });

        return $this;
    }

    /**
     * @param int    $field
     * @param string $direction
     *
     * @return $this
     */
    public function setOrder($field, $direction = \Magento\Framework\Data\Collection::SORT_ORDER_DESC)
    {
        $direction = strtoupper($direction);
        usort($this->items, function ($itemA, $itemB) use ($field, $direction) {
            if ($direction == \Magento\Framework\Data\Collection::SORT_ORDER_DESC) {
                return $itemA->getData($field) < $itemB->getData($field) ? 1 : -1;
            } else {
                return $itemA->getData($field) > $itemB->getData($field) ? 1 : -1;
            }
        });

        return $this;
    }

    /**
     * @param bool|false $printQuery
     * @param bool|false $logQuery
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return count($this->items);
    }
}
