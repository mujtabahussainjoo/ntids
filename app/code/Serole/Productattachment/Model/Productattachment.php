<?php
namespace Serole\Productattachment\Model;

class Productattachment extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Productattachment\Model\ResourceModel\Productattachment');
    }
}
?>