<?php

namespace Nwdthemes\Revslider\Model\ResourceModel\Animation;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\Animation', 'Nwdthemes\Revslider\Model\ResourceModel\Animation');
    }

}