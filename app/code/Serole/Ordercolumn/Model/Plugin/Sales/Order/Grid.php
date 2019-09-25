<?php
namespace Serole\Ordercolumn\Model\Plugin\Sales\Order;
 
class Grid
{
 
    public static $table = 'sales_order_grid';
    public static $leftJoinTable = 'racvportal';
 
    public function afterSearch($intercepter, $collection)
    {
        if ($collection->getMainTable() === $collection->getConnection()->getTableName(self::$table)) {
 
            $leftJoinTableName = $collection->getConnection()->getTableName(self::$leftJoinTable);
 
            $collection
                ->getSelect()
                ->joinLeft(
                    ['co'=>$leftJoinTableName],
                    "co.incrementid = main_table.increment_id",
                    [
                        'shop_name' => 'co.shop_name'
                    ]
                );
 
            $where = $collection->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
 
            $collection->getSelect()->setPart(\Magento\Framework\DB\Select::WHERE, $where);
 
            //echo $collection->getSelect()->__toString();die; 
 
 
        }
        return $collection;
 
 
    }
 
 
}