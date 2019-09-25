<?php

namespace Wizkunde\WebSSO\Connection\SAML2\Model;

use Magento\Framework\Model\AbstractModel;

class Type extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wizkunde\WebSSO\Connection\SAML2\Model\ResourceModel\Type');
    }
}
