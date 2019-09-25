<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoIP\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * GeoIP config helper data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * XML config path database type
     */
    const XML_PATH_DATABASE_TYPE = 'mageworx_geoip/geoip_database/database_type';
    
    /**
     * XML config path to database path
     */
    const XML_PATH_DATABASE_PATH = 'mageworx_geoip/geoip_database/database_path';
    
    
    /**
     * Retrive database type (1-country or 2-city)
     *
     * @param int $storeId
     * @return int
     */
    public function getDatabaseType($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_DATABASE_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    
    /**
     * Retrive database path
     *
     * @param int $storeId
     * @return string
     */
    public function getDatabasePath($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DATABASE_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
