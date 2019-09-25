<?php

namespace Serole\Cashback\Model\ResourceModel\Carddetails;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Cashback\Model\Carddetails', 'Serole\Cashback\Model\ResourceModel\Carddetails');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>