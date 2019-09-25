<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class SurchargeConfigProvider implements ConfigProviderInterface
{

    const XML_PATH_DISPLAY_CART_SURCHARGE = 'tax/cart_display/fooman_surcharge';
    const XML_PATH_DISPLAY_CART_SURCHARGE_ZERO = 'tax/cart_display/fooman_surcharge_zero';

    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfig()
    {
        $config = [
            'foomanSurchargeConfig' => [
                'isDisplayedTaxInclusive' => $this->isDisplayedTaxInclusive(),
                'isDisplayedTaxExclusive' => $this->isDisplayedTaxExclusive(),
                'isDisplayedBoth' => $this->isDisplayedBoth(),
                'isZeroDisplayed' => $this->isZeroDisplayed(),
            ]
        ];

        return $config;
    }

    public function isDisplayedTaxInclusive()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_CART_SURCHARGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) == \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    public function isDisplayedTaxExclusive()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_CART_SURCHARGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) == \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    public function isDisplayedBoth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_CART_SURCHARGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) == \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH;
    }

    public function isZeroDisplayed()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_CART_SURCHARGE_ZERO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
