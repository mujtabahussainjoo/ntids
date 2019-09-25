<?php
namespace Serole\Smsauth\Model\ResourceModel;

class Smsauth extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('smsauth', 'id');
    }
}
?>