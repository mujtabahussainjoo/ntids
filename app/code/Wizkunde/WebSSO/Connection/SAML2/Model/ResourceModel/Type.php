<?php

namespace Wizkunde\WebSSO\Connection\SAML2\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Type extends AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wizkunde_websso_server_saml2', 'id');
    }
}
