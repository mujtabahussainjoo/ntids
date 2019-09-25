<?php

namespace Nwdthemes\Revslider\Model\ResourceModel;

class Animation extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('nwdthemes_revslider_animations','id');
    }

}