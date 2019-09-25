<?php
namespace Serole\Subscriber\Model\ResourceModel;

class Subscriber extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('subscriber', 'id');
    }
}
?>