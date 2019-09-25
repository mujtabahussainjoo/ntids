<?php

namespace Nwdthemes\Revslider\Model\ResourceModel;

class StaticSlide extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('nwdthemes_revslider_static_slides','id');
    }

}