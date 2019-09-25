<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Rule\Action\Rate;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use MageWorx\ShippingRules\Model\Rule;

interface RateInterface
{
    /**
     * @param Rule $rule
     * @param Method $rate
     * @param Quote $quote
     * @return Method
     */
    public function calculate(Rule $rule, Method $rate, Quote $quote);

    /**
     * Set method of calculation:
     * Possible valid values is: fixed or percent
     *
     * @param string $method
     * @return RateInterface
     */
    public function setCalculationMethod($method);

    /**
     * Get current used calculation method
     * Possible valid values is: fixed or percent
     *
     * @return string
     */
    public function getCalculationMethod();

    /**
     * Set apply method.
     * Possible valid values is: overwrite, surcharge or discount
     *
     * Overwrite - totally overwrites the shipping amount of the method
     * Surcharge - adds calculated surcharge to the current amount of the shipping method
     * Discount - adds calculated discount to the amount of the shipping method (decreasing it)
     *
     * @param string $method
     * @return RateInterface
     */
    public function setApplyMethod($method);

    /**
     * Get current used apply method
     * Possible valid values is: overwrite, surcharge or discount
     *
     * Overwrite - totally overwrites the shipping amount of the method
     * Surcharge - adds calculated surcharge to the current amount of the shipping method
     * Discount - adds calculated discount to the amount of the shipping method (decreasing it)
     *
     * @return string
     */
    public function getApplyMethod();

    /**
     * Set current quote for rate.
     * Usual the quote is used for the correct price calculations.
     *
     * @param Quote $quote
     * @return RateInterface
     */
    public function setQuote(Quote $quote);

    /**
     * Getting current used quote.
     * Usual the quote is used for the correct price calculations.
     *
     * @return Quote
     */
    public function getQuote();

    /**
     * Set full amount type.
     * Usually this type is used to find the amount method.
     *
     * @param string $type
     * @return RateInterface
     */
    public function setAmountType($type);

    /**
     * Get full amount type.
     * Usually this type is used to find the amount method.
     *
     * @return string
     */
    public function getAmountType();

    /**
     * Set current shipping method rate for modifications
     *
     * @param Method $rate
     * @return mixed
     */
    public function setRate(Method $rate);

    /**
     * Get current active shipping method rate
     *
     * @return Method
     */
    public function getRate();

    /**
     * Get info for logger about calculation
     *
     * @return array
     */
    public function getLogInfo();
}
