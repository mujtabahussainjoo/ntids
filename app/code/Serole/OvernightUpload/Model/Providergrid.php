<?php

namespace Serole\OvernightUpload\Model;

//use Serole\OvernightUpload\Api\Data\GridInterface;

class Providergrid extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init('Serole\OvernightUpload\Model\ResourceModel\Providergrid');
    }

}
