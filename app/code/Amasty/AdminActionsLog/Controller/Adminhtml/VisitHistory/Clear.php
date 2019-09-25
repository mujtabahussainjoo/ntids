<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Controller\Adminhtml\VisitHistory;

class Clear extends \Magento\Backend\App\Action
{
    public function execute()
    {
        /**
         * @var \Amasty\AdminActionsLog\Model\VisitHistory $log
         */
        $log = $this->_objectManager->get('Amasty\AdminActionsLog\Model\VisitHistory');
        $log->clearLog(false);
        $this->_redirect('amaudit/visithistory/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_AdminActionsLog::page_visit_history');
    }
}
