<?php
namespace Serole\Ordercolumn\Model\ResourceModel;

class Shopmapping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('racvportal', 'Id');
    }
}
?>