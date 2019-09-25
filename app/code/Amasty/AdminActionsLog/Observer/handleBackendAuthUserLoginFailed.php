<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Logger;

class handleBackendAuthUserLoginFailed implements ObserverInterface
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
        $loginAttemptsModel = $this->_objectManager->create('Amasty\AdminActionsLog\Model\LoginAttempts');
        $userData = $loginAttemptsModel->prepareUserLoginData($observer, \Amasty\AdminActionsLog\Model\LoginAttempts::UNSUCCESS);
        $loginAttemptsModel->setData($userData);
        $loginAttemptsModel->save();

        $receiveUnsuccessfulEmail = $this->_scopeConfig->getValue('amaudit/unsuccessful_log_mailing/send_to_mail');
        if (
            ($this->_scopeConfig->getValue('amaudit/unsuccessful_log_mailing/enabled') != 0)
            && !empty($receiveUnsuccessfulEmail)
        ) {
            $unsuccessfulCount = $loginAttemptsModel->getUnsuccessfulCount() + 1;//"+1" Because saving failed login is later
            if ($unsuccessfulCount >= $loginAttemptsModel::MIN_UNSUCCESSFUL_COUNT) {
                $userData['unsuccessful_login_count'] = $unsuccessfulCount;
                /**
                 * @var \Amasty\AdminActionsLog\Model\Mailsender $mailsendModel
                 */
                $mailsendModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Mailsender');
                $userData['unsuccessful_login_count'] = $unsuccessfulCount;
                $mailsendModel->sendMail($userData, 'unsuccessful', $receiveUnsuccessfulEmail);
            }
        }
    }
}
