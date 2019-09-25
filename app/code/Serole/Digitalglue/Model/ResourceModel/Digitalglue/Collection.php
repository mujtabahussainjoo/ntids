<?php

namespace Serole\Digitalglue\Model\ResourceModel\Digitalglue;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Digitalglue\Model\Digitalglue', 'Serole\Digitalglue\Model\ResourceModel\Digitalglue');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>