<?php
namespace Serole\Productattachment\Model\ResourceModel;

class Productattachment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('serole_productattachment', 'id');
    }
}
?>