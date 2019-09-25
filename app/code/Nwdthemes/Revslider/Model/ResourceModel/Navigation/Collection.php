<?php

namespace Nwdthemes\Revslider\Model\ResourceModel\Navigation;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\Navigation', 'Nwdthemes\Revslider\Model\ResourceModel\Navigation');
    }

}