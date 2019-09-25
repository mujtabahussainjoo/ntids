<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * ShippingRules Validator Model
 *
 * @method mixed getStoreId()
 * @method Validator setStoreId($id)
 * @method mixed getCustomerGroupId()
 * @method Validator setCustomerGroupId($id)
 */
class Validator extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Rule source collection
     *
     * @var \MageWorx\ShippingRules\Model\ResourceModel\Rule\Collection
     */
    protected $rules;

    /** @var \MageWorx\ShippingRules\Model\ResourceModel\Rule\CollectionFactory */
    protected $collectionFactory;

    /** @var \MageWorx\ShippingRules\Model\Utility */
    protected $validatorUtility;

    /** @var \Magento\Checkout\Model\Session|\Magento\Backend\Model\Session\Quote */
    protected $session;

    /** @var \Magento\SalesRule\Model\Rule\Condition\Product  */
    protected $productCondition;

    protected $appliedShippingRuleIds = [];
    protected $disabledShippingMethods = [];

    /** @var \Magento\Quote\Model\QuoteRepository  */
    protected $quoteRepository;

    /** @var \Magento\Quote\Model\Quote  */
    protected $quote;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\ShippingRules\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param Utility $utility
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Backend\Model\Session\Quote $backendQuoteSession
     * @param \Magento\SalesRule\Model\Rule\Condition\Product $productCondition
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorx\ShippingRules\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \MageWorx\ShippingRules\Model\Utility $utility,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Magento\SalesRule\Model\Rule\Condition\Product $productCondition,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
    
        $this->collectionFactory = $collectionFactory;
        $this->validatorUtility = $utility;
        $this->productCondition = $productCondition;
        if ($context->getAppState()->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $this->session = $backendQuoteSession;
        } else {
            $this->session = $checkoutSession;
        }
        $this->quoteRepository = $quoteRepository;
        $this->quote = $quote;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init validator
     * Init process load collection of rules for specific store and
     * customer group
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @return $this
     */
    public function init($storeId, $customerGroupId)
    {
        $this->setStoreId($storeId)->setCustomerGroupId($customerGroupId);

        $key = $storeId . '_' . $customerGroupId;
        if (!isset($this->rules[$key])) {
            /** @var \MageWorx\ShippingRules\Model\ResourceModel\Rule\Collection $collection */
            $collection = $this->collectionFactory->create()
                ->setValidationFilter(
                    $storeId,
                    $customerGroupId
                )
                ->addFieldToFilter('is_active', 1);
            $this->rules[$key] = $collection->load();
        }

        return $this;
    }

    /**
     * @param Method $rate
     * @return bool
     */
    public function validate(Method $rate)
    {
        /**
         * @important
         * When quote taken from session it is possible that shipping address country id (or another data) will be
         * rewritten by it's original info (when customer use address from the pre-saved collection on the checkout)
         */
        /** @var \Magento\Quote\Model\Quote $quote */
        try {
            $quote = $this->quoteRepository->getActive($this->session->getQuoteId());
        } catch (NoSuchEntityException $e) {
            $quote = $this->quote && $this->quote->getId() ? $this->quote : $this->session->getQuote();
        }
        $this->quote = $quote;

        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $quote->getShippingAddress();
        /** @var array $quoteItems */
        $quoteItems = $quote->getAllItems();
        /** @var string $currentMethod */
        $currentMethod = Rule::getMethodCode($rate);

        // Do not process request without items (unusual request)
        if (empty($quoteItems)) {
            return false;
        }

        $this->_eventManager->dispatch(
            'mwx_start_rules_validation_processing',
            [
                'log_type' => 'startRulesValidationProcessing',
                'current_method' => $currentMethod,
            ]
        );

        /* @var Rule $rule */
        foreach ($this->_getRules() as $rule) {
            // If rule has been already applied - continue
            if (isset($this->appliedShippingRuleIds[$currentMethod][$rule->getId()]) ||
                $this->checkAddressAppliedRule($address, $rule, $currentMethod)
            ) {
                if ($rule->getStopRulesProcessing()) {
                    break;
                }
                continue;
            }

            $this->_eventManager->dispatch(
                'mwx_start_validate_rule',
                [
                    'log_type' => 'startRuleValidation',
                    'rule' => $rule,
                ]
            );

            // Validate rule conditions
            if (!$this->validatorUtility->canProcessRule($rule, $address, $currentMethod)) {
                $this->_eventManager->dispatch(
                    'mwx_invalid_rule',
                    [
                        'log_type' => 'logInvalidRule',
                        'rule' => $rule,
                    ]
                );
                continue;
            }

            $this->appliedShippingRuleIds[$currentMethod][$rule->getId()] = $rule;
            $this->updateAddressAppliedShippingRuleIds($address);
            $logData['valid'] = true;

            if ($rule->getStopRulesProcessing()) {
                $this->_eventManager->dispatch(
                    'mwx_rule_stop_processing',
                    [
                        'log_type' => 'logStopProcessingRule',
                        'rule' => $rule,
                    ]
                );
                break;
            }

            $this->_eventManager->dispatch(
                'mwx_stop_validate_rule',
                [
                    'log_type' => 'stopRuleValidation',
                    'rule' => $rule,
                ]
            );
        }

        $this->_eventManager->dispatch(
            'mwx_stop_validate_all_rules',
            [
                'log_type' => 'stopAllRulesValidation',
                'current_method' => $currentMethod,
            ]
        );

        $validationResult = isset($this->appliedShippingRuleIds[$currentMethod]) &&
            count($this->appliedShippingRuleIds[$currentMethod]);

        return $validationResult;
    }

    /**
     * Get available stored rules for $rate
     *
     * @param Method $rate
     * @return array
     */
    public function getAvailableRulesForRate(Method $rate)
    {
        /** @var string $currentMethod */
        $currentMethod = Rule::getMethodCode($rate);

        if (isset($this->appliedShippingRuleIds[$currentMethod]) &&
            count($this->appliedShippingRuleIds[$currentMethod])
        ) {
            return $this->appliedShippingRuleIds[$currentMethod];
        }

        return [];
    }

    /**
     * Update address applied rule ids with new rule id
     *
     * @param Address $address
     * @return Address
     */
    protected function updateAddressAppliedShippingRuleIds(
        Address $address
    ) {
    
        $addressRuleIds = $address->getAppliedShippingRulesIds();
        if (!$addressRuleIds) {
            $addressRuleIds = [];
        }

        $resultIds = array_merge($addressRuleIds, $this->appliedShippingRuleIds);
        $address->setAppliedShippingRulesIds($resultIds);

        return $address;
    }

    /**
     * If rhe rule already has been applied to the address return true
     * else return false
     *
     * @param Address $address
     * @param Rule $rule
     * @param $method
     * @return bool
     */
    protected function checkAddressAppliedRule(
        Address $address,
        Rule $rule,
        $method
    ) {
    
        $appliedRules = $address->getAppliedShippingRulesIds();

        if (!is_array($appliedRules)) {
            return false;
        }

        if (empty($appliedRules[$rule->getId()])) {
            return false;
        }

        if (in_array($method, $appliedRules[$rule->getId()])) {
            return true;
        }

        return false;
    }

    /**
     * Get rules collection for current object state
     *
     * @return \MageWorx\ShippingRules\Model\ResourceModel\Rule\Collection
     */
    protected function _getRules()
    {
        $key = $this->getStoreId() . '_' . $this->getCustomerGroupId();
        return $this->rules[$key];
    }

    /**
     * Check is item valid for the corresponding rule
     *
     * @param Rule $rule
     * @param QuoteItem $item
     * @return bool
     */
    public function isValidItem(Rule $rule, QuoteItem $item)
    {
        /** @var \Magento\SalesRule\Model\Rule\Condition\Product\Combine $actions */
        $actions = $rule->getActions();
        if (!$actions->validate($item)) {
            return false;
        }

        return true;
    }
}
