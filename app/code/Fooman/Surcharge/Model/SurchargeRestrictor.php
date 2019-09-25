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

class SurchargeRestrictor
{
    const ADDRESS_TYPE_BILL = 'billing';
    const ADDRESS_TYPE_SHIP = 'shipping';

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param SurchargeConfig                       $config
     * @param                                       $total
     *
     * @return bool
     */
    public function surchargeApplies(
        \Magento\Quote\Api\Data\CartInterface $quote,
        SurchargeConfig $config,
        $total
    ) {
        if (!$this->isSurchargeWithinAmounts($config, $total)) {
            return false;
        }
        if (!$this->isSurchargeApplicableForGroup($config, $quote)) {
            return false;
        }
        if (!$this->isSurchargeApplicableForCountryAndRegion($config, $quote)) {
            return false;
        }
        return true;
    }

    private function isSurchargeApplicableForGroup($config, $quote)
    {
        if ($config->getApplyGroupFilter()) {
            if (in_array($quote->getCustomerGroupId(), $config->getGroups())) {
                $apply = true;
            } else {
                $apply = false;
            }
        } else {
            $apply = true;
        }
        return $apply;
    }

    private function isSurchargeWithinAmounts($config, $total)
    {
        if (strlen($config->getMax()) && $total >= $config->getMax()) {
            return false;
        }
        if (strlen($config->getMin()) && $config->getMin() > 0 && $total <= $config->getMin()) {
            return false;
        }
        return true;
    }

    private function isSurchargeApplicableForCountryAndRegion($config, $quote)
    {
        if ($config->getApplyRegionFilter()) {
            if ($config->getRegionFilterAddressType() === self::ADDRESS_TYPE_BILL) {
                $address = $quote->getBillingAddress();
                if (!$address) {
                    return false;
                }
            } else {
                $address = $quote->getShippingAddress();
                if (!$address) {
                    return false;
                }
            }
            if (in_array($address->getCountryId(), $config->getCountries())) {
                $apply = $this->isSurchargeApplicableForRegion($config, $address);
            } else {
                $apply = false;
            }
        } else {
            $apply = true;
        }
        return $apply;
    }

    private function isSurchargeApplicableForRegion($config, $address)
    {
        if (!$address->getRegionId()) {
            return true;
        }
        $regions = $config->getRegions();
        if (empty($regions)) {
            return true;
        }
        return in_array($address->getRegionId(), $config->getRegions());
    }
}
