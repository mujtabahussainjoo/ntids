<?php
namespace Serole\Cashback\Model;

class Usedpoints extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Cashback\Model\ResourceModel\Usedpoints');
    }
}
?>