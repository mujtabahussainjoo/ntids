<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model;

use Magento\Framework\Model\AbstractModel;

class LogDetails extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Amasty\AdminActionsLog\Model\ResourceModel\LogDetails');
    }

    /**
     * @param \Amasty\AdminActionsLog\Model\Log $logModel
     */
    public function deleteUnnecessaryOrderData($logModel)
    {
        $where['log_id = ?'] = $logModel->getLogId();
        $this->getCollection()->getConnection()->delete($this->getCollection()->getMainTable(), $where);
    }
}
