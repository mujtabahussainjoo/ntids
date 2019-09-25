<?php

namespace Serole\MemberList\Model\ResourceModel\Memberlist;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\MemberList\Model\Memberlist', 'Serole\MemberList\Model\ResourceModel\Memberlist');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>