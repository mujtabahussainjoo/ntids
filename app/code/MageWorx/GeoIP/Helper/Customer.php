<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoIP\Helper;

/**
 * GeoIP CUSTOMER helper
 */
class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;
    
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var \Magento\Framework\HTTP\Header
     */
    protected $httpHeader;
    
    /**
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\Header $httpHeader
     */
    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\HTTP\Header $httpHeader
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->storeManager = $storeManager;
        $this->httpHeader = $httpHeader;
    }
    
    /**
     * Set encoded cookie
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $encode
     * @return boolean
     */
    public function setCookie($key, $value, $encode = true)
    {
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $urlParse = parse_url($store->getBaseUrl());
            $path = rtrim(str_replace('index.php', '', $urlParse['path']), '/');
            if (!empty($path)) {
                $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                    ->setDurationOneYear()
                    ->setDomain($this->httpHeader->getHttpHost())
                    ->setPath($path)
                    ->setHttpOnly(true);

                $this->cookieManager->setPublicCookie($key, $value, $metadata);
            }
        }
        
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDurationOneYear()
            ->setDomain($this->httpHeader->getHttpHost())
            ->setPath('/')
            ->setHttpOnly(true);
        
        $this->cookieManager->setPublicCookie($key, $value, $metadata);
        
        return true;
    }

    /**
     * Return decoded cookie
     *
     * @param string $key
     * @param boolean $decode
     * @return boolean|string
     */
    public function getCookie($key, $decode = false)
    {
        if ($cookieResult = $this->cookieManager->getCookie($key)) {
            return $cookieResult;
        } else {
            return false;
        }
    }
    
    /**
     * Get customer IP
     *
     * @return string
     */
    public function getCustomerIp()
    {
        if ($testIp = $this->getDebugIp()) { // for debug: paste into 'getDebugIp' country code like 'US','DE','FR','SE'
            return $testIp;
        }
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $ipArr = explode(',', $ip);
        $ip = $ipArr[count($ipArr) - 1];

        return trim($ip);
    }
    
    protected function getDebugIp($countryCode = null)
    {
        switch ($countryCode) {
            case 'US':
                return '24.24.24.24';
            case 'FR':
                return '62.147.0.1';
            case 'SE':
                return '81.13.146.205';
            case 'DE':
                return '78.159.112.71';
            default:
                return $countryCode;
        }
    }
}
