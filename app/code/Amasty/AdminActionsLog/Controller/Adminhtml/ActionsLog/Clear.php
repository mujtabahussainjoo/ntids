<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Controller\Adminhtml\ActionsLog;

class Clear extends \Magento\Backend\App\Action
{
    public function execute()
    {
        /**
         * @var \Amasty\AdminActionsLog\Model\Log $log
         */
        $log = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log');
        $log->clearLog(false);
        $this->_redirect('amaudit/actionslog/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_AdminActionsLog::actions_log');
    }
}
