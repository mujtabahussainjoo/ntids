<?php

namespace Serole\Carreport\Model\ResourceModel\Carreport;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    protected function _construct() {
        $this->_init('Serole\Carreport\Model\Carreport', 'Serole\Carreport\Model\ResourceModel\Carreport');
    }

}