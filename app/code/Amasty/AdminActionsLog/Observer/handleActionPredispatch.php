<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Magento\Framework\Event\ObserverInterface;


class handleActionPredispatch implements ObserverInterface
{
    protected $_registryManager;
    protected $_objectManager;
    protected $_authSession;
    protected $_appState;
    protected $_scopeConfig;
    protected $_helper;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Amasty\AdminActionsLog\Helper\Data $helper
    )
    {
        $this->_registryManager = $coreRegistry;
        $this->_objectManager = $objectManager;
        $this->_authSession = $authSession;
        $this->_appState = $appState;
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            /**
             * @var \Magento\Framework\App\Request\Http $request
             */
            $request = $observer->getRequest();
            $action = $request->getActionName();
            if ($this->_needToRegister($action)) {
                $this->_registryManager->register('amaudit_action', $action, true);
                $category = $request->getModuleName() . '/' . $request->getControllerName();
                $this->_registryManager->register('amaudit_category', $category, true);
            }

            $this->_saveCache($request);
            $this->_saveExport($request);

            if ($action == 'logout') {
                /**
                 * @var \Amasty\AdminActionsLog\Model\LoginAttempts $loginAttemptsModel
                 */
                $loginAttemptsModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\LoginAttempts');
                $loginAttemptsModel->logout($observer);
            }

            if ($this->_scopeConfig->getValue('amaudit/log/log_enable_visit_history')) {
                /**
                 * @var \Amasty\AdminActionsLog\Model\ActiveSessions $activeSessionsModel
                 */
                $activeSessionsModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\ActiveSessions');
                $activeSessionsModel->updateOnlineAdminActivity($this->_helper->getSessionId());
            }

        }
    }

    protected function _needToRegister($action)
    {
        $needToRegister = false;

        $actionsToRegister = [
            'save',
            'edit',
            'delete',
            'massDelete',
            'deleteWebsitePost',
            'inlineEdit',
            'addComment',
            'massDisable',
            'massEnable',
            'restore',
            'cancel',
            'hold',
            'unhold',
            'post',
            'saverole',
            'massOnTheFly',
            'importPost',
        ];

        if (in_array($action, $actionsToRegister)) {
            $needToRegister = true;
        }

        return $needToRegister;
    }

    /**
     * @param \Magento\Framework\App\Request\Http $request
     */
    protected function _saveExport($request)
    {
        $action = $request->getActionName();

        if (($isCsv = stripos($action, 'csv') !== false)
            || stripos($action, 'xml') !== false
            || stripos($action, 'excel') !== false
            || $action == 'exportPost'
        ) {
            $data['date_time'] = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
            $data['username'] = $this->_authSession->getUser()->getUserName();
            if ($isCsv) {
                $data['type'] = 'exportCsv';
            } else {
                $data['type'] = 'exportXml';
            }
            $category = $request->getModuleName() . ' ' . $request->getControllerName();
            $data['category'] = $category;
            $data['category_name'] = $category;
            $data['item'] = __('Data was exported');
            $data['store_id'] = 0;

            /** @var \Amasty\AdminActionsLog\Model\Log $logModel */
            $logModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log');
            $logModel->addData($data);
            $logModel->save();
        }
    }

    /**
     * @param \Magento\Framework\App\Request\Http $request
     */
    protected function _saveCache($request)
    {
        if ($request->getControllerName() == 'cache') {
            $action = $request->getActionName();

            if ($action != 'index') {
                $data['date_time'] = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
                $data['username'] = $this->_authSession->getUser()->getUserName();
                $data['type'] = $action;
                $data['category'] = __('Cache');
                $data['category_name'] = __('Cache');
                $data['item'] = __('Cache');
                $data['store_id'] = 0;

                /** @var \Amasty\AdminActionsLog\Model\Log $logModel */
                $logModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log');
                $logModel->addData($data);
                $logModel->save();
            }
        }
    }
}
