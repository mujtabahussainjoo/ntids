<?php

namespace Serole\Smsauth\Model\ResourceModel\Smsauth;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Smsauth\Model\Smsauth', 'Serole\Smsauth\Model\ResourceModel\Smsauth');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>