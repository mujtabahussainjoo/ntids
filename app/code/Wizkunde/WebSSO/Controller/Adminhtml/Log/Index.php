<?php

namespace Wizkunde\WebSSO\Controller\Adminhtml\Log;

use Magento\Framework\Controller\ResultFactory;
use Wizkunde\WebSSO\Controller\Adminhtml\Log;

class Index extends Log
{
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Audit Log'));
        return $resultPage;
    }
}
