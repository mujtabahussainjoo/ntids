<?php
namespace Serole\Smsauth\Model;

class Smsauth extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Smsauth\Model\ResourceModel\Smsauth');
    }
}
?>