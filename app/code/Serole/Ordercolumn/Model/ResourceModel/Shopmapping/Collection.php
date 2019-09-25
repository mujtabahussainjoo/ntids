<?php

namespace Serole\Ordercolumn\Model\ResourceModel\Shopmapping;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Ordercolumn\Model\Shopmapping', 'Serole\Ordercolumn\Model\ResourceModel\Shopmapping');
        //$this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>