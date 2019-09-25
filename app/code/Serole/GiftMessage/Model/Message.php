<?php

namespace Serole\GiftMessage\Model;

class Message extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init('Serole\GiftMessage\Model\ResourceModel\Message');
    }

}