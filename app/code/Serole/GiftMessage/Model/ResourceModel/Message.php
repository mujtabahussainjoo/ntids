<?php

namespace Serole\GiftMessage\Model\ResourceModel;

class Message extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('giftmessage','id');
    }

}