<?php

namespace Serole\Pdf\Model\ResourceModel\Pdf;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Serole\Pdf\Model\Pdf', 'Serole\Pdf\Model\ResourceModel\Pdf');
    }

}