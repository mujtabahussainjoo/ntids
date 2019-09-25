<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\Carrier\Method;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use MageWorx\ShippingRules\Api\Data\RateInterface;
use MageWorx\ShippingRules\Api\ImportExportEntity;
use Magento\Framework\DataObject;
use MageWorx\ShippingRules\Model\Carrier\AbstractModel;
use Magento\Quote\Model\Quote\Address\RateRequest;
use MageWorx\ShippingRules\Model\Config\Source\Locale\Country;
use Magento\Directory\Model\RegionFactory;
use MageWorx\ShippingRules\Model\ResourceModel\Rate as RateResource;
use Magento\Framework\App\State;

/**
 * Class Rate
 *
 * @method bool hasStoreLabels()
 * @method RateResource _getResource()
 * @method boolean hasStoreIds()
 *
 */
class Rate extends AbstractModel implements RateInterface, ImportExportEntity
{
    const CURRENT_RATE = 'current_rate';

    const PRICE_CALCULATION_OVERWRITE = 0;
    const PRICE_CALCULATION_SUM = 1;

    const MULTIPLE_RATES_PRICE_CALCULATION_MAX_PRIORITY = 0;
    const MULTIPLE_RATES_PRICE_CALCULATION_MAX_PRICE = 1;
    const MULTIPLE_RATES_PRICE_CALCULATION_MIN_PRICE = 2;
    const MULTIPLE_RATES_PRICE_CALCULATION_SUM_UP = 3;

    const DELIMITER = ',';
    const MAX_ZIP = 9999999999;
    const MIN_ZIP = 0;

    /**
     * Columns which will be ignored during import/export process
     * @see \MageWorx\ShippingRules\Model\Carrier\AbstractModel::getIgnoredColumnsForImportExport()
     */
    const IMPORT_EXPORT_IGNORE_COLUMNS = [
        'created_at',
        'updated_at',
        'rate_id',
        'method_id'
    ];

    /**
     * @var array
     */
    protected $preparedCountryIds = [];

    /**
     * @var bool
     */
    protected $preparedCountryIdsFlag = false;

    /**
     * @var \MageWorx\ShippingRules\Helper\Data
     */
    protected $helper;

    /**
     * @var Country
     */
    protected $countryList;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var CountryInformationAcquirerInterface
     */
    protected $countryInformationAcquirer;

    /**
     * @var bool
     */
    protected $methodPriceWasAdded = false;

    /**
     * @var array
     */
    protected $regionsAsArray = [];

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\ShippingRules\Helper\Data $helper
     * @param Country $countryList
     * @param RegionFactory $regionFactory
     * @param CountryInformationAcquirerInterface $countryInformationAcquirer
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\ShippingRules\Helper\Data $helper,
        Country $countryList,
        RegionFactory $regionFactory,
        CountryInformationAcquirerInterface $countryInformationAcquirer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
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
        $this->helper = $helper;
        $this->countryList = $countryList;
        $this->regionFactory = $regionFactory;
        $this->countryInformationAcquirer = $countryInformationAcquirer;
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MageWorx\ShippingRules\Model\ResourceModel\Rate');
        $this->setIdFieldName('rate_id');
    }

    /**
     * Processing object before save data
     *
     * @return $this
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

        return parent::beforeSave();
    }

    /**
     * Set if not yet and retrieve method store labels
     *
     * @return array
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
     * Validate model data
     *
     * @param DataObject $dataObject
     * @return bool|array
     */
    public function validateData(DataObject $dataObject)
    {
        $errors = [];

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $method
     * @param RateRequest $request
     * @param \MageWorx\ShippingRules\Model\Carrier\Method $methodData
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    public function applyRateToMethod(
        \Magento\Quote\Model\Quote\Address\RateResult\Method $method,
        RateRequest $request,
        \MageWorx\ShippingRules\Model\Carrier\Method $methodData
    ) {
    
        $result = $this->getCalculatedPrice($request, $methodData);
        // Sum up rate prices
        if ($methodData->getMultipleRatesPrice() === Rate::MULTIPLE_RATES_PRICE_CALCULATION_SUM_UP) {
            $result += (float)$method->getPrice();
        }

        if ($methodData->getMaxPriceThreshold() !== null &&
            $methodData->getMaxPriceThreshold() > 0 &&
            $result > $methodData->getMaxPriceThreshold()
        ) {
            $method->setPrice($methodData->getMaxPriceThreshold());
        } elseif ($methodData->getMinPriceThreshold() !== null &&
            $result < $methodData->getMinPriceThreshold() &&
            $methodData->getMinPriceThreshold() > 0
        ) {
            $method->setPrice($methodData->getMinPriceThreshold());
        } else {
            $method->setPrice($result);
        }

        // Change method title (if it is allowed by a method config)
        if ($methodData->getReplaceableTitle()) {
            if ($this->getStoreLabel()) {
                $method->setMethodTitle($this->getStoreLabel());
            } elseif ($this->getTitle()) {
                $method->setMethodTitle($this->getTitle());
            }
        }

        // Change Estimated Delivery time
        if ($methodData->isNeedToDisplayEstimatedDeliveryTime() && $methodData->getReplaceableEstimatedDeliveryTime()) {
            $methodData->setEstimatedDeliveryTimeMinByRate($this->getEstimatedDeliveryTimeMin());
            $methodData->setEstimatedDeliveryTimeMaxByRate($this->getEstimatedDeliveryTimeMax());
        }

        return $method;
    }

    /**
     * Get calculated rate's price
     *
     * @param RateRequest $request
     * @param \MageWorx\ShippingRules\Model\Carrier\Method $methodData
     * @return mixed|number
     */
    public function getCalculatedPrice(
        RateRequest $request,
        \MageWorx\ShippingRules\Model\Carrier\Method $methodData
    ) {
    
        $requestItemsCount = 0;
        $requestProductsCount = 0;
        foreach ($request->getAllItems() as $requestItem) {
            if ($requestItem->getParentItemId()) {
                continue;
            }
            $requestItemsCount += 1;
            $requestProductsCount += (float)$requestItem->getQty();
        }
        $requestItemsCost = $this->calculateItemsTotalPrice($request->getAllItems());

        $price['base_price'] = $this->getPrice();
        $price['per_product'] = $requestProductsCount * $this->getPricePerProduct();
        $price['per_item'] = $requestItemsCount * $this->getPricePerItem();
        $price['percent_per_product'] = $requestProductsCount * $this->getPricePercentPerProduct() / 100;
        $price['percent_per_item'] = $requestItemsCount * $this->getPricePercentPerItem() / 100;
        $price['item_price_percent'] = $requestItemsCost * $this->getItemPricePercent() / 100;
        $price['per_weight'] = $request->getPackageWeight() * $this->getPricePerWeight();

        $result = array_sum($price);
        // Method price could be added only once
        if ($this->getRateMethodPrice() == self::PRICE_CALCULATION_SUM && !$this->methodPriceWasAdded) {
            $this->methodPriceWasAdded = true;
            $result += (float)$methodData->getData('price');
        }

        return $result;
    }

    /**
     * @param $items
     * @return float
     */
    public function calculateItemsTotalPrice($items)
    {
        $totalPrice = 0.0;
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            $totalPrice += (float)$item->getBaseRowTotal(); // @TODO: with tax? without discount?
            // @TODO: possible add settings to the module config
        }

        return $totalPrice;
    }

    /**
     * @param RateRequest $request
     * @return bool
     */
    public function validateRequest(RateRequest $request)
    {
        // Not active rates are invalid
        if (!$this->getActive()) {
            return false;
        }

        $this->getResource()->unserializeFields($this);

        // Validate country
        if (!$this->validateRequestByCountryId($request)) {
            return false;
        }

        // Validate region
        if (!$this->validateRequestByRegion($request)) {
            return false;
        }

        if (!$this->validateRequestByZipCode($request)) {
            return false;
        }

        if (!$this->validateRequestByPrice($request)) {
            return false;
        }

        if (!$this->validateRequestByQty($request)) {
            return false;
        }

        if (!$this->validateRequestByWeight($request)) {
            return false;
        }

        return true;
    }

    /**
     * Get region codes as array (for the current rate)
     *
     * @return array
     */
    public function getRegionAsArray()
    {
        if (!empty($this->regionsAsArray) && is_array($this->regionsAsArray)) {
            return $this->regionsAsArray;
        }

        $regionCodesRaw = $this->getRegion();
        if (is_array($regionCodesRaw)) {
            $this->regionsAsArray = $regionCodesRaw;

            return $this->regionsAsArray;
        }

        if (!$regionCodesRaw) {
            $this->regionsAsArray = [];

            return $this->regionsAsArray;
        }

        $regionCodes = explode(static::DELIMITER, $regionCodesRaw);
        $regionCodes = array_map('trim', $regionCodes);
        $regionCodes = array_filter($regionCodes);

        $this->regionsAsArray = $regionCodes;

        return $this->regionsAsArray;
    }

    /**
     * Validate request by region or region id
     *
     * @param $request
     * @return bool
     */
    public function validateRequestByRegion($request)
    {
        // @TODO refactoring needed, unclear code, to hard to understand
        /** @var array $regionId */
        $regionId = $this->getRegionId();
        $regionCodes = $this->getRegionAsArray();
        $destinationRegionCode = trim($request->getDestRegionCode());
        $destinationRegionId = $request->getDestRegionId();
        if (!empty($regionId) &&
            $destinationRegionId &&
            !in_array($destinationRegionId, $regionId)
        ) {
            // Validate by destination region id (region from select): invalid result
            return false;
        } elseif (!$destinationRegionId &&
            !empty($regionCodes) &&
            $destinationRegionCode &&
            !in_array($destinationRegionCode, $regionCodes)
        ) {
            // Validate by destination region code (region name from text input): invalid result
            return false;
        } elseif (!$destinationRegionId &&
            !$destinationRegionCode &&
            (!empty($regionId) || !empty($regionCodes))
        ) {
            // Not valid if the region required by the rate and is not set yet by customer
            return false;
        }
        // @TODO end

        return true;
    }

    /**
     * @param RateRequest $request
     * @return bool
     */
    public function validateRequestByCountryId(RateRequest $request)
    {
        $destinationCountryId = $request->getDestCountryId();
        $rateCountryIds = $this->getRateCountryIdAsArray();

        if (!$this->getCountryId()) {
            return true;
        }

        if (in_array(Country::CODE_WORLD, $rateCountryIds)) {
            return true;
        }

        if (!in_array($destinationCountryId, $rateCountryIds)) {
            return false;
        }

        return true;
    }

    /**
     * Get countries ids as an array based on selection:
     * converts geo regions (as Africa, EU, etc.) to real countries
     *
     * @return array|string
     */
    protected function getRateCountryIdAsArray()
    {
        if ($this->preparedCountryIdsFlag) {
            return $this->preparedCountryIds;
        }

        if (!is_array($this->getCountryId())) {
            $countries = [$this->getCountryId()];
        } else {
            $countries = $this->getCountryId();
        }

        $resultCountries = [];
        foreach ($countries as $country) {
            // Check not real countries in the array and merge them
            if (strtolower($country) == 'eu') {
                $prePreparedCountryIds = $this->helper->getEuCountries();
            } elseif (preg_match('/^\d{0,3}$/', $country)) {
                $prePreparedCountryIds = $this->helper->resolveCountriesByDigitCode($country);
            } else {
                $prePreparedCountryIds = [$country];
            }
            $resultCountries = array_merge($resultCountries, $prePreparedCountryIds);
        }

        $this->preparedCountryIds = array_unique($resultCountries);
        $this->preparedCountryIdsFlag = true;

        return $this->preparedCountryIds;
    }

    /**
     * @param RateRequest $request
     *
     * @return bool
     */
    public function validateRequestByZipCode(RateRequest $request)
    {
        if (empty($this->getZipFrom()) && empty($this->getZipTo())) {
            return true;
        }

        $requestZip = mb_strtoupper($request->getDestPostcode());
        if (!$requestZip) {
            // In case zip is not set yet the rate will be invalid
            // @TODO: add setting?
            return false;
        }

        $zipsFrom = $this->getZipFrom();
        $zipsTo = $this->getZipTo();
        // For a list of zip codes without any diapason
        if (!empty($zipsFrom) && empty($zipsTo)) {
            $ninCount = 0;
            // Validate list of zips one by one
            foreach ($zipsFrom as $zipFrom) {
                // Check condition state: IN or NOT IN (Does a leading "!" found?)
                if (mb_substr($zipFrom, 0, 1) == '!') {
                    // Remove special symbol
                    $zipFrom = mb_substr($zipFrom, 1);
                    $nin = true;
                    $ninCount++;
                } else {
                    $nin = false;
                }

                // Validation itself:
                if ($requestZip == $zipFrom) {
                    // In case when zips are matching...
                    if ($nin === false) {
                        // ... and we are searching for a zip which IN
                        return true;
                    } else {
                        // ... and we are searching for a zip which NOT IN
                        return false;
                    }
                } elseif ($nin === true) {
                    // Case when zips does not match and there is NIN condition for that zip-code
                    return true;
                }
            }

            // No one zip match: false
            return false;
        }

        if (!empty($zipsTo)) {
            $maxSize = count($zipsFrom) >= count($zipsTo) ? count($zipsFrom) : count($zipsTo);
            $i = 0;
            $zips = [];
            while ($i < $maxSize) {
                $zipFrom = !empty($zipsFrom[$i]) ? $zipsFrom[$i] : static::MIN_ZIP;
                $zipTo = !empty($zipsTo[$i]) ? $zipsTo[$i] : static::MAX_ZIP;

                $nin = false;
                if (mb_substr($zipFrom, 0, 1) == '!') {
                    $zipFrom = mb_substr($zipFrom, 1);
                    $nin = true;
                }
                if (mb_substr($zipTo, 0, 1) == '!') {
                    $zipTo = mb_substr($zipTo, 1);
                    $nin = true;
                }

                $zips[$i] = [
                    'from' => $zipFrom,
                    'to' => $zipTo,
                    'nin' => $nin
                ];
                $i++;
            }

            // Validation itself
            foreach ($zips as $condition) {
                if ($requestZip >= $condition['from'] && $requestZip <= $condition['to']) {
                    // If request zip in diapason
                    if ($condition['nin']) {
                        // and there was NIN condition
                        return false;
                    } else {
                        // and there was IN condition
                        return true;
                    }
                } elseif ($condition['nin']) {
                    // request zip is outside diapason and there was NIN condition
                    return true;
                }
            }

            // Case when no one of the conditions met
            return false;
        }

//        if ($this->getZipFrom() == $this->getZipTo() &&
//            $requestZip == $this->getZipFrom()
//        ) {
//            return true;
//        }
//
//        if ($requestZip < $this->getZipFrom()) {
//            return false;
//        }
//
//        if ($this->getZipTo() && $requestZip > $this->getZipTo()) {
//            return false;
//        }

        return true;
    }

    /**
     * @param RateRequest $request
     * @return bool
     */
    public function validateRequestByPrice(RateRequest $request)
    {
        if (!$this->getPriceFrom() && !$this->getPriceTo()) {
            return true;
        }

        $requestPrice = $request->getPackageValue();
        if ($this->getPriceFrom() == $this->getPriceTo() && $requestPrice == $this->getPriceFrom()) {
            return true;
        }

        if ($requestPrice < $this->getPriceFrom()) {
            return false;
        }

        if ($this->getPriceTo() != 0 && $requestPrice > $this->getPriceTo()) {
            return false;
        }

        return true;
    }

    /**
     * @param RateRequest $request
     * @return bool
     */
    public function validateRequestByQty(RateRequest $request)
    {
        if (!$this->getQtyFrom() && !$this->getQtyTo()) {
            return true;
        }

        $requestQty = $request->getPackageQty();
        if ($this->getQtyFrom() == $this->getQtyTo() && $requestQty == $this->getQtyFrom()) {
            return true;
        }

        if ($requestQty < $this->getQtyFrom()) {
            return false;
        }

        if ($this->getQtyTo() != 0 && $requestQty > $this->getQtyTo()) {
            return false;
        }

        return true;
    }

    /**
     * @param RateRequest $request
     * @return bool
     */
    public function validateRequestByWeight(RateRequest $request)
    {
        if (!$this->getWeightFrom() && !$this->getWeightTo()) {
            return true;
        }

        $requestWeight = $request->getPackageWeight();
        if ($this->getWeightFrom() == $this->getWeightTo() && $requestWeight == $this->getWeightFrom()) {
            return true;
        }

        if ($requestWeight < $this->getWeightFrom()) {
            return false;
        }

        if ($this->getWeightTo() != 0 && $requestWeight > $this->getWeightTo()) {
            return false;
        }

        return true;
    }

    /**
     * Returns zip_from data in uppercase
     *
     * @return array
     */
    public function getZipFrom()
    {
        $zip = mb_strtoupper($this->getData('zip_from'));
        if (!$zip) {
            return [];
        }

        return explode(static::DELIMITER, $zip);
    }

    /**
     * Returns zip_to data in uppercase
     *
     * @return array
     */
    public function getZipTo()
    {
        $zip = mb_strtoupper($this->getData('zip_to'));
        if (!$zip) {
            return [];
        }

        return explode(static::DELIMITER, $zip);
    }

    /**
     * Retrieve rate ID
     *
     * @return int
     */
    public function getRateId()
    {
        return $this->getData('rate_id');
    }

    /**
     * Get id of the corresponding method
     *
     * @return int
     */
    public function getMethodId()
    {
        return $this->getData('method_id');
    }

    /**
     * Get priority of the rate (sort order)
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->getData('priority');
    }

    /**
     * Check is rate active
     *
     * @return int|bool
     */
    public function getActive()
    {
        return $this->getData('active');
    }

    /**
     * Get price calculation method
     *
     * @return int
     */
    public function getRateMethodPrice()
    {
        return $this->getData('rate_method_price');
    }

    /**
     * Retrieve rate name
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Retrieve corresponding country id
     *
     * @return array
     */
    public function getCountryId()
    {
        $countryId = $this->getData('country_id');
        if (!$countryId) {
            return [];
        }

        return $countryId;
    }

    /**
     * Get region plain name
     *
     * @return array
     */
    public function getRegion()
    {
        $region = $this->getData('region');
        if (!$region) {
            return [];
        }

        return $region;
    }

    /**
     * Get id of region
     *
     * @return array
     */
    public function getRegionId()
    {
        $regionId = $this->getData('region_id');
        if (!$regionId) {
            return [];
        }

        return $regionId;
    }

    /**
     * Get conditions price from
     *
     * @return float
     */
    public function getPriceFrom()
    {
        return (float)$this->getData('price_from');
    }

    /**
     * Get conditions price to
     *
     * @return float
     */
    public function getPriceTo()
    {
        return (float)$this->getData('price_to');
    }

    /**
     * Get conditions qty from
     *
     * @return float
     */
    public function getQtyFrom()
    {
        return (float)$this->getData('qty_from');
    }

    /**
     * Get conditions qty to
     *
     * @return float
     */
    public function getQtyTo()
    {
        return (float)$this->getData('qty_to');
    }

    /**
     * Get conditions weight from
     *
     * @return float
     */
    public function getWeightFrom()
    {
        return (float)$this->getData('weight_from');
    }

    /**
     * Get conditions weight to
     *
     * @return float
     */
    public function getWeightTo()
    {
        return (float)$this->getData('weight_to');
    }

    /**
     * Get rates price
     *
     * @return float
     */
    public function getPrice()
    {
        return (float)$this->getData('price');
    }

    /**
     * Get rates price per each product in cart
     *
     * @return float
     */
    public function getPricePerProduct()
    {
        return (float)$this->getData('price_per_product');
    }

    /**
     * Get rates price per each item in cart
     *
     * @return float
     */
    public function getPricePerItem()
    {
        return (float)$this->getData('price_per_item');
    }

    /**
     * Get rates price percent per each product in cart
     *
     * @return float
     */
    public function getPricePercentPerProduct()
    {
        return (float)$this->getData('price_percent_per_product');
    }

    /**
     * Get rates price percent per each item in cart
     *
     * @return float
     */
    public function getPricePercentPerItem()
    {
        return (float)$this->getData('price_percent_per_item');
    }

    /**
     * Get item price percent
     *
     * @return float
     */
    public function getItemPricePercent()
    {
        return (float)$this->getData('item_price_percent');
    }

    /**
     * Price per each unit of weight
     *
     * @return float
     */
    public function getPricePerWeight()
    {
        return (float)$this->getData('price_per_weight');
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
     * Min estimated delivery time (usd to overwrite method value, visible at checkout & cart)
     *
     * @return float
     */
    public function getEstimatedDeliveryTimeMin()
    {
        $value = (float)$this->getData('estimated_delivery_time_min');

        return $value;
    }

    /**
     * Max estimated delivery time (usd to overwrite method value, visible at checkout & cart)
     *
     * @return float
     */
    public function getEstimatedDeliveryTimeMax()
    {
        $value = (float)$this->getData('estimated_delivery_time_max');

        return $value;
    }

    /**
     * Get corresponding method code (relation)
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->getData('method_code');
    }

    /**
     * Set corresponding method code
     *
     * @param string $code
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setMethodCode($code)
    {
        return $this->setData('method_code', $code);
    }

    /**
     * Set id of the corresponding method
     *
     * @param $id
     * @return RateInterface
     */
    public function setMethodId($id)
    {
        return $this->setData('method_id', $id);
    }

    /**
     * Set rate ID
     *
     * @param int $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setRateId($value)
    {
        return $this->setData('rate_id', $value);
    }

    /**
     * Set priority of the rate (sort order)
     *
     * @param int $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPriority($value)
    {
        return $this->setData('priority', $value);
    }

    /**
     * Check is rate active
     *
     * @param bool $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setActive($value)
    {
        return $this->setData('active', $value);
    }

    /**
     * Set price calculation method
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setRateMethodPrice($value)
    {
        return $this->setData('rate_method_price', $value);
    }

    /**
     * Set rate name
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setTitle($value)
    {
        return $this->setData('title', $value);
    }

    /**
     * Retrieve corresponding country id
     *
     * @param int $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setCountryId($value)
    {
        return $this->setData('country_id', $value);
    }

    /**
     * set region plain name
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setRegion($value)
    {
        return $this->setData('region', $value);
    }

    /**
     * set id of region
     *
     * @param int $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setRegionId($value)
    {
        return $this->setData('region_id', $value);
    }

    /**
     * Set conditions zip from
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setZipFrom($value)
    {
        if (is_array($value)) {
            $value = implode(static::DELIMITER, $value);
        }

        return $this->setData('zip_from', $value);
    }

    /**
     * Set conditions zip to
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setZipTo($value)
    {
        if (is_array($value)) {
            $value = implode(static::DELIMITER, $value);
        }

        return $this->setData('zip_to', $value);
    }

    /**
     * Set conditions price from
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPriceFrom($value)
    {
        return $this->setData('price_from', $value);
    }

    /**
     * Set conditions price to
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPriceTo($value)
    {
        return $this->setData('price_to', $value);
    }

    /**
     * Set conditions qty from
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setQtyFrom($value)
    {
        return $this->setData('qty_from', $value);
    }

    /**
     * Set conditions qty to
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setQtyTo($value)
    {
        return $this->setData('qty_to', $value);
    }

    /**
     * Set conditions weight from
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setWeightFrom($value)
    {
        return $this->setData('weight_from', $value);
    }

    /**
     * Set conditions weight to
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setWeightTo($value)
    {
        return $this->setData('weight_to', $value);
    }

    /**
     * Set rates price
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPrice($value)
    {
        return $this->setData('price', $value);
    }

    /**
     * Set rates price per each product in cart
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPricePerProduct($value)
    {
        return $this->setData('price_per_product', $value);
    }

    /**
     * Set rates price per each item in cart
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPricePerItem($value)
    {
        return $this->setData('price_per_item', $value);
    }

    /**
     * Set rates price percent per each product in cart
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPricePercentPerProduct($value)
    {
        return $this->setData('price_percent_per_product', $value);
    }

    /**
     * Set rates price percent per each item in cart
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPricePercentPerItem($value)
    {
        return $this->setData('price_percent_per_item', $value);
    }

    /**
     * Set item price percent
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setItemPricePercent($value)
    {
        return $this->setData('item_price_percent', $value);
    }

    /**
     * Price per each unit of weight
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setPricePerWeight($value)
    {
        return $this->setData('price_per_weight', $value);
    }

    /**
     * Set created at date
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setCreatedAt($value)
    {
        return $this->setData('created_at', $value);
    }

    /**
     * Set last updated date
     *
     * @param string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setUpdatedAt($value)
    {
        return $this->setData('updated_at', $value);
    }

    /**
     * Min estimated delivery time (usd to overwrite method value, visible at checkout & cart)
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setEstimatedDeliveryTimeMin($value)
    {
        return $this->setData('estimated_delivery_time_min', $value);
    }

    /**
     * Max estimated delivery time (usd to overwrite method value, visible at checkout & cart)
     *
     * @param float $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setEstimatedDeliveryTimeMax($value)
    {
        return $this->setData('estimated_delivery_time_max', $value);
    }

    /**
     * Set associated store Ids
     *
     * @param array $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setStoreIds($value)
    {
        return $this->setData('store_ids', $value);
    }

    /**
     * Set store specific labels (title)
     *
     * @param array $storeLabels
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setStoreLabels($storeLabels = [])
    {
        return $this->setData('store_labels', $storeLabels);
    }

    /**
     * Retrieve rate code (used during import\export)
     *
     * @return string
     */
    public function getRateCode()
    {
        return $this->getData('rate_code');
    }

    /**
     * Set rate code (used during import\export)
     *
     * string $value
     * @return \MageWorx\ShippingRules\Api\Data\RateInterface
     */
    public function setRateCode($value)
    {
        return $this->setData('rate_code', $value);
    }
}
