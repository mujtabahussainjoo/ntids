<?php

namespace Serole\Cashback\Model\ResourceModel\Customerorder;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Cashback\Model\Customerorder', 'Serole\Cashback\Model\ResourceModel\Customerorder');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>