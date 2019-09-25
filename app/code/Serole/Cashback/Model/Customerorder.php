<?php
namespace Serole\Cashback\Model;

class Customerorder extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Cashback\Model\ResourceModel\Customerorder');
    }
}
?>