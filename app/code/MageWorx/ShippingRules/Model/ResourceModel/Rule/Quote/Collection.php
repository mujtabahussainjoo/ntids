<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ResourceModel\Rule\Quote;

class Collection extends \MageWorx\ShippingRules\Model\ResourceModel\Rule\Collection
{
    /**
     * Add stores for load
     *
     * @return $this
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addStoresToResult();
        return $this;
    }

    /**
     * Init flag for adding rule store ids to collection result
     *
     * @param bool|null $flag
     * @return $this
     */
    public function addStoresToResult($flag = null)
    {
        $flag = $flag === null ? true : $flag;
        $this->setFlag('add_stores_to_result', $flag);
        return $this;
    }

    /**
     * Add store ids to rules data
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->getFlag('add_stores_to_result') && $this->_items) {
            /** @var \Magento\Rule\Model\AbstractModel $item */
            foreach ($this->_items as $item) {
                // @TODO Check this strange afterLoad later, may be we should remove it
                $item->afterLoad();
            }
        }

        return $this;
    }

    /**
     * Provide support for store id filter
     *
     * @param string $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_ids') {
            return $this->addStoreFilter($condition);
        }

        parent::addFieldToFilter($field, $condition);
        return $this;
    }
}
