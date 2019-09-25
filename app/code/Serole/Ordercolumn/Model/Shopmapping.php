<?php
namespace Serole\Ordercolumn\Model;

class Shopmapping extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Ordercolumn\Model\ResourceModel\Shopmapping');
    }
}
?>