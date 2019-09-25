<?php

namespace Nwdthemes\Revslider\Model\ResourceModel\Css;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\Css', 'Nwdthemes\Revslider\Model\ResourceModel\Css');
    }

}