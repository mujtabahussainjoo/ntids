<?php

namespace Serole\Subscriber\Model\ResourceModel\Subscriber;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Subscriber\Model\Subscriber', 'Serole\Subscriber\Model\ResourceModel\Subscriber');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>