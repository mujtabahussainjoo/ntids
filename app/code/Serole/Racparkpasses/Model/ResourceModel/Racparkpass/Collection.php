<?php

namespace Serole\Racparkpasses\Model\ResourceModel\Racparkpass;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Racparkpasses\Model\Racparkpass', 'Serole\Racparkpasses\Model\ResourceModel\Racparkpass');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>