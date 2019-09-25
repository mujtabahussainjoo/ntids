<?php
namespace Serole\Cashback\Model\ResourceModel;

class Customerorder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_order_detail', 'id');
    }
}
?>