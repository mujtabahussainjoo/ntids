<?php
namespace Serole\Digitalglue\Model\ResourceModel;

class Digitalglue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digitalglue', 'id');
    }
}
?>