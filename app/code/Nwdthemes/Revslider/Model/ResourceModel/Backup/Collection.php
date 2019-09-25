<?php

namespace Nwdthemes\Revslider\Model\ResourceModel\Backup;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\Backup', 'Nwdthemes\Revslider\Model\ResourceModel\Backup');
    }

}