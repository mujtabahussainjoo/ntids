<?php


namespace Serole\OvernightUpload\Model\ResourceModel\Providergrid;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id';


    protected function _construct(){
        $this->_init(
            'Serole\OvernightUpload\Model\Providergrid',
            'Serole\OvernightUpload\Model\ResourceModel\Providergrid'
        );
    }
}
