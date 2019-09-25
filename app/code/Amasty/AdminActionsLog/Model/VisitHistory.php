<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model;

use Magento\Framework\Model\AbstractModel;

class VisitHistory extends AbstractModel
{
    protected $_scopeConfig;
    protected $_helper;

    /**
     * @var LoginAttempts
     */
    private $loginAttempts;

    /**
     * @var VisitHistoryDetails
     */
    private $visitHistoryDetails;

    protected function _construct()
    {
        $this->_init('Amasty\AdminActionsLog\Model\ResourceModel\VisitHistory');
    }

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\AdminActionsLog\Helper\Data $helper,
        \Amasty\AdminActionsLog\Model\LoginAttempts $loginAttempts,
        \Amasty\AdminActionsLog\Model\VisitHistoryDetails $visitHistoryDetails
    ) {
        parent::__construct($context, $coreRegistry);
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->loginAttempts = $loginAttempts;
        $this->visitHistoryDetails = $visitHistoryDetails;
    }

    public function startVisit($userData)
    {
        $userData['session_start'] = time();
        $userData['session_id'] = $this->_helper->getSessionId();
        $this->setData($userData);
        $this->save();
    }

    public function getVisitEntity($sessionId)
    {
        $activeModel = $this->getCollection()
            ->addFieldToFilter('session_id', $sessionId);
        $activeEntity = $activeModel->getFirstItem();
        return $activeEntity;
    }

    public function endVisit($sessionId, $observer = NULL)
    {
        if ($observer) {
            $visitEntity = $this->load($sessionId, 'session_id');
            if (!$visitEntity->getId()) {
                $userData = $this->loginAttempts->prepareUserLoginData(
                    $observer,
                    \Amasty\AdminActionsLog\Model\LoginAttempts::LOGOUT
                );
                $visitEntity->setData($userData);
            }
            $visitEntity->addData(['session_end' => time()]);
            $visitEntity->save();
        }

        $this->visitHistoryDetails->saveLastPageDuration($this->_helper->getSessionId());
    }

    public function clearLog($fromObserver = true)
    {
        $logCollection = $this->getCollection();

        $where = [];

        if ($fromObserver) {
            $days = $this->_scopeConfig->getValue('amaudit/log/log_delete_pages_history_after_days');
            $where['session_start < NOW() - INTERVAL ? DAY'] = $days;
        }

        $logCollection->getConnection()->delete($logCollection->getMainTable(), $where);
    }
}
