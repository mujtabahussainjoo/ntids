<?php

namespace Nwdthemes\Revslider\Model\ResourceModel\Slider;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\Slider', 'Nwdthemes\Revslider\Model\ResourceModel\Slider');
    }

}