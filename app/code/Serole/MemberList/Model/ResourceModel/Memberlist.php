<?php
namespace Serole\MemberList\Model\ResourceModel;

class Memberlist extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_memberlist_detail', 'id');
    }
}
?>