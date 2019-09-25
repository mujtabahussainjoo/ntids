<?php
namespace Serole\Racparkpasses\Model\ResourceModel;

class Racparkpass extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_order_item', 'item_id');
    }
}
?>