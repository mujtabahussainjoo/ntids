<?php

namespace Serole\Carreport\Model;

class Carreport extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init('Serole\Carreport\Model\ResourceModel\Carreport');
    }

}