<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Rule\Condition;

use Magento\Framework\Model\AbstractModel;
use MageWorx\ShippingRules\Model\Rule;

/**
 * Class Address
 *
 * @method string getAttribute()
 * @method array getAttributeOption()
 * @method Address setInputType($string)
 * @method Address setOperator($string)
 * @method Address setValue($string)
 * @method Rule getRule()
 */
class Address extends \MageWorx\ShippingRules\Model\Condition\AbstractAddress
{
    /**
     * @var \MageWorx\ShippingRules\Model\ResourceModel\Zone\CollectionFactory
     */
    protected $zoneCollectionFactory;

    /**
     * @var \Magento\Webapi\Controller\Rest\InputParamsResolver
     */
    protected $inputParamsResolver;

    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \MageWorx\ShippingRules\Model\Config\Source\Locale\Country
     */
    protected $sourceCountry;

    /**
     * @param \MageWorx\ShippingRules\Helper\Data $helper
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Directory\Model\Config\Source\Country $directoryCountry
     * @param \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion
     * @param \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods
     * @param \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods
     * @param \MageWorx\ShippingRules\Model\ResourceModel\Zone\CollectionFactory $zoneCollectionFactory
     * @param \Magento\Webapi\Controller\Rest\InputParamsResolver $inputParamsResolver
     * @param \Magento\Quote\Model\Quote\AddressFactory $addressFactory
     * @param array $data
     */
    public function __construct(
        \MageWorx\ShippingRules\Helper\Data $helper,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Directory\Model\Config\Source\Country $directoryCountry,
        \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods,
        \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods,
        \MageWorx\ShippingRules\Model\ResourceModel\Zone\CollectionFactory $zoneCollectionFactory,
        \Magento\Webapi\Controller\Rest\InputParamsResolver $inputParamsResolver,
        \Magento\Quote\Model\Quote\AddressFactory $addressFactory,
        \MageWorx\ShippingRules\Model\Config\Source\Locale\Country $sourceCountry,
        array $data = []
    ) {
        parent::__construct(
            $helper,
            $context,
            $directoryCountry,
            $directoryAllregion,
            $shippingAllmethods,
            $paymentAllmethods,
            $data
        );
        $this->zoneCollectionFactory = $zoneCollectionFactory;
        $this->inputParamsResolver = $inputParamsResolver;
        $this->addressFactory = $addressFactory;
        $this->sourceCountry = $sourceCountry;
    }
    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'base_subtotal' => __('Subtotal (base)'),
            'base_subtotal_after_discount' => __('Subtotal with Discount (base)'),
            'base_discount_amount_for_validation' => __('Discount (base)'),
            'total_qty' => __('Total Items Quantity'),
            'weight' => __('Total Weight'),
            'coupon_code' => __('Coupon Code'),
            'postcode' => __('Shipping Postcode'),
            'region' => __('Shipping Region'),
            'region_id' => __('Shipping State/Province'),
            'country_id' => __('Shipping Country'),
            'street' => __('Shipping Street'),
            'city' => __('Shipping City'),
            'zone_id' => __('Location Group (Shipping Zone)'),
        ];

        if ($this->helper->isUKSpecificPostcodeConditionsEnabled()) {
            foreach (static::UK_POST_CODE_ATTRIBUTES as $value) {
                $attributes[$value] = ucfirst(str_ireplace('uk_', '', $value)) . ' (UK)';
            }
        }

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal':
            case 'base_subtotal_after_discount':
            case 'weight':
            case 'total_qty':
            case 'base_discount_amount_for_validation':
                return 'numeric';
            case 'country_id':
            case 'region_id':
                return 'select';
            case 'zone_id':
                return 'multiselect';
        }
        return 'string';
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'country_id':
            case 'region_id':
                return 'select';
            case 'zone_id':
                return 'multiselect';
        }

        return 'text';
    }

    /**
     * Get value select options
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = $this->getCountryOptionsByConfig();
                    break;

                case 'region_id':
                    $options = $this->_directoryAllregion->toOptionArray();
                    break;

                case 'zone_id':
                    /** @var \MageWorx\ShippingRules\Model\ResourceModel\Zone\Collection $zonesCollection */
                    $zonesCollection = $this->zoneCollectionFactory->create();
                    $options = $zonesCollection->toOptionArray();
                    break;

                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * @return array|string
     */
    public function getValue()
    {
        return parent::getValue();
    }

    /**
     * Get countries as an option array based on modules settings
     *
     * @return array
     */
    private function getCountryOptionsByConfig()
    {
        if ($this->helper->isExtendedCountrySelectEnabled()) {
            $options = $this->sourceCountry->toOptionArray();
        } else {
            $options = $this->_directoryCountry->toOptionArray();
        }

        return $options;
    }

    /**
     * Validate Address Rule Condition
     *
     * @param AbstractModel|\Magento\Quote\Model\Quote\Address $model
     * @return bool
     */
    public function validate(AbstractModel $model)
    {
        /** @var \Magento\Quote\Model\Quote\Address|AbstractModel $address */
        $address = $model;
        if (!$address instanceof \Magento\Quote\Model\Quote\Address) {
            if ($model->getQuote()->isVirtual()) {
                $address = $model->getQuote()->getBillingAddress();
            } else {
                $address = $model->getQuote()->getShippingAddress();
            }
        }

        if ('payment_method' == $this->getAttribute() && !$address->hasPaymentMethod()) {
            $address->setPaymentMethod($model->getQuote()->getPayment()->getMethod());
        }

        if ('base_subtotal_after_discount' == $this->getAttribute() && !$address->hasData($this->getAttribute())) {
            $baseSubtotalAfterDiscount = $this->calculateBaseSubtotalAfterDiscount($address);
            $address->setData('base_subtotal_after_discount', $baseSubtotalAfterDiscount);
        }

        if ($this->getAttribute() == 'zone_id' && !$address->hasData('zone_id')) {
            $this->addZoneToAddress($address);
        }

        /**
         * @important When API is used some of parameters could be found in the request but not in the address
         * from checkout session.
         */
        if ($this->helper->isNeedToResolveParametersFromApiRequest()) {
            $this->resolveParametersFromApiRequest($address);
        }

        if ($this->getAttribute() == 'coupon_code' && !$address->hasData('coupon_code')) {
            $address->setData('coupon_code', $address->getQuote()->getCouponCode());
        }

        if ($this->getAttribute() == 'base_discount_amount_for_validation' &&
            $address->getData('base_discount_amount') !== null
        ) {
            if ($address->getData('base_discount_amount') < 0) {
                $address->setData(
                    'base_discount_amount_for_validation',
                    (float)$address->getData('base_discount_amount') * -1
                );
            } else {
                $address->setData(
                    'base_discount_amount_for_validation',
                    $address->getData('base_discount_amount')
                );
            }
        }

        if ('total_qty' == $this->getAttribute() && !$address->hasTotalQty()) {
            $address->setTotalQty($address->getItemQty());
        }

        $this->addUKPostCodeParts($address);

        /**
         * Prevent uncontrolled load of the address in the
         * @see \Magento\Rule\Model\Condition\AbstractCondition::validate
         */
        if (!$address->hasData($this->getAttribute())) {
            /** @var \Magento\Quote\Model\Quote\Address $addressLoaded */
            $addressLoaded = $this->addressFactory->create();
            $addressLoaded->getResource()->load($addressLoaded, $address->getId());
            $address->setData($this->getAttribute(), $addressLoaded->getData($this->getAttribute()));
        }

        $attributeValue = $address->getData($this->getAttribute());

        if ($this->getAttribute() == 'country_id' && $this->helper->isExtendedCountrySelectEnabled()) {
            $this->prepareCountryId();
        }

        // Ignore parent actions
        $result = $this->validateAttribute($attributeValue);

        /** @var Rule $rule */
        $rule = $this->getRule();
        if ($rule instanceof Rule) {
            $rule->logConditions($this->getAttribute(), $result);
        }

        return $result;
    }

    /**
     * Prepare country id for validation when extended country select used
     *
     * @return void
     */
    private function prepareCountryId()
    {
        $operator = $this->getOperator();
        $inputType = $this->getInputType();
        $value = $this->getValue();

        if ($inputType != 'select') {
            return;
        }

        if (strtolower($value) == 'eu') {
            $countries = $this->helper->getEuCountries();
        } elseif (preg_match('/^\d{0,3}$/', $value)) {
            $countries = $this->helper->resolveCountriesByDigitCode($value);
        } else {
            return;
        }

        $this->setInputType('multiselect');
        $this->setValue($countries);

        switch ($operator) {
            case '==':
                $this->setOperator('()');
                break;
            case '!=':
                $this->setOperator('!()');
                break;
            default:
                return;
        }

        return;
    }

    /**
     * Resolve additional parameters which exists in api request address
     * but not saved in address inside quote.
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return void
     */
    private function resolveParametersFromApiRequest(\Magento\Quote\Model\Quote\Address $address)
    {
        /**
         * Do nothing during a payment information saving.
         *
         * Actual methods where it's needed:
         *  - estimateByExtendedAddress
         *  - saveAddressInformation
         *
         * This method can cause a bug when the shipping address filled with a billing address information
         * and the it's saved somewhere in the core methods
         */
        /** @var \Magento\Webapi\Controller\Rest\Router\Route $route */
        $route = $this->inputParamsResolver->getRoute();
        if ($route && $route->getServiceMethod() == 'savePaymentInformationAndPlaceOrder') {
            return;
        }

        /** @var \Magento\Quote\Model\Quote\Address[] $addressesFound */
        $addressesFound = [];
        try {
            $inputParams = $this->inputParamsResolver->resolve();
            if (empty($inputParams)) {
                return;
            }
            foreach ($inputParams as $param) {
                if ($param instanceof \Magento\Quote\Api\Data\AddressInterface) {
                    $addressesFound[] = $param;
                }
            }
            if (!count($addressesFound)) {
                return;
            }

            $priorAddress = $addressesFound[0];
            foreach ($addressesFound as $addressFound) {
                if ($addressFound->getAddressType() === \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING) {
                    $priorAddress = $addressFound;
                    break;
                }
            }
            $address->addData($priorAddress->getData());

            return;
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return float
     */
    protected function calculateBaseSubtotalAfterDiscount(\Magento\Quote\Model\Quote\Address $address)
    {
        $baseSubtotalAfterDiscount = $address->getBaseSubtotalWithDiscount();

        return $baseSubtotalAfterDiscount;
    }

    /**
     * Detect valid zone for the address
     * Store valid zone id in address data
     * Available one most prioritized zone mode and all available valid zones as an array.
     * Settings could be changed in the store configuration
     * @see \MageWorx\ShippingRules\Helper\Data::isSingleAddressZoneMode()
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return void
     */
    protected function addZoneToAddress($address)
    {
        if ($this->helper->isSingleAddressZoneMode()) {
            $this->addSinglePrioritizedZoneToAddress($address);
        } else {
            $this->addFewZonesToAddress($address);
        }
    }

    /**
     * Add one and only one most prioritized zone to the customers address
     * Settings could be changed in the store configuration
     * @see \MageWorx\ShippingRules\Helper\Data::isSingleAddressZoneMode()
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     */
    private function addSinglePrioritizedZoneToAddress(\Magento\Quote\Model\Quote\Address $address)
    {
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Zone\Collection $zoneCollection */
        $zoneCollection = $this->zoneCollectionFactory->create();
        $zoneCollection->addStoreFilter($address->getQuote()->getStore()->getId())
            ->addIsActiveFilter()
            ->setOrder('priority', \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC);

        /** @var \MageWorx\ShippingRules\Model\Zone $zone */
        foreach ($zoneCollection as $zone) {
            if ($zone->validate($address)) {
                $address->setData('zone_id', $zone->getId());
            }
        }
    }

    /**
     * Add all available zones to the address (as an array) and remove invalid zones, if exists
     * Settings could be changed in the store configuration
     * @see \MageWorx\ShippingRules\Helper\Data::isSingleAddressZoneMode()
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     */
    private function addFewZonesToAddress(\Magento\Quote\Model\Quote\Address $address)
    {
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Zone\Collection $zoneCollection */
        $zoneCollection = $this->zoneCollectionFactory->create();
        $zoneCollection->addStoreFilter($address->getQuote()->getStore()->getId())
            ->addIsActiveFilter();

        foreach ($zoneCollection as $zone) {
            if ($zone->validate($address)) {
                // Add valid zone to the zones stack
                $zones = $this->getAddressZonesArray($address);
                $key = array_search($zone->getId(), $zones);
                if ($key === false) {
                    $zones[] = $zone->getId();
                }
                $address->setData('zone_id', $zones);
            } else {
                // Remove invalid zone from address if exists
                $zones = $this->getAddressZonesArray($address);
                $key = array_search($zone->getId(), $zones);
                if ($key !== false) {
                    unset($zones[$key]);
                    $address->setData('zone_id', $zones);
                }
            }
        }
    }

    /**
     * Get array of address zones
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return array|mixed
     */
    private function getAddressZonesArray(\Magento\Quote\Model\Quote\Address $address)
    {
        $zones = $address->getData('zone_id');
        if (!is_array($zones)) {
            $zones = explode(',', $zones);
        }

        return $zones;
    }

    /**
     * Retrieve operator for php validation
     *
     * @return string
     */
    public function getOperatorForValidate()
    {
        $operator = $this->getOperator();
        if ($this->getAttribute() == 'zone_id' &&
            !in_array($operator, $this->getOperatorInputByType('multiselect'))
        ) {
            switch ($operator) {
                case '==':
                    return '()';
                case '!=':
                    return '!()';
                default:
                    return $operator;
            }
        }

        return $operator;
    }

    /**
     * Get type specific operators
     *
     * @param $type
     * @return array
     */
    private function getOperatorInputByType($type)
    {
        $operators = $this->getDefaultOperatorInputByType();

        return isset($operators[$type]) ? $operators[$type] : [];
    }
}
