<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Observer\Logger;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\ShippingRules\Helper\Data;
use MageWorx\ShippingRules\Model\Logger as LoggerInstance;
use MageWorx\ShippingRules\Model\Rule;
use Magento\Framework\Exception\LocalizedException;

abstract class AbstractLoggerObserver implements ObserverInterface
{
    /**
     * @var LoggerInstance
     */
    protected $logger;

    /**
     * @var array
     */
    protected $currentInfo;

    /**
     * @var int
     */
    protected $iterator = 0;

    /**
     * @var array
     */
    protected $ruleLogData = [];

    /**
     * @var Observer
     */
    protected $observer;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param LoggerInstance $logger
     * @param Data $helper
     */
    public function __construct(
        LoggerInstance $logger,
        Data $helper
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * Clear temporary data (drop)
     *
     * @return $this
     */
    protected function clearData()
    {
        $this->ruleLogData = [];
        $this->iterator = 0;

        return $this;
    }

    /**
     * Validate rule instance in observer
     *
     * @return Rule
     * @throws LocalizedException
     */
    protected function validateRuleExists()
    {
        /** @var Rule $rule */
        $rule = $this->observer->getRule();
        if (!$rule instanceof Rule) {
            throw new LocalizedException(__('Empty or invalid rule'));
        }

        return $rule;
    }

    /**
     * Get rule instance from observer
     *
     * @return Rule
     * @throws LocalizedException
     */
    protected function getRule()
    {
        return $this->validateRuleExists();
    }
}
