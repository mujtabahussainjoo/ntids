<?php

namespace Wizkunde\WebSSO\Model;

use Magento\Framework\Model\AbstractModel;

class Server extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wizkunde\WebSSO\Model\ResourceModel\Server');
    }
}
