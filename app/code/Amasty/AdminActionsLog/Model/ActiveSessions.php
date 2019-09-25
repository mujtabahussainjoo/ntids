<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model;

use Magento\Framework\Model\AbstractModel;

class ActiveSessions extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Amasty\AdminActionsLog\Model\ResourceModel\ActiveSessions');
    }

    protected $_objectManager;
    protected $_scopeConfig;
    protected $_date;
    protected $_helper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Amasty\AdminActionsLog\Helper\Data $helper
    )
    {
        parent::__construct($context, $coreRegistry);
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_date = $date;
        $this->_helper = $helper;
    }

    public function updateOnlineAdminActivity($sessionId)
    {
        $time = $this->_date->timestamp();
        $timeStamp = date('Y-m-d H:i:s', $time);
        $activeEntity = $this->_getActiveEntity($sessionId);
        $activeEntityData = $activeEntity->getData();
        if (!empty($activeEntityData)) {
            $activeEntity->setData('recent_activity', $timeStamp);
            $activeEntity->save();
        }
    }

    public function saveActive($data)
    {
        $activeData = array(
            'session_id' => $this->_helper->getSessionId(),
            'recent_activity' => $data['date_time'],
        );

        $allData = array_merge($data, $activeData);

        $this->setData($allData);
        $this->save();
    }

    public function removeOnlineAdmin($sessionId, $observer = NULL)
    {
        /**
         * @var \Amasty\AdminActionsLog\Model\ActiveSessions $activeEntity
         */
        $activeEntity = $this->_getActiveEntity($sessionId);
        $activeEntity->delete();

        /**
         * @var \Amasty\AdminActionsLog\Model\VisitHistory $visitHistoryModel
         */
        $visitHistoryModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\VisitHistory');
        $visitHistoryModel->endVisit($sessionId, $observer);
    }

    public function destroySession($sessionId)
    {
        $this->_helper->setSessionId($sessionId);
        $this->_helper->sessionDestroy(['clear_storage' => false, 'send_expire_cookie' => false]);
    }

    public function checkOnline()
    {
        $sessionLifeTime =  $this->_scopeConfig->getValue('admin/security/session_lifetime');
        if (empty($sessionLifeTime)) {
            $sessionLifeTime = 900;
        }
        $currentTime = $this->_date->timestamp();
        foreach ($this->getCollection() as $entity) {
            $rowTime = strtotime($entity->getRecentActivity());
            $timeDifference = $currentTime - $rowTime;
            if ($timeDifference >= $sessionLifeTime) {
                $sessionId = $entity->getSessionId();
                $this->removeOnlineAdmin($sessionId);
            }
        }
    }

    protected function _getActiveEntity($sessionId)
    {
        /**
         * @var \Amasty\AdminActionsLog\Model\ActiveSessions $activeModel
         */
        $activeModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\ActiveSessions');
        $activeCollection = $activeModel->getCollection();
        $activeCollection->addFieldToFilter('session_id', $sessionId);
        $activeEntity = $activeCollection->getFirstItem();

        return $activeEntity;
    }
}
