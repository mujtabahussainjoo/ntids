<?php

namespace Serole\Serialcode\Model\ResourceModel;

class OrderitemSerialcode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('order_item_serialcode','id');
    }

}