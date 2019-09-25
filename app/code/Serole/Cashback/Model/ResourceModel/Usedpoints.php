<?php
namespace Serole\Cashback\Model\ResourceModel;

class Usedpoints extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_used_points', 'id');
    }
}
?>