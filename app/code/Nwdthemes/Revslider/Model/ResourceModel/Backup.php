<?php

namespace Nwdthemes\Revslider\Model\ResourceModel;

class Backup extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('nwdthemes_revslider_backup','id');
    }

}