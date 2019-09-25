<?php

namespace Nwdthemes\Revslider\Model\ResourceModel\Slide;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\Slide', 'Nwdthemes\Revslider\Model\ResourceModel\Slide');
    }

}