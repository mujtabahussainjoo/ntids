<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Logger;

class handleBackendAuthUserLoginSuccess implements ObserverInterface
{
    protected $_objectManager;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $loginAttemptsModel \Amasty\AdminActionsLog\Model\LoginAttempts $logModel */
        $loginAttemptsModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\LoginAttempts');
        $userData = $loginAttemptsModel->prepareUserLoginData($observer, \Amasty\AdminActionsLog\Model\LoginAttempts::SUCCESS);
        $loginAttemptsModel->setData($userData);
        $loginAttemptsModel->save();

        /**
         * @var \Amasty\AdminActionsLog\Model\ActiveSessions $activeModel
         */
        $activeModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\ActiveSessions');
        $activeModel->saveActive($userData);

        /**
         * @var \Amasty\AdminActionsLog\Model\Mailsender $mailsendModel
         */
        $mailsendModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Mailsender');

        $successfulMail = $this->_scopeConfig->getValue('amaudit/successful_log_mailing/send_to_mail');
        if (
            ($this->_scopeConfig->getValue('amaudit/successful_log_mailing/enabled') != 0)
            && !empty($successfulMail)
        ) {
            $mailsendModel->sendMail($userData, 'success', $successfulMail);
        }

        $suspiciousMail = $this->_scopeConfig->getValue('amaudit/suspicious_log_mailing/send_to_mail');
        if ((($this->_scopeConfig->getValue('amaudit/suspicious_log_mailing/enabled') != 0) &&
            !empty($suspiciousMail) && $this->_scopeConfig->getValue('amaudit/geolocation/geolocation_enable'))
        ) {
            $isSuspicious = $loginAttemptsModel->isSuspicious($userData);
            if ($isSuspicious){
                $mailsendModel->sendMail($userData, 'suspicious', $suspiciousMail);
            }
        }

        if ($this->_scopeConfig->getValue('amaudit/log/log_enable_visit_history') && !empty($userData['username'])) {
            /**
             * @var \Amasty\AdminActionsLog\Model\VisitHistory $visitModel
             */
            $visitModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\VisitHistory');
            $visitModel->startVisit($userData);
        }
    }
}
