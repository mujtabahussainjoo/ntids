<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\AdminGws\Model\Collections;

class AddFilter
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
     */
    private $orderGridCollection;

    /**
     * Changed select for compatibility with AdminGws module
     *
     * @param \Magento\AdminGws\Model\Collections $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $result
     */
    public function beforeAddStoreFieldToFilter($subject, $result)
    {
        $this->orderGridCollection = $result;
    }

    /**
     * Changed select for compatibility with AdminGws module
     *
     * @param \Magento\AdminGws\Model\Collections $subject
     * @param string $result
     * @return string $result
     * @throws \Zend_Db_Select_Exception
     */
    public function afterAddStoreFieldToFilter($subject, $result)
    {
        $mainTableName = 'main_table';
        /** @var \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection */
        $wherePart = $this->orderGridCollection->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
        $fromPart = $this->orderGridCollection->getSelect()->getPart(\Magento\Framework\DB\Select::FROM);
        if (is_array($fromPart)) {
            $mainTableName = key($fromPart);
        }
        foreach ($wherePart as $key => $where) {
            if (strpos($where, "`store_id`") !== false && strpos($where, "`$mainTableName`.`store_id`") === false) {
                $wherePart[$key] = str_replace("`store_id`", "`$mainTableName`.`store_id`", $where);
            } elseif (strpos($where, "store_id ") !== false && strpos($where, "$mainTableName.store_id") === false) {
                $wherePart[$key] = str_replace("store_id", "$mainTableName.store_id", $where);
            }
        }
        $this->orderGridCollection->getSelect()->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);

        return $result;
    }
}
