<?php

namespace Wizkunde\WebSSO\Controller\Adminhtml\Server;

use Magento\Framework\Controller\ResultFactory;
use Wizkunde\WebSSO\Controller\Adminhtml\Server;

class Index extends Server
{
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Servers'));
        return $resultPage;
    }
}
