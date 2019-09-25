<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class RulesApplier
{

    const SORT_MULTIPLIER = 1000;

    /** @var Session|\Magento\Backend\Model\Session\Quote */
    protected $session;

    /** @var Utility */
    protected $validatorUtility;

    /** @var Rule\Action\RateFactory */
    protected $rateFactory;

    protected $shippingMethods = [];

    /** @var \Magento\Framework\Event\ManagerInterface */
    protected $eventManager;

    /**
     * @param Rule\Action\RateFactory $rateFactory
     * @param Session $checkoutSession
     * @param \Magento\Backend\Model\Session\Quote $backendQuoteSession
     * @param \Magento\Framework\App\State $state
     * @param Utility $utility
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        Rule\Action\RateFactory $rateFactory,
        Session $checkoutSession,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Magento\Framework\App\State $state,
        Utility $utility,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->rateFactory = $rateFactory;
        $this->validatorUtility = $utility;
        if ($state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $this->session = $backendQuoteSession;
        } else {
            $this->session = $checkoutSession;
        }
        $this->eventManager = $eventManager;
    }

    /**
     * Apply rules to current order item
     *
     * @param Method $rate
     * @param array|ResourceModel\Rule\Collection $rules
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function applyRules(Method $rate, array $rules)
    {
        /** @var string $currentRate */
        $currentRate = Rule::getMethodCode($rate);

        $this->eventManager->dispatch(
            'mwx_start_applying_rules_process',
            [
                'log_type' => 'startApplyingRulesProcess',
                'current_method' => $currentRate,
            ]
        );

        /** @var \MageWorx\ShippingRules\Model\Rule $rule */
        foreach ($rules as $rule) {
            // Do not apply one rule more then one time to the one rate
            $ruleId = $rule->getId();
            $rateAppliedRules = is_array($rate->getAppliedRules()) ? $rate->getAppliedRules() : [];
            if (in_array($ruleId, $rateAppliedRules)) {
                continue;
            }

            $this->eventManager->dispatch(
                'mwx_start_applying_rule',
                [
                    'log_type' => 'startApplyingRule',
                    'rule' => $rule,
                    'rate' => $rate
                ]
            );

            // Process rules actions
            foreach ($rule->getActionType() as $actionType) {
                switch ($actionType) {
                    case Rule::ACTION_OVERWRITE_COST:
                        if (!in_array($currentRate, $rule->getShippingMethods())) {
                            break;
                        }
                        $this->overwriteCost($rule, $rate);

                        $this->eventManager->dispatch(
                            'mwx_rule_overwrite_rate_cost',
                            [
                                'log_type' => 'logRewrittenCost',
                                'rate' => $rate
                            ]
                        );

                        break;
                    case Rule::ACTION_DISABLE_SM:
                        if (is_array($rule->getDisabledShippingMethods()) &&
                            in_array($currentRate, $rule->getDisabledShippingMethods())
                        ) {
                            $this->disableShippingMethod($rate, $rule);
                            $this->eventManager->dispatch(
                                'mwx_rule_method_disabled',
                                [
                                    'log_type' => 'logDisabledMethod',
                                    'rate' => $rate
                                ]
                            );
                        }
                        break;
                    case Rule::ACTION_CHANGE_SM_DATA:
                        $rule->changeShippingMethodData($rate);
                        $this->eventManager->dispatch(
                            'mwx_rule_method_changed',
                            [
                                'log_type' => 'logChangeMethodData',
                                'rate' => $rate
                            ]
                        );
                        break;
                }
            }

            $this->updateShippingMethodsAvailability();

            // Update applied rules in the shipping method
            $appliedRules = array_merge($rateAppliedRules, [$ruleId]);
            $rate->setAppliedRules($appliedRules);

            $this->eventManager->dispatch(
                'mwx_end_applying_rule',
                [
                    'log_type' => 'endApplyingRule',
                    'rule' => $rule
                ]
            );
        }

        $this->eventManager->dispatch(
            'mwx_all_rules_are_applied',
            [
                'log_type' => 'allRulesAreApplied',
                'current_method' => $currentRate,
            ]
        );

        return $rate;
    }

    /**
     * Overwrite shipping method cost & price
     *
     * @param Rule $rule
     * @param Method $rate
     * @return Method
     */
    protected function overwriteCost(Rule $rule, Method $rate)
    {
        // Check what action is used in rule
        $actionsCommaSeparated = $rule->getSimpleAction();
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->session->getQuote();

        if (!$actionsCommaSeparated) {
            return $rate;
        }

        $actions = explode(',', $actionsCommaSeparated);
        $sortedActions = $this->sortActions($actions, $rule);

        foreach ($sortedActions as $action) {
            // Do not change price for the free shipping method
            $code = Rule::getMethodCode($rate);
            if ($code === Rule::FREE_SHIPPING_CODE) {
                return $this;
            }

            // Create calculator for actual action & Calculate result
            $calculator = $this->rateFactory->create($action);
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $rate */
            $rate = $calculator->calculate($rule, $rate, $quote);
            $this->eventManager->dispatch(
                'mwx_log_detailed_action',
                [
                    'log_type' => 'logDetailedAction',
                    'calculator' => $calculator,
                    'action' => $action
                ]
            );
        }

        return $rate;
    }

    /**
     * Sort the rule actions
     *
     * @param array $actions
     * @param Rule $rule
     * @return array
     */
    protected function sortActions(array $actions, Rule $rule)
    {
        $amounts = $rule->getAmount();
        $sortedActions = [];

        foreach ($actions as $action) {
            // Do not sort not existing actions
            if (empty($amounts[$action])) {
                continue;
            }

            // Get original sort order
            $sortOrder = $amounts[$action]['sort'];

            /**
             * Update the sort order to prevent overwriting.
             * It's possible that exists more than one rule with the same sort order)
             */
            $updatedSort = (int)$sortOrder * self::SORT_MULTIPLIER;
            while (isset($sortedActions[$updatedSort])) {
                $updatedSort++;
            }

            // Save the action with the new sort order (numeric array key)
            $sortedActions[$updatedSort] = $action;
        }

        ksort($sortedActions);

        return $sortedActions;
    }

    /**
     * Add current shipping method to array of disabled shipping methods
     *
     * @param Method $rate
     * @param Rule $rule
     * @return $this
     */
    public function disableShippingMethod(Method $rate, Rule $rule)
    {
        $rate->setIsDisabled(true);
        $storeId = $this->session->getStoreId();
        if ($rule->getDisplayErrorMessage()) {
            $errorMessage = $rule->getStoreSpecificErrorMessage($rate, $storeId);
            $rate->setShowError(true);
            $rate->setCustomErrorMessage($errorMessage);
        } else {
            // If method completely disabled in rule with max priority - do not show error message!
            $rate->setShowError(false);
        }

        $code = Rule::getMethodCode($rate);
        $this->shippingMethods[$code] = Rule::DISABLED;

        return $this;
    }

    /**
     * Save shipping methods availability in the checkout session
     *
     * @return $this
     */
    protected function updateShippingMethodsAvailability()
    {

        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $this->session->getQuote()->getShippingAddress();
        $existingMethods = $address->getShippingRulesMethods();

        if (!$existingMethods) {
            $existingMethods = [];
        }

        $allMethods = array_merge($existingMethods, $this->shippingMethods);
        $address->setShippingRulesMethods($allMethods);

        return $this;
    }

    /**
     * @param $address
     * @param int[] $appliedRuleIds
     * @return $this
     */
    public function setAppliedShippingRuleIds($address, array $appliedRuleIds)
    {
        $address->setAppliedShippingRuleIds(
            $this->validatorUtility->mergeIds($address->getAppliedShippingRuleIds(), $appliedRuleIds)
        );

        return $this;
    }
}
