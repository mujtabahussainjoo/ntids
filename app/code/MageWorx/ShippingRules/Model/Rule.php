<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use MageWorx\ShippingRules\Api\Data\RuleInterface;
use MageWorx\ShippingRules\Api\RuleEntityInterface;

/**
 * Shipping Rule data model
 *
 * @method Rule setActionType(string $type)
 * @method Rule setShippingMethods(string $methods)
 * @method Rule setAmount(float $amount)
 * @method Rule setSimpleAction(string $action)
 * @method Rule setStopRulesProcessing($flag)
 * @method Rule setDisabledShippingMethods($methods)
 * @method Rule setChangedTitles($changedTitles)
 *
 * Days of week in PHP: from 0 to 6
 *
 * Time from & to in minutes from 0 to 1440
 * @method Rule setTimeFrom($time)
 * @method Rule setTimeTo($time)
 *
 * @method Rule setUseTime($bool)
 * @method Rule setTimeEnabled($bool)
 */
class Rule extends \Magento\Rule\Model\AbstractModel implements RuleInterface, RuleEntityInterface
{
    /**
     * Rule possible actions
     */
    const ACTION_OVERWRITE_COST = 'overwrite';
    const ACTION_DISABLE_SM = 'disable';
    const ACTION_CHANGE_SM_DATA = 'change';

    // Matrix
    const ACTION_CALCULATION_FIXED = 'fixed';
    const ACTION_CALCULATION_PERCENT = 'percent';

    const ACTION_METHOD_OVERWRITE = 'overwrite';
    const ACTION_METHOD_SURCHARGE = 'surcharge';
    const ACTION_METHOD_DISCOUNT = 'discount';

    const ACTION_TYPE_AMOUNT = 'amount';
    const ACTION_TYPE_PER_QTY_OF_ITEM = 'product'; // per Qty of Item
    const ACTION_TYPE_PER_ITEM = 'item';
    const ACTION_TYPE_PER_WEIGHT_UNIT = 'weight';

    const FREE_SHIPPING_CODE = 'freeshipping_freeshipping';

    const ENABLED = 1;
    const DISABLED = 0;

    const TABLE_NAME = 'mageworx_shippingrules';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_shippingrules_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $validatedAddresses = [];

    /** @var \MageWorx\ShippingRules\Model\Rule\Condition\CombineFactory */
    protected $condCombineFactory;

    /** @var \MageWorx\ShippingRules\Model\Rule\Condition\Product\CombineFactory */
    protected $condProdCombineF;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\DataObject */
    protected $logData;

    /** @var \Magento\Framework\DataObjectFactory */
    protected $dataObjectFactory;

    /**
     * @return array
     */
    public static function getActionCalculations()
    {
        return [
            self::ACTION_CALCULATION_FIXED,
            self::ACTION_CALCULATION_PERCENT
        ];
    }

    /**
     * @return array
     */
    public static function getActionMethods()
    {
        return [
            self::ACTION_METHOD_OVERWRITE,
            self::ACTION_METHOD_SURCHARGE,
            self::ACTION_METHOD_DISCOUNT
        ];
    }

    /**
     * @return array
     */
    public static function getActionTypes()
    {
        return [
            self::ACTION_TYPE_AMOUNT,
            self::ACTION_TYPE_PER_QTY_OF_ITEM,
            self::ACTION_TYPE_PER_ITEM,
            self::ACTION_TYPE_PER_WEIGHT_UNIT,
        ];
    }

    /**
     * Get calculation matrix as array
     *
     * @return array
     */
    public static function getCalculationMatrix()
    {
        $calculations = self::getActionCalculations();
        $methods = self::getActionMethods();
        $types = self::getActionTypes();

        $matrix = [];

        foreach ($calculations as $calculation) {
            foreach ($methods as $method) {
                foreach ($types as $type) {
                    $key = implode('_', [$calculation, $method, $type]);
                    $matrix[$key] = $key;
                }
            }
        }

        return $matrix;
    }

    /**
     * @param Method $rate
     * @return string
     */
    public static function getMethodCode(Method $rate)
    {
        /** @var string $methodCode */
        $methodCode = $rate->getCarrier() . '_' . $rate->getMethod();
        return $methodCode;
    }

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \MageWorx\ShippingRules\Model\Rule\Condition\CombineFactory $condCombineFactory
     * @param Rule\Condition\Product\CombineFactory $condProdCombineF
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \MageWorx\ShippingRules\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \MageWorx\ShippingRules\Model\Rule\Condition\Product\CombineFactory $condProdCombineF,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
    
        $this->condCombineFactory = $condCombineFactory;
        $this->condProdCombineF = $condProdCombineF;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logData = $dataObjectFactory->create();
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MageWorx\ShippingRules\Model\ResourceModel\Rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \MageWorx\ShippingRules\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \MageWorx\ShippingRules\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        $factory = $this->condProdCombineF;
        $result = $factory->create();

        return $result;
    }

    /**
     * Get shipping rule customer group Ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_getResource()->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

    /**
     * Check cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param Address $address
     * @param bool $validationResult
     * @return $this
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->validatedAddresses[$addressId]) ? $this->validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address
     *
     * @param Address $address
     * @return string
     */
    private function _getAddressId($address)
    {
        if ($address instanceof Address) {
            return $address->getId();
        }
        return $address;
    }

    /**
     * Prepare data before saving
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function beforeSave()
    {
        /**
         * Prepare store Ids if applicable and if they were set as string in comma separated format.
         * Backwards compatibility.
         */
        if ($this->hasStoreIds()) {
            $storeIds = $this->getStoreIds();
            if (!empty($storeIds)) {
                $this->setStoreIds($storeIds);
            }
        }

        parent::beforeSave();
        return $this;
    }

    /**
     * Get rule associated store Ids
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (!$this->hasStoreIds()) {
            $storeIds = $this->_getResource()->getStoreIds($this->getId());
            $this->setData('store_ids', (array)$storeIds);
        }
        return $this->getData('store_ids');
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getActionsFieldSetId($formName = '')
    {
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }

    /**
     * Get all shipping methods affected by rule (from the change price & disable sections both)
     *
     * @return array
     */
    public function getAffectedShippingMethods()
    {
        $shippingMethods = !empty($this->getShippingMethods()) ? $this->getShippingMethods() : [];
        $disabledShippingMethods = !empty($this->getDisabledShippingMethods()) ?
            $this->getDisabledShippingMethods() :
            [];
        $affectShippingMethods = array_merge($shippingMethods, $disabledShippingMethods);

        return $affectShippingMethods;
    }

    /**
     * @param Method $rate
     * @return void
     */
    public function changeShippingMethodData(\Magento\Quote\Model\Quote\Address\RateResult\Method $rate)
    {
        /** @var string $currentRate */
        $currentRate = static::getMethodCode($rate);

        $titles = $this->getChangedTitles();
        if (empty($titles)) {
            return;
        }

        foreach ($titles as $methodTitleData) {
            if ($methodTitleData['methods_id'] != $currentRate) {
                continue;
            }

            $storeId = $this->storeManager->getStore()->getId();
            if (!empty($methodTitleData['title_' . $storeId])) {
                $suitableTitle = $methodTitleData['title_' . $storeId];
            } elseif (!empty($methodTitleData['title_0'])) {
                $suitableTitle = $methodTitleData['title_0'];
            } else {
                continue;
            }
            $rate->setMethodTitle($suitableTitle);
        }
    }

    //////////////////////////////////// GETTERS ///////////////////////////////////////////////////////////////////////

    /**
     * Retrieve rule name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * Get rules description text
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getData('description');
    }

    /**
     * Get date when rule is active from
     *
     * @return mixed
     */
    public function getFromDate()
    {
        return $this->getData('from_date');
    }

    /**
     * Get date when rule is active to
     *
     * @return mixed
     */
    public function getToDate()
    {
        return $this->getData('to_date');
    }

    /**
     * Check is rule active
     *
     * @return int|bool
     */
    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    /**
     * Get serialized rules conditions
     *
     * @return string
     */
    public function getConditionsSerialized()
    {
        return $this->getData('conditions_serialized');
    }

    /**
     * Get serialized rules actions
     *
     * @return string
     */
    public function getActionsSerialized()
    {
        return $this->getData('actions_serialized');
    }

    /**
     * Sort Order of the rule
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getData('sort_order');
    }

    /**
     * Retrieve rule ID
     *
     * @return int
     */
    public function getRuleId()
    {
        return $this->getData('rule_id');
    }

    /**
     * Get specified action types for the rule
     *
     * @return array
     */
    public function getActionType()
    {
        return $this->getData('action_type');
    }

    /**
     * Get corresponding shipping methods
     *
     * @return string
     */
    public function getShippingMethods()
    {
        return $this->getData('shipping_methods');
    }

    /**
     * Get amounts
     *
     * @return array
     */
    public function getAmount()
    {
        return $this->getData('amount');
    }

    /**
     * Get simple action
     *
     * @return string
     */
    public function getSimpleAction()
    {
        return $this->getData('simple_action');
    }

    /**
     * Is need to stop another rules
     *
     * @return bool
     */
    public function getStopRulesProcessing()
    {
        return $this->getData('stop_rules_processing');
    }

    /**
     * Get all disabled shipping methods
     *
     * @return array
     */
    public function getDisabledShippingMethods()
    {
        return $this->getData('disabled_shipping_methods');
    }

    /**
     * Days of week in PHP: from 0 to 6
     *
     * @return int
     */
    public function getDaysOfWeek()
    {
        return $this->getData('days_of_week');
    }

    /**
     * Time from & to in minutes from 0 to 1440
     *
     * @return int
     */
    public function getTimeFrom()
    {
        return $this->getData('time_from');
    }

    /**
     * Time from & to in minutes from 0 to 1440
     *
     * @return int
     */
    public function getTimeTo()
    {
        return $this->getData('time_to');
    }

    /**
     * Is rule use time
     *
     * @return bool
     */
    public function getUseTime()
    {
        return $this->getData('use_time');
    }

    /**
     * Is time enabled in the rule
     *
     * @return bool
     */
    public function getTimeEnabled()
    {
        return $this->getData('time_enabled');
    }

    /**
     * Get rule unique key
     *
     * @return string
     */
    public function getUniqueKey()
    {
        return $this->getId();
    }

    /**
     * Get all logged data as array
     *
     * @param string $key
     * @return array
     */
    public function getLogData($key = '')
    {
        return $this->logData->getData($key);
    }

    /**
     * Save specific data to log
     *
     * @param string $key
     * @param mixed $value
     */
    public function log($key, $value)
    {
        $this->logData->setData($key, $value);
    }

    /**
     * Save attribute validation result to log
     *
     * @param string $attribute
     * @param boolean $validationResult
     */
    public function logConditions($attribute, $validationResult)
    {
        $data = $this->logData->getData('validation_result');
        if (empty($data)) {
            $data = [];
        }

        $data[] = [$attribute => $validationResult];
        $this->logData->setData('validation_result', $data);
    }

    /**
     * Get store specific error message
     *
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $rate
     * @param null $storeId
     * @return mixed
     */
    public function getStoreSpecificErrorMessage(Method $rate, $storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $storeSpecificErrorMessages = $this->getStoreErrmsgs();
        $errorMessage = $this->getData('error_message');
        if (!empty($storeSpecificErrorMessages[$storeId])) {
            $message = $storeSpecificErrorMessages[$storeId];
        } else {
            $message = $errorMessage;
        }

        $message = preg_replace('/{{carrier_title}}/ui', $rate->getCarrierTitle(), $message);
        $message = preg_replace('/{{method_title}}/ui', $rate->getMethodTitle(), $message);

        return $message;
    }

    /**
     * Flag: display or not error message for the disabled methods
     *
     * @return int
     */
    public function getDisplayErrorMessage()
    {
        return $this->getData('display_error_message');
    }

    /**
     * Get error message with a {{carrier_title}} and {{method_title}} variables in the body
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->getData('error_message');
    }

    /**
     * Get store specific error messages with a {{carrier_title}} and {{method_title}} variables in the bodies
     *
     * @return array
     */
    public function getStoreErrmsgs()
    {
        return $this->getData('store_errmsgs');
    }

    /**
     * Get changed titles of shipping methods
     *
     * @return array
     */
    public function getChangedTitles()
    {
        return $this->getData('changed_titles');
    }
}
