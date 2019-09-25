<?php

namespace Serole\Pdf\Model\ResourceModel;

class Pdf extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('order_pdf_status','id');
    }

}