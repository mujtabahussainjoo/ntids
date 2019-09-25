<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model;

use Magento\Framework\Model\AbstractModel;
use Amasty\AdminActionsLog\lib\BrowserDetection;

class LoginAttempts extends AbstractModel
{
    const SUCCESS = 1;
    const UNSUCCESS = 0;
    const LOGOUT = 2;
    const MIN_ALL_COUNT = 5;
    const WEEK = 604800;
    const MIN_UNSUCCESSFUL_COUNT = 5;

    protected $_objectManager;
    protected $_authSession;
    protected $_helper;
    protected $_scopeConfig;
    protected $_registryManager;
    protected $_localeLists;
    protected $_resourceConfig;

    /** @var \Magento\Framework\App\Request\Http $request */
    protected $request;

    protected function _construct()
    {
        $this->_init('Amasty\AdminActionsLog\Model\ResourceModel\LoginAttempts');
    }

    /**
     * LoginAttempts constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param \Amasty\AdminActionsLog\Helper\Data $helper
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Amasty\AdminActionsLog\Helper\Data $helper,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\App\Request\Http $request
    )
    {
        parent::__construct($context, $coreRegistry);
        $this->_objectManager = $objectManager;
        $this->_authSession = $authSession;
        $this->_registryManager = isset($data['registry']) ? $data['registry'] : $coreRegistry;
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_localeLists = $localeLists;
        $this->_resourceConfig = $resourceConfig;
        $this->request = $request;
    }

    public function prepareUserLoginData($observer, $status)
    {
        /** @var $user \Magento\User\Model\User */
        $user = $observer->getUser();
        $userData['date_time'] = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
        if ($user || $user = $this->_authSession->getUser()) {
            $userData['username'] = $user->getUserName();
        } else {
            $userData['username'] = $observer->getUserName();
            /** @var $user \Magento\User\Model\User */
            $user = $this->_objectManager->get('Magento\User\Model\User')->loadByUsername($userData['username']);
        }

        $userData['name'] = $user->getFirstName() . ' ' . $user->getLastName();

        $userData['ip'] = $this->getVisitorIp();
        $userData['status'] = $status;
        if ($this->_scopeConfig->getValue('amaudit/geolocation/geolocation_enable')) {
            if ($this->_helper->canUseGeolocation()) {
                /**
                 * @var \Amasty\Geoip\Model\Geolocation $geoLocationModel
                 */
                $geoLocationModel = $this->_objectManager->get('\Amasty\Geoip\Model\Geolocation');
//                $userData['ip'] = '46.28.103.255';//Minsk
                $location = $geoLocationModel->locate($userData['ip']);
                $country = '';
                if ($location->getCountry()) {
                    $country = $this->_localeLists->getCountryTranslation($location->getCountry());
                }
                $city = '';
                if ($location->getCity()) {
                    $city = ', ' . $location->getCity();
                }
                $userData['location'] = $country . $city;
                $userData['country_id'] = $location->getCountry();
            }
        }
        $browser = new BrowserDetection();
        $userBrowserName = $browser->getBrowser();
        $userBrowserVer = $browser->getVersion();
        $userData['user_agent'] = $userBrowserName . ' ' . $userBrowserVer;

        return $userData;
    }

    public function logout($observer)
    {
        $userData = $this->prepareUserLoginData($observer, \Amasty\AdminActionsLog\Model\LoginAttempts::LOGOUT);
        $this->setData($userData);
        $this->save();

        if ($this->_scopeConfig->getValue('amaudit/log/log_enable_visit_history')) {
            /**
             * @var \Amasty\AdminActionsLog\Model\ActiveSessions $activeSessionModel
             */
            $activeSessionModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\ActiveSessions');
            $activeSessionModel->removeOnlineAdmin($this->_helper->getSessionId(), $observer);
        }
    }

    public function clearLog($fromObserver = true)
    {
        $logCollection = $this->getCollection();

        $where = [];

        if ($fromObserver) {
            $days = $this->_scopeConfig->getValue('amaudit/log/log_delete_login_attempts_after_days');
            $where['date_time < NOW() - INTERVAL ? DAY'] = $days;
        }

        $logCollection->getConnection()->delete($logCollection->getMainTable(), $where);
    }

    public function isSuspicious($userData)
    {
        $time = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
        $intTime = strtotime($time);
        $intlastTime = $intTime - LoginAttempts::WEEK;
        $lastTime = date('Y-m-d H:i:s', $intlastTime);
        $allCollection = $this->getCollection()
            ->addFieldToFilter('date_time', array('from' => $lastTime, 'to' => $time))
            ->addFieldToFilter('status', 1);
        $allCount = $allCollection->count();
        $allCollection->clear();
        $currentUserCollection = $allCollection
            ->addFieldToFilter('country_id', substr($userData['country_id'], 0, 3));
        $currentUserCollection->getSelectCountSql();
        $currentUserCount = $currentUserCollection->count();

        if (($allCount >= LoginAttempts::MIN_ALL_COUNT) && ($currentUserCount == 0)) {
            return true;
        }

        return false;
    }

    /**
     * If email did not send - return count of unsuccessful login for last hour .
     * @return int
     */
    public function getUnsuccessfulCount()
    {
        $latestSending = $this->_scopeConfig->getValue('amaudit/unsuccessful_log_mailing/latest_sending');
        $duration = 3600;
        /**
         * @var \Magento\Framework\Stdlib\DateTime\DateTime $time
         */
        $time = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
        $intTime = strtotime($time);
        $count = 0;
        if (($intTime - $latestSending) > $duration) {
            $unsuccessfulDataCollection = $this->getCollection();
            $lastHour = $intTime - $duration;
            $fromHour = date('Y-m-d H:i:s', $lastHour);
            $unsuccessfulDataCollection
                ->addFieldToFilter('date_time', array('from' => $fromHour, 'to' => $time))
                ->addFieldToFilter('status', $this::UNSUCCESS);

            $count = $unsuccessfulDataCollection->count();
        }

        if ($count >= $this::MIN_UNSUCCESSFUL_COUNT) {
            $this->_resourceConfig->saveConfig(
                'amaudit/unsuccessful_log_mailing/latest_sending',
                $intTime,
                'default',
                0
            );
        }

        return $count;
    }

    protected function getVisitorIp() {
        /** @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddressModel */
        $remoteAddressModel = $this->_objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
        $remoteIp = $remoteAddressModel->getRemoteAddress();

        if (!is_null($this->request->getServer('HTTP_X_REAL_IP'))) {
            $remoteIp = $this->request->getServer('HTTP_X_REAL_IP');
        } elseif (!is_null($this->request->getServer('HTTP_X_FORWARDED_FOR'))) {
            $remoteIp = explode(',', str_replace(' ', '', $this->request->getServer('HTTP_X_FORWARDED_FOR')));
            $remoteIp = $remoteIp[0];
        }
        return $remoteIp;
    }
}
