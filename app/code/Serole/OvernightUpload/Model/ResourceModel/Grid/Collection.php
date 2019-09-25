<?php


namespace Serole\OvernightUpload\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'entity_id';


    protected function _construct(){
        $this->_init(
            'Serole\OvernightUpload\Model\Grid',
            'Serole\OvernightUpload\Model\ResourceModel\Grid'
        );
    }
}
