<?php
namespace Serole\ExternalProduct\Model\ResourceModel;

class Referral extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('referral_link_history', 'id');
    }
}
?>