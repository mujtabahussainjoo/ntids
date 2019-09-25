<?php
namespace Serole\Sage\Model\ResourceModel;

class Sageintegration extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sage_integration', 'id');
    }
}
?>