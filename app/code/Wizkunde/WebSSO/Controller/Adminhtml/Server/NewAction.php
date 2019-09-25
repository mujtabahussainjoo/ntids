<?php

namespace Wizkunde\WebSSO\Controller\Adminhtml\Server;

use Wizkunde\WebSSO\Controller\Adminhtml\Server;

class NewAction extends Server
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
