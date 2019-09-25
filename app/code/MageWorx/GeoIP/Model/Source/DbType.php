<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoIP\Model\Source;

class DbType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Value determine country database
     *
     * @var int
     */
    const GEOIP_COUNTRY_DATABASE = 1;
    
    /**
     * Value determine city database
     *
     * @var int
     */
    const GEOIP_CITY_DATABASE    = 2;
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
                   ['value' => self::GEOIP_COUNTRY_DATABASE, 'label' => __('GeoIP Country')],
                   ['value' => self::GEOIP_CITY_DATABASE, 'label' => __('GeoIP City')]
               ];
    }
}
