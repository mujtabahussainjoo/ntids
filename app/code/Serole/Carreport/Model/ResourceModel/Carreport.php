<?php

namespace Serole\Carreport\Model\ResourceModel;

class Carreport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('car_report_status','id');
    }

}