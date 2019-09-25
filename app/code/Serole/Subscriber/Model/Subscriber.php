<?php
namespace Serole\Subscriber\Model;

class Subscriber extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Subscriber\Model\ResourceModel\Subscriber');
    }
}
?>