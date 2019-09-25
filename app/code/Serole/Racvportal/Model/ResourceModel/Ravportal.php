<?php
namespace Serole\Racvportal\Model\ResourceModel;

class Ravportal extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('portal_shop', 'entity_id');
    }
}
?>