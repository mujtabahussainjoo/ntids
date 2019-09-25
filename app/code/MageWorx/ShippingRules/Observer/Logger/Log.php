<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Observer\Logger;

use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Model\Rule;

/**
 * Class Log
 * @package MageWorx\ShippingRules\Observer\Logger
 */
class Log extends AbstractLoggerObserver
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->observer = $observer;
        if (!$this->helper->isLoggerEnabled()) {
            return;
        }

        $logType = $observer->getLogType();
        if (!$logType) {
            return;
        }

        if (!method_exists($this, $logType)) {
            return;
        }

        $this->{$logType}();
    }

    /**
     * Clear data and start new log
     *
     * @throws LocalizedException
     */
    protected function startRulesValidationProcessing()
    {
        $currentMethod = $this->observer->getCurrentMethod();
        if (!$currentMethod) {
            throw new LocalizedException(__('Empty method code'));
        }

        // Create new info data object
        /** @var \Magento\Framework\DataObject $currentInfo */
        $this->currentInfo = $this->logger->createNewInfo($currentMethod);

        // Drop all stored data about the previous rule
        $this->clearData();
    }

    /**
     * Clear data and start new log when rules are start applying
     *
     * @throws LocalizedException
     */
    protected function startApplyingRulesProcess()
    {
        $currentMethod = $this->observer->getCurrentMethod();
        if (!$currentMethod) {
            throw new LocalizedException(__('Empty method code'));
        }

        // Create new info data object
        /** @var \Magento\Framework\DataObject $currentInfo */
        $this->currentInfo = $this->logger->createNewInfo($currentMethod);

        // Drop all stored data about the previous rule
        $this->clearData();
    }

    /**
     * Log main info about the rule: index, id, sort order
     *
     * @throws LocalizedException
     */
    protected function startRuleValidation()
    {
        /** @var Rule $rule */
        $rule = $this->getRule();
        $this->ruleLogData = [];
        $this->ruleLogData['index'] = $this->iterator++;
        $this->ruleLogData['rule_id'] = $rule->getId();
        $this->ruleLogData['sort_order'] = $rule->getSortOrder();
    }

    /**
     * Log data about rule whenever it is invalid
     */
    protected function logInvalidRule()
    {
        /** @var Rule $rule */
        $rule = $this->getRule();
        $this->ruleLogData['valid'] = false;
        if ($rule->getLogData()) {
            $this->ruleLogData = array_merge_recursive($this->ruleLogData, $rule->getLogData());
        }
        $this->currentInfo['rules'][$rule->getUniqueKey()] = $this->ruleLogData;
    }

    /**
     * Log data about rule whenever in is mark as stop further processing
     */
    protected function logStopProcessingRule()
    {
        /** @var Rule $rule */
        $rule = $this->getRule();
        $this->ruleLogData['stop_processing'] = true;
        if ($rule->getLogData()) {
            $this->ruleLogData = array_merge_recursive($this->ruleLogData, $rule->getLogData());
        }
        $this->currentInfo['rules'][$rule->getUniqueKey()] = $this->ruleLogData;
    }

    /**
     * Log data when rule successfully validated
     */
    protected function stopRuleValidation()
    {
        /** @var Rule $rule */
        $rule = $this->getRule();
        if ($rule->getLogData()) {
            $this->ruleLogData = array_merge_recursive($this->ruleLogData, $rule->getLogData());
        }
        $this->currentInfo['rules'][$rule->getUniqueKey()] = $this->ruleLogData;
    }

    /**
     * Log data when all rules was validated
     *
     * @throws LocalizedException
     */
    protected function stopAllRulesValidation()
    {
        $currentMethod = $this->observer->getCurrentMethod();
        if (!$currentMethod) {
            throw new LocalizedException(__('Empty method code'));
        }
        $this->logger->saveInfo($currentMethod, $this->currentInfo);
    }

    /**
     * Log data when new rule is applying
     */
    protected function startApplyingRule()
    {
        /** @var Rule $rule */
        $rule = $this->getRule();
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $rate */
        $rate = $this->observer->getRate();
        $this->ruleLogData = [];
        $this->ruleLogData['rule_id'] = $rule->getId();
        $this->ruleLogData['input_data']['price'] = $rate->getPrice();
        $this->ruleLogData['input_data']['availability'] = $rate->getIsDisabled() ? false : true;
    }

    /**
     * Log data when cost was rewritten
     */
    protected function logRewrittenCost()
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $rate */
        $rate = $this->observer->getRate();
        $this->ruleLogData['cost_overwrote'] = true;
        $this->ruleLogData['output_data']['price'] = $rate->getPrice();
        $this->ruleLogData['output_data']['availability'] = $rate->getIsDisabled() ? false : true;
    }

    /**
     * Log data when method was disabled by rule
     */
    protected function logDisabledMethod()
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $rate */
        $rate = $this->observer->getRate();
        $this->ruleLogData['disabled'] = true;
        $this->ruleLogData['output_data']['price'] = $rate->getPrice();
        $this->ruleLogData['output_data']['availability'] = $rate->getIsDisabled() ? false : true;
    }

    /**
     * Log data when method data was changed by rule
     */
    protected function logChangeMethodData()
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $rate */
        $rate = $this->observer->getRate();
        $this->ruleLogData['changed_data'] = true;
        $this->ruleLogData['output_data']['title'] = $rate->getMethodTitle();
    }

    /**
     * Log data when applying process is ending
     */
    protected function endApplyingRule()
    {
        /** @var Rule $rule */
        $rule = $this->getRule();
        if (!empty($this->ruleLogData['cost_overwrote']) || !empty($this->ruleLogData['disabled'])) {
            $this->ruleLogData['processed'] = true;
        } else {
            $this->ruleLogData['processed'] = false;
        }
        if (!empty($this->currentInfo['rules'][$rule->getUniqueKey()])) {
            $this->currentInfo['rules'][$rule->getUniqueKey()] =
                array_merge($this->currentInfo['rules'][$rule->getUniqueKey()], $this->ruleLogData);
        } else {
            $this->currentInfo['rules'][$rule->getUniqueKey()] = $this->ruleLogData;
        }
    }

    /**
     * Log data from calculator
     *
     * @throws LocalizedException
     */
    protected function logDetailedAction()
    {
        /** @var \MageWorx\ShippingRules\Model\Rule\Action\Rate\RateInterface $calculator */
        $calculator = $this->observer->getCalculator();
        if (!$calculator instanceof \MageWorx\ShippingRules\Model\Rule\Action\Rate\RateInterface) {
            throw new LocalizedException(__('Empty calculator'));
        }

        $action = $this->observer->getAction();
        if (!$action) {
            throw new LocalizedException(__('Empty action code'));
        }

        $this->ruleLogData['output_data']['detailed_actions'][$action] = $calculator->getLogInfo();
    }

    /**
     * Save logged data when all rules was applied
     *
     * @throws LocalizedException
     */
    protected function allRulesAreApplied()
    {
        $currentMethod = $this->observer->getCurrentMethod();
        if (!$currentMethod) {
            throw new LocalizedException(__('Empty method code'));
        }
        $this->logger->saveInfo($currentMethod, $this->currentInfo);
    }
}
