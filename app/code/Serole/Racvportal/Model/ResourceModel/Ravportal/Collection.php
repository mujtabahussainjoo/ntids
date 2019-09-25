<?php

namespace Serole\Racvportal\Model\ResourceModel\Ravportal;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Racvportal\Model\Ravportal', 'Serole\Racvportal\Model\ResourceModel\Ravportal');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>