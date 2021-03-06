<?php
namespace Wizkunde\WebSSO\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Server extends AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wizkunde_websso_server', 'id');
    }
}
