<?php

namespace Serole\GiftMessage\Model\ResourceModel\Message;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Serole\GiftMessage\Model\Message', 'Serole\GiftMessage\Model\ResourceModel\Message');
    }

}