<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Model\ResourceModel\Rule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @method \Amasty\Rolepermissions\Model\ResourceModel\Rule getResource()
 * @method \Amasty\Rolepermissions\Model\Rule[] getItems()
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rolepermissions\Model\Rule', 'Amasty\Rolepermissions\Model\ResourceModel\Rule');
    }

    /**
     * @param array|int $categoryIds
     *
     * @return $this
     */
    public function addCategoriesFilter($categoryIds)
    {
        $this->getResource()->addRelationFilter($this->getSelect(), $categoryIds, 'categories');

        return $this;
    }
}
