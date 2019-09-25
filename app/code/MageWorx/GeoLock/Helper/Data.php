<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoLock\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'mageworx_geoip/geo_lock_general/active';
    const XML_PATH_COUNTRIES = 'mageworx_geoip/geo_lock_general/countries';
    const XML_PATH_REDIRECT_URL = 'mageworx_geoip/geo_lock_general/redirect_url';
    const XML_PATH_RULE_TYPE = 'mageworx_geoip/geo_lock_general/rule_type';
    const XML_PATH_IP_BLACK_LIST = 'mageworx_geoip/geo_lock_general/ip_black_list';
    const XML_PATH_IP_WHITE_LIST = 'mageworx_geoip/geo_lock_general/ip_white_list';

    const IP_LIST_REGEXP_DELIMITER = '/[\r?\n]+|[,?]+/';

    protected $countryGeoIpHelper;

    /**
     * @param Context $context
     * @param \MageWorx\GeoIP\Helper\Country $countryGeoIpHelper
     */
    public function __construct(
        Context $context,
        \MageWorx\GeoIP\Helper\Country $countryGeoIpHelper
    ) {
        parent::__construct($context);
        $this->countryGeoIpHelper = $countryGeoIpHelper;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return array|null
     */
    public function getCountries($storeId = null)
    {
        $countriesRawValue = $this->scopeConfig->getValue(
            self::XML_PATH_COUNTRIES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $countriesRawValue = $this->countryGeoIpHelper->prepareCode($countriesRawValue);
        if ($countriesRawValue) {
            $countriesCode = explode(',', $countriesRawValue);

            return $countriesCode;
        }

        return $countriesRawValue ? $countriesRawValue : null;
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getRedirectUrl($storeId = null)
    {
        $redirectUrl = $this->scopeConfig->getValue(
            self::XML_PATH_REDIRECT_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $redirectUrlPure = $this->prepareRedirectUrl($redirectUrl);

        return $redirectUrlPure;
    }

    /**
     * Prepare redirect URL
     *
     * @param $url
     * @return string
     */
    public function prepareRedirectUrl($url)
    {
        $prefix = '/';
        if (!empty($url) && !preg_match("/^https?:\/\/.+/i", $url) && (substr($url, 0, 1) != $prefix)) {
            $url = $prefix.$url;
        }

        return $url;
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getRuleType($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RULE_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return array
     */
    public function getIpBlackList($storeId = null)
    {
        $rawIpList = $this->scopeConfig->getValue(
            self::XML_PATH_IP_BLACK_LIST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $ipList = array_filter((array) preg_split(self::IP_LIST_REGEXP_DELIMITER, $rawIpList));

        return $ipList;
    }

    /**
     * @param null $storeId
     * @return array
     */
    public function getIpWhiteList($storeId = null)
    {
        $rawIpList = $this->scopeConfig->getValue(
            self::XML_PATH_IP_WHITE_LIST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $ipList = array_filter((array) preg_split(self::IP_LIST_REGEXP_DELIMITER, $rawIpList));

        return $ipList;
    }
}
