<?php

namespace Serole\Productattachment\Model\ResourceModel\Productattachment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Productattachment\Model\Productattachment', 'Serole\Productattachment\Model\ResourceModel\Productattachment');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>