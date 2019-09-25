<?php

namespace Serole\Pdf\Model;

class Pdf extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init('Serole\Pdf\Model\ResourceModel\Pdf');
    }

}