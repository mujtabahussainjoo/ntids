<?php

namespace Serole\ExternalProduct\Model\ResourceModel\Referral;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\ExternalProduct\Model\Referral', 'Serole\ExternalProduct\Model\ResourceModel\Referral');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>