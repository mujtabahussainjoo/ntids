<?php

namespace Nwdthemes\Revslider\Model\ResourceModel\Option;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\Option', 'Nwdthemes\Revslider\Model\ResourceModel\Option');
    }

}