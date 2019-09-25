<?php

namespace Serole\Serialcode\Model\ResourceModel\OrderitemSerialcode;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Serole\Serialcode\Model\OrderitemSerialcode', 'Serole\Serialcode\Model\ResourceModel\OrderitemSerialcode');
    }

}