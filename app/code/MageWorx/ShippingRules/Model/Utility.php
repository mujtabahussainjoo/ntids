<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Model\Quote\Address;

class Utility
{

    /** @var PriceCurrencyInterface */
    protected $priceCurrency;

    /** @var TimezoneInterface  */
    protected $timezone;

    /** @var DateTime  */
    protected $datetime;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param DateTime $datetime
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        DateTime $datetime,
        TimezoneInterface $timezone
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->timezone = $timezone;
        $this->datetime = $datetime;
    }

    /**
     * Check if rule can be applied for specific address/quote/customer
     *
     * @param \MageWorx\ShippingRules\Model\Rule $rule
     * @param Address $address
     * @param string $currentMethod
     * @return bool
     */
    public function canProcessRule(Rule $rule, Address $address, $currentMethod)
    {
        // Validate address
        if ($rule->hasIsValidForAddress($address) && !$address->isObjectNew()) {
            return $rule->getIsValidForAddress($address);
        }

        // Validate time
        if (!$this->validateTime($rule)) {
            $rule->log('invalid_time', true);
            return false;
        }

        // Validate day of week
        if (!$this->validateDayOfWeek($rule)) {
            $rule->log('invalid_day_of_week', true);
            return false;
        }

        // Check is rule can affect current shipping method
        $affectShippingMethods = $rule->getAffectedShippingMethods();
        if (!empty($affectShippingMethods) && !in_array($currentMethod, $affectShippingMethods)) {
            $rule->log('invalid_shipping_method', true);
            return false;
        }

        // quote does not meet rule's conditions
        if (!$rule->validate($address)) {
            $rule->setIsValidForAddress($address, false);
            $rule->log('invalid_conditions', true);
            return false;
        }

        // passed all validations, remember to be valid
        $rule->setIsValidForAddress($address, true);

        return true;
    }

    /**
     * Validate rule by time (in minutes from 0 to 1440)
     *
     * @param Rule $rule
     * @return bool
     */
    public function validateTime(Rule $rule)
    {
        // Do not validate the rule if it is not using the time restrictions
        if (!$rule->getUseTime()) {
            return true;
        }

        $isRuleEnabledInTimeRange = $rule->getTimeEnabled();
        $ruleTimeFrom = $rule->getTimeFrom();
        $ruleTimeTo = $rule->getTimeTo();

        /** @var \Zend_Date $date */
        $date = $this->getCurrentStoreDateTime();
        $currentHours = (int)$date->toString('H');
        $currentMinutes = (int)$date->toString('m');
        $currentTimeInMinutes = $currentHours * 60 + $currentMinutes;

        if ($isRuleEnabledInTimeRange) {
            // Rule is enabled at this time range
            if ($currentTimeInMinutes >= $ruleTimeFrom && $currentTimeInMinutes <= $ruleTimeTo) {
                return true;
            }
        } else {
            // Rule is disabled at this time range
            if ($currentTimeInMinutes <= $ruleTimeFrom && $currentTimeInMinutes >= $ruleTimeTo) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate rule by day of the week in current store locale
     *
     * @param Rule $rule
     * @return bool
     */
    public function validateDayOfWeek(Rule $rule)
    {
        $daysOfWeek = $rule->getDaysOfWeek();

        // Available for all days (no one option was selected)
        if (!$daysOfWeek) {
            return true;
        }

        $ruleDays = explode(',', $daysOfWeek);

        // Available for all 7 days (all options was selected)
        if (count($ruleDays) === 7) {
            return true;
        }

        $date = $this->getCurrentStoreDateTime();
        $dayOfTheWeek = $date->toString('eee');

        if (in_array($dayOfTheWeek, $ruleDays)) {
            return true;
        }

        return false;
    }

    /**
     * Merge two sets of ids
     *
     * @param mixed[]|string $a1
     * @param mixed[]|string $a2
     * @param bool $asString
     * @return mixed[]|string
     */
    public function mergeIds($a1, $a2, $asString = true)
    {
        if (!is_array($a1)) {
            $a1 = empty($a1) ? [] : explode(',', $a1);
        }
        if (!is_array($a2)) {
            $a2 = empty($a2) ? [] : explode(',', $a2);
        }
        $a = array_unique(array_merge($a1, $a2));
        if ($asString) {
            $a = implode(',', $a);
        }
        return $a;
    }

    /**
     * Get datetime in current store locale
     *
     * @return \Zend_Date
     */
    protected function getCurrentStoreDateTime()
    {
        $currentStoreDate = $this->timezone->date()->format('Y-m-d H:i:s');
        $zendDate = new \Zend_Date($currentStoreDate);

        return $zendDate;
    }
}
