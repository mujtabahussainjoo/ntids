<?php

namespace Serole\Handoversso\Model\ResourceModel\Token;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Handoversso\Model\Token', 'Serole\Handoversso\Model\ResourceModel\Token');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>