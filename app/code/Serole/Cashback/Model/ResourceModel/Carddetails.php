<?php
namespace Serole\Cashback\Model\ResourceModel;

class Carddetails extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_card_detail', 'id');
    }
}
?>