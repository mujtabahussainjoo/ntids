<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoLock\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\GeoLock\Model\Config\Source\RuleType;

class Locker implements ObserverInterface
{
    /**
     * @var \MageWorx\GeoLock\Helper\Data
     */
    protected $helper;

    /**
     * @var \MageWorx\GeoIp\Helper\Country
     */
    protected $geoIpCountryHelper;

    /**
     * @var \MageWorx\GeoIP\Model\Geoip
     */
    protected $geoIp;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    /**
     * @var bool
     */
    protected $isDenied = false;

    /**
     * @param \MageWorx\GeoLock\Helper\Data $helper
     * @param \MageWorx\GeoIP\Helper\Country $geoIpCountryHelper
     * @param \MageWorx\GeoIP\Model\Geoip $geoIp
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     */
    public function __construct(
        \MageWorx\GeoLock\Helper\Data $helper,
        \MageWorx\GeoIP\Helper\Country $geoIpCountryHelper,
        \MageWorx\GeoIP\Model\Geoip $geoIp,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {

        $this->helper = $helper;
        $this->geoIpCountryHelper = $geoIpCountryHelper;
        $this->request = $request;
        $this->geoIp = $geoIp;
        $this->storeManager = $storeManager;
        $this->redirect = $redirect;
        $this->actionFlag = $actionFlag;
    }

    /**
     * @param Observer $observer
     * @return mixed
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        if ($this->request->isAjax()) {
            return $this;
        }

        /** @var \MageWorx\GeoIP\Model\Geoip $geoIpModel */
        $currentLocation = $this->geoIp->getCurrentLocation();
        if (!$currentLocation->getCode() || !$currentLocation->getCountry()) {
            return $this;
        }

        $this->detectByRuleType($currentLocation);
        $this->detectByIpList($currentLocation);

        if ($this->isDenied === true) {
            $this->denyCustomerAccess($observer);
        }

        return $this;
    }

    /**
     * Send 403 status and "access denied" content
     * @TODO: Add redirection functionality
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return mixed
     */
    protected function denyCustomerAccess($observer)
    {
        /** @var \Magento\Framework\App\Action\Action $action */
        $action = $observer->getControllerAction();
        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $action->getResponse();

        $response->clearBody()
            ->setStatusCode(\Magento\Framework\App\Response\Http::STATUS_CODE_403);
        $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
    }

    /**
     * Filter customer by ip list: black or white
     *
     * @param $currentLocation
     * @return bool
     */
    protected function detectByIpList($currentLocation)
    {
        $customerIp = $currentLocation->getIp();

        $ipBlackList = $this->helper->getIpBlackList();
        if ($ipBlackList) {
            foreach ($ipBlackList as $ip) {
                $ip = str_replace(['*', '.'], ['\d+', '\.'], $ip);
                if (preg_match("/^{$ip}$/", $customerIp)) {
                    $this->isDenied = true;
                    break;
                }
            }
        }

        $ipWhiteList = $this->helper->getIpWhiteList();
        if ($ipWhiteList) {
            $this->isDenied = true;
            foreach ($ipWhiteList as $ip) {
                $ip = str_replace(['*', '.'], ['\d+', '\.'], $ip);
                if (preg_match("/^{$ip}$/", $customerIp)) {
                    $this->isDenied = false;
                    break;
                }
            }
        }

        return $this->isDenied;
    }

    /**
     * @param $currentLocation
     * @return bool
     */
    protected function detectByRuleType($currentLocation)
    {
        $customerCountryCode = $this->geoIpCountryHelper->prepareCode($currentLocation->getCode());
        $countries = $this->helper->getCountries();

        if (!$countries || empty($countries)) {
            return $this->isDenied;
        }

        switch ($this->helper->getRuleType()) {
            case RuleType::ALLOW:
                if (!in_array($customerCountryCode, $countries)) {
                    $this->isDenied = true;
                }
                break;
            case RuleType::DENY:
                if (in_array($customerCountryCode, $countries)) {
                    $this->isDenied = true;
                }
                break;
            default:
                return $this->isDenied;
        }

        return $this->isDenied;
    }

    /**
     * @return mixed
     */
    protected function getCurrentUrl()
    {
        return $this->storeManager->getStore()->getCurrentUrl();
    }
}
