<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoIP\Helper;

/**
 * GeoIP COUNTRY helper
 */
class Country extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;
    
    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $sourceCountry;
    
    /**
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Directory\Model\Config\Source\Country $sourceCountry
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Directory\Model\Config\Source\Country $sourceCountry
    ) {
        $this->assetRepo = $assetRepo;
        $this->sourceCountry = $sourceCountry;
    }
    
    /**
     * Return path to country flag
     *
     * @param string $name
     * @return string
     */
    public function getFlagPath($name = null)
    {
        $flagName = strtolower($name) . '.png';
        
        return $this->assetRepo->getUrl('MageWorx_GeoIP::images/flags/' . $flagName);
    }
    
    /**
     * Changes country code to upper case
     *
     * @param string $countryCode
     * @return string
     */
    public function prepareCode($countryCode)
    {
        return strtoupper(trim($countryCode));
    }
    
    /**
     * Check whether country code is valid
     *
     * @param string $code
     * @return bool
     */
    public function checkCountryCode($code)
    {
        $allCountries = $this->sourceCountry->toOptionArray(true);
        $code = $this->prepareCode($code);

        $isValid = false;
        foreach ($allCountries as $country) {
            if ($country['value'] == $code) {
                $isValid = true;
                break;
            }
        }

        return $isValid;
    }
}
