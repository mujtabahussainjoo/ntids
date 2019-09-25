<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\Carrier;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ShippingRules\Api\Data\MethodInterface;
use MageWorx\ShippingRules\Api\MethodEntityInterface;
use MageWorx\ShippingRules\Api\ImportExportEntity;
use MageWorx\ShippingRules\Model\ResourceModel\Method as MethodResourceModel;
use MageWorx\ShippingRules\Model\ResourceModel\Rate\Collection as RatesCollection;
use Magento\Framework\App\State;

/**
 * Class Method
 *
 * @method mixed[]|null getRates()
 * @method Method setRates(mixed[] $rates)
 * @method bool hasStoreLabels()
 * @method MethodResourceModel _getResource()
 * @method bool hasEdtStoreSpecificMessages()
 * @method boolean hasStoreIds()
 */
class Method extends AbstractModel implements MethodInterface, MethodEntityInterface, ImportExportEntity
{
    const CURRENT_METHOD = 'current_method';

    /**
     * Columns which will be ignored during import/export process
     * @see \MageWorx\ShippingRules\Model\Carrier\AbstractModel::getIgnoredColumnsForImportExport()
     */
    const IMPORT_EXPORT_IGNORE_COLUMNS = [
        'entity_id',
        'created_at',
        'updated_at',
        'carrier_id'
    ];

    /**
     * @var RatesCollection
     */
    protected $ratesCollection;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MageWorx\ShippingRules\Model\ResourceModel\Method');
        $this->setIdFieldName('entity_id');
    }

    /**
     * Validate model data
     *
     * @param DataObject $dataObject
     * @return bool|mixed[]
     */
    public function validateData(DataObject $dataObject)
    {
        $errors = [];

        if (!$dataObject->getData('code')) {
            $errors[] = __('Method code is required');
        }

        if (!$dataObject->getData('title')) {
            $errors[] = __('Title is required');
        }

        if ($dataObject->getData('price') < 0) {
            $errors[] = __('Price could not be a negative number');
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    /**
     * Get Method EDT message by specified store
     *
     * @param \Magento\Store\Model\Store|int|bool|null $store
     * @return string|bool
     */
    public function getEdtStoreSpecificMessage($store = null)
    {
        $storeId = $this->storeManager->getStore($store)->getId();
        $messages = (array)$this->getEdtStoreSpecificMessages();

        if (isset($messages[$storeId])) {
            return $messages[$storeId];
        } elseif (isset($messages[0]) && $messages[0]) {
            return $messages[0];
        }

        return false;
    }

    /**
     * Set if not yet and retrieve method store specific EDT messages
     *
     * @return mixed[]
     */
    public function getEdtStoreSpecificMessages()
    {
        if (!$this->hasEdtStoreSpecificMessages()) {
            $messages = $this->_getResource()->getEdtStoreSpecificMessages($this->getId());
            $this->setEdtStoreSpecificMessages($messages);
        }

        return $this->_getData('edt_store_specific_messages');
    }

    /**
     * Set if not yet and retrieve method store labels
     *
     * @return mixed[]
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }

        return $this->_getData('store_labels');
    }

    /**
     * Initialize method model data from array.
     * Set store labels if applicable.
     *
     * @param mixed[] $data
     * @return $this
     */
    public function loadPost(array $data)
    {
        if (isset($data['store_labels'])) {
            $this->setStoreLabels($data['store_labels']);
        }

        if (isset($data['edt_store_specific_messages'])) {
            $this->setEdtStoreSpecificMessages($data['edt_store_specific_messages']);
        }

        return $this;
    }

    /**
     * @return RatesCollection
     */
    public function getRatesCollection()
    {
        return $this->ratesCollection;
    }

    /**
     * @param RatesCollection $rates
     * @return $this
     */
    public function setRatesCollection(RatesCollection $rates)
    {
        $this->ratesCollection = $rates;

        return $this;
    }

    /**
     * Display or not the estimated delivery time message
     *
     * @return bool
     */
    public function isNeedToDisplayEstimatedDeliveryTime()
    {
        if ($this->getShowEstimatedDeliveryTime() && $this->getEstimatedDeliveryTimeMessage()) {
            return true;
        }

        return false;
    }

    /**
     * Returns formatted estimated delivery time message
     * string will be formatted as $prefix + message + $ending
     *
     * @param string $prefix
     * @param string $ending
     * @return string
     */
    public function getEstimatedDeliveryTimeMessageFormatted($prefix = '', $ending = '')
    {
        $message = $this->getEstimatedDeliveryTimeMessage();
        if (!$message) {
            return '';
        }

        $minValue = $this->getEstimatedDeliveryTimeMin();
        if ($this->getEstimatedDeliveryTimeMinByRate()) {
            $minValue = $this->getEstimatedDeliveryTimeMinByRate();
        }
        $maxValue = $this->getEstimatedDeliveryTimeMax();
        if ($this->getEstimatedDeliveryTimeMaxByRate()) {
            $maxValue = $this->getEstimatedDeliveryTimeMaxByRate();
        }

        if (!$minValue && !$maxValue) {
            return '';
        }

        $minDays = '';
        $maxDays = '';
        $minHours = '';
        $maxHours = '';

        $displayType = $this->getEstimatedDeliveryTimeDisplayType();
        switch ($displayType) {
            case MethodInterface::EDT_DISPLAY_TYPE_DAYS:
                $minDays = $this->parseDays($minValue);
                $maxDays = $this->parseDays($maxValue);
                break;
            case MethodInterface::EDT_DISPLAY_TYPE_HOURS:
                $minHours = $this->parseHours($minValue);
                $maxHours = $this->parseHours($maxValue);
                break;
            case MethodInterface::EDT_DISPLAY_TYPE_DAYS_AND_HOURS:
                $minDays = $this->parseDays(floor($minValue));
                $maxDays = $this->parseDays(floor($maxValue));
                $minHours = $this->parseHours($minValue - floor($minValue));
                $maxHours = $this->parseHours($maxValue - floor($maxValue));
                break;
            default:
                return '';
        }

        $message = preg_replace('/{{min_days}}/ui', $minDays, $message);
        $message = preg_replace('/{{max_days}}/ui', $maxDays, $message);
        $message = preg_replace('/{{min_hours}}/ui', $minHours, $message);
        $message = preg_replace('/{{max_hours}}/ui', $maxHours, $message);
        $message = $prefix. $message . $ending;

        return $message;
    }

    /**
     * Parse days from days (with round)
     *
     * @param $value
     * @return float
     */
    private function parseDays($value)
    {
        return round($value, 0, PHP_ROUND_HALF_UP);
    }

    /**
     * Parse hours from days
     *
     * @param $value
     * @return float
     */
    private function parseHours($value)
    {
        $value = (float)$value * 24;
        return round($value, 0, PHP_ROUND_HALF_UP);
    }

    /**
     * Prepare data before saving
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
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
     * Get associated store Ids
     *
     * @return mixed[]
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
     * Retrieve method title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Flag: is the title replacement allowed
     * In case it is allowed - the title from a most prior rate will be used
     * (in a case valid rate is exists)
     *
     * @return int
     */
    public function getReplaceableTitle()
    {
        return $this->getData('replaceable_title');
    }

    /**
     * Retrieve method code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData('code');
    }

    /**
     * Corresponding carrier id
     *
     * @return int
     */
    public function getCarrierId()
    {
        return $this->getData('carrier_id');
    }

    /**
     * Check is method active
     *
     * @return int|bool
     */
    public function getActive()
    {
        return $this->getData('active');
    }

    /**
     * Default method price
     *
     * @return float (12,2)
     */
    public function getPrice()
    {
        return $this->getData('price');
    }

    /**
     * Get Max price threshold
     *
     * @return float|null
     */
    public function getMaxPriceThreshold()
    {
        return $this->getData('max_price_threshold');
    }

    /**
     * Get Min price threshold
     *
     * @return float|null
     */
    public function getMinPriceThreshold()
    {
        return $this->getData('min_price_threshold');
    }

    /**
     * Default method cost
     *
     * @return float (12,2)
     */
    public function getCost()
    {
        return $this->getData('cost');
    }

    /**
     * Get created at date
     *
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    /**
     * Get last updated date
     *
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    /**
     * Check is we should disable this method when there are no valid rates
     *
     * @return int|bool
     */
    public function getDisabledWithoutValidRates()
    {
        return $this->getData('disabled_without_valid_rates');
    }

    /**
     * Multiple rates price calculation method
     * @see \MageWorx\ShippingRules\Model\Config\Source\MultipleRatesPrice::toOptionArray()
     *
     * @return int
     */
    public function getMultipleRatesPrice()
    {
        return $this->getData('multiple_rates_price');
    }

    /**
     * Is free shipping triggered by a third party extension allowed (like sales rule)
     *
     * @return int
     */
    public function getAllowFreeShipping()
    {
        return $this->getData('allow_free_shipping');
    }

    /**
     * Min estimated delivery time (can be overwritten by a value form a rate, visible at checkout & cart)
     *
     * @return float
     */
    public function getEstimatedDeliveryTimeMin()
    {
        $value = $this->getData('estimated_delivery_time_min') ? $this->getData('estimated_delivery_time_min') : null;

        return (float)$value;
    }

    /**
     * Max estimated delivery time (can be overwritten by a value form a rate, visible at checkout & cart)
     *
     * @return float
     */
    public function getEstimatedDeliveryTimeMax()
    {
        $value = $this->getData('estimated_delivery_time_max') ? $this->getData('estimated_delivery_time_max') : null;

        return (float)$value;
    }

    /**
     * Flag: is replacing of the estimated delivery time allowed (from a valid rates)
     *
     * @return int
     */
    public function getReplaceableEstimatedDeliveryTime()
    {
        return $this->getData('replaceable_estimated_delivery_time');
    }

    /**
     * How an estimated delivery time values would be visible for the customer?
     *
     * Possible values:
     * DAYS (rounded) - MethodInterface::EDT_DISPLAY_TYPE_DAYS
     * HOURS - MethodInterface::EDT_DISPLAY_TYPE_HOURS
     * DAYS & HOURS - MethodInterface::EDT_DISPLAY_TYPE_DAYS_AND_HOURS
     *
     * @return int
     */
    public function getEstimatedDeliveryTimeDisplayType()
    {
        return $this->getData('estimated_delivery_time_display_type');
    }

    /**
     * Flag: should be the Estimated Delivery Time displayed for the customer or not
     *
     * @return int
     */
    public function getShowEstimatedDeliveryTime()
    {
        return $this->getData('show_estimated_delivery_time');
    }

    /**
     * Markup for the EDT message.
     * You can use variables {{min}} {{max}} which will be replaced by a script to the corresponding values
     * from a method or rate.
     *
     * {{min}} - MethodInterface::EDT_PLACEHOLDER_MIN
     * {{max}} - MethodInterface::EDT_PLACEHOLDER_MAX
     *
     * @return string
     */
    public function getEstimatedDeliveryTimeMessage()
    {
        return $this->getData('estimated_delivery_time_message');
    }

    /**
     * Get min estimated delivery time by rate (overwritten default value)
     *
     * @return float
     */
    public function getEstimatedDeliveryTimeMinByRate()
    {
        return $this->getData('estimated_delivery_time_min_by_rate');
    }

    /**
     * Get max estimated delivery time by rate (overwritten default value)
     *
     * @return float
     */
    public function getEstimatedDeliveryTimeMaxByRate()
    {
        return $this->getData('estimated_delivery_time_max_by_rate');
    }

    /**
     * Set min estimated delivery time by rate (overwrite default value)
     *
     * @param float $value
     * @return $this
     */
    public function setEstimatedDeliveryTimeMinByRate($value)
    {
        return $this->setData('estimated_delivery_time_min_by_rate', $value);
    }

    /**
     * Set max estimated delivery time by rate (overwrite default value)
     *
     * @param float $value
     * @return $this
     */
    public function setEstimatedDeliveryTimeMaxByRate($value)
    {
        return $this->setData('estimated_delivery_time_max_by_rate', $value);
    }

    /**
     * Get corresponding carrier code (relation)
     *
     * @return string
     */
    public function getCarrierCode()
    {
        return $this->getData('carrier_code');
    }

    /**
     * Set corresponding carrier code
     *
     * @param string $code
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setCarrierCode($code)
    {
        return $this->setData('carrier_code', $code);
    }

    /**
     * Set corresponding carrier id
     *
     * @param int $id
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setCarrierId($id)
    {
        return $this->setData('carrier_id', $id);
    }

    /**
     * Flag: is the title replacement allowed
     * In case it is allowed - the title from a most prior rate will be used
     * (in a case valid rate is exists)
     *
     * @param bool $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setReplaceableTitle($value)
    {
        return $this->setData('replaceable_title', $value);
    }

    /**
     * Set method code
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setCode($value)
    {
        return $this->setData('code', $value);
    }

    /**
     * Set is method active
     *
     * @param bool $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setActive($value)
    {
        return $this->setData('active', $value);
    }

    /**
     * Set default method price
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setPrice($value)
    {
        return $this->setData('price', $value);
    }

    /**
     * Set Default method cost
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setCost($value)
    {
        return $this->setData('cost', $value);
    }

    /**
     * Set created at date
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setCreatedAt($value)
    {
        return $this->setData('created_at', $value);
    }

    /**
     * Set last updated date
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setUpdatedAt($value)
    {
        return $this->setData('updated_at', $value);
    }

    /**
     * Set Min estimated delivery time (can be overwritten by a value form a rate, visible at checkout & cart)
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setEstimatedDeliveryTimeMin($value)
    {
        return $this->setData('estimated_delivery_time_min', $value);
    }

    /**
     * Set Max estimated delivery time (can be overwritten by a value form a rate, visible at checkout & cart)
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setEstimatedDeliveryTimeMax($value)
    {
        return $this->setData('estimated_delivery_time_max', $value);
    }

    /**
     * Set Flag: is replacing of the estimated delivery time allowed (from a valid rates)
     *
     * @param int $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setReplaceableEstimatedDeliveryTime($value)
    {
        return $this->setData('replaceable_estimated_delivery_time', $value);
    }

    /**
     * How an estimated delivery time values would be visible for the customer?
     *
     * Possible values:
     * DAYS (rounded) - MethodInterface::EDT_DISPLAY_TYPE_DAYS
     * HOURS - MethodInterface::EDT_DISPLAY_TYPE_HOURS
     * DAYS & HOURS - MethodInterface::EDT_DISPLAY_TYPE_DAYS_AND_HOURS
     *
     * @param int $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setEstimatedDeliveryTimeDisplayType($value)
    {
        return $this->setData('estimated_delivery_time_display_type', $value);
    }

    /**
     * Set Flag: should be the Estimated Delivery Time displayed for the customer or not
     *
     * @param int $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setShowEstimatedDeliveryTime($value)
    {
        return $this->setData('show_estimated_delivery_time', $value);
    }

    /**
     * Set method title
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setTitle($value)
    {
        return $this->setData('title', $value);
    }

    /**
     * Set Max price threshold
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setMaxPriceThreshold($value)
    {
        return $this->setData('max_price_threshold', $value);
    }

    /**
     * Set Min price threshold
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setMinPriceThreshold($value)
    {
        return $this->setData('min_price_threshold', $value);
    }

    /**
     * Set is we should disable this method when there are no valid rates
     *
     * @param bool $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setDisabledWithoutValidRates($value)
    {
        return $this->setData('disabled_without_valid_rates', $value);
    }

    /**
     * Set Multiple rates price calculation method
     * @see \MageWorx\ShippingRules\Model\Config\Source\MultipleRatesPrice::toOptionArray($value)
     *
     * @param int $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setMultipleRatesPrice($value)
    {
        return $this->setData('multiple_rates_price', $value);
    }

    /**
     * Is free shipping by a third party extension allowed (like sales rule)
     *
     * @param bool $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setAllowFreeShipping($value)
    {
        return $this->setData('allow_free_shipping', $value);
    }

    /**
     * Set estimated Delivery Time Message
     *
     * Markup for the EDT message.
     * You can use variables {{min}} {{max}} which will be replaced by a script to the corresponding values
     * from a method or rate.
     *
     * {{min}} - MethodInterface::EDT_PLACEHOLDER_MIN
     * {{max}} - MethodInterface::EDT_PLACEHOLDER_MAX
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setEstimatedDeliveryTimeMessage($value)
    {
        return $this->setData('estimated_delivery_time_message', $value);
    }

    /**
     * Set associated store Ids
     *
     * @param mixed[] $value
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setStoreIds($value)
    {
        return $this->setData('store_ids', $value);
    }

    /**
     * Set store specific labels (title)
     *
     * @param mixed[] $storeLabels
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setStoreLabels($storeLabels = [])
    {
        return $this->setData('store_labels', $storeLabels);
    }

    /**
     * Set Store Specific Estimated Delivery Time Messages
     *
     * @param mixed[] $messages
     * @return \MageWorx\ShippingRules\Api\Data\MethodInterface
     */
    public function setEdtStoreSpecificMessages($messages = [])
    {
        return $this->setData('edt_store_specific_messages', $messages);
    }
}
