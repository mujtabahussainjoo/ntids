<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Controller\Adminhtml\ActionsLog;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportActionsLogCsv extends \Magento\Reports\Controller\Adminhtml\Report\Review
{
    public function execute()
    {
        $fileName = 'admin_actions_log.csv';
        $content = $this->_view->getLayout()->createBlock(
            'Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Export\Grid'
        )->getCsv();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_AdminActionsLog::actions_log');
    }
}
