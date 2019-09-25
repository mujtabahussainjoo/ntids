<?php

namespace Serole\Sage\Model\ResourceModel\Sageintegration;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Sage\Model\Sageintegration', 'Serole\Sage\Model\ResourceModel\Sageintegration');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>