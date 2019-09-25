<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 11:15 AM
 */
namespace PL\Competition\Model\Resource\Competition;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * _contruct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('PL\Competition\Model\Competition', 'PL\Competition\Model\Resource\Competition');
        $this->_map['fields']['competition_id'] = 'main_table.competition_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    
}