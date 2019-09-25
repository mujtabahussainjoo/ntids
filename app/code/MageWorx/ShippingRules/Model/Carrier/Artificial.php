<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use MageWorx\ShippingRules\Observer\Logger\Log;

class Artificial extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = null;

    /**
     * @var \MageWorx\ShippingRules\Model\CarrierFactory
     */
    protected $carrierFactory;

    /**
     * @var \MageWorx\ShippingRules\Model\ResourceModel\Carrier\CollectionFactory
     */
    protected $carrierCollectionFactory;

    /**
     * @var \MageWorx\ShippingRules\Model\ResourceModel\Carrier\Collection
     */
    protected $carriersCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $loadedCarriers = [];

    /**
     * @var RateRequest
     */
    protected $request;

    /**
     * @var \Magento\Store\Model\StoreResolver
     */
    private $storeResolver;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $_rateMethodFactory;


    protected $_productFactory;


   /* protected $mailoutProduct;


    protected $thirdpartyproduct;


    protected $onlythresold;


    protected $onlyThirdparty;


    protected $allShppingExemption;


    protected $allmailoutShppingException;


    protected $allthersoldShppingException;


    protected $allThirdpartyNonThersold;*/


    protected  $allTypesShippingExcemption;


    protected $allThirdPartyProducts;


    protected $allThirdpartyThersoldProducts;


    protected $allThirdpartyNonThersoldProducts;


    protected $allMailoutproducts;


    protected $isThersoldActive;


    protected $allProductsShippingExcemption;


    protected $allmailoutShppingException;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \MageWorx\ShippingRules\Model\CarrierFactory $carrierFactory
     * @param \MageWorx\ShippingRules\Model\ResourceModel\Carrier\CollectionFactory $collectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \MageWorx\ShippingRules\Model\CarrierFactory $carrierFactory,
        \MageWorx\ShippingRules\Model\ResourceModel\Carrier\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\StoreResolver $storeResolver,
        \Magento\Catalog\Model\ProductFactory  $productFactory,
        array $data = []
    ) {

        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->carrierFactory = $carrierFactory;
        $this->carrierCollectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->storeResolver = $storeResolver;
        $this->_productFactory = $productFactory;
        /*$this->mailoutProduct = 0;
        $this->thirdpartyproduct = 0;
        $this->onlythresold = 0;
        $this->onlyThirdparty = 1;
        $this->allShppingExemption = 0;
        $this->allmailoutShppingException = 0;
        $this->allthersoldShppingException = 0;
        $this->allThirdpartyNonThersold = 0;*/
        $this->allTypesShippingExcemption = 0;
        $this->allThirdPartyProducts = 0;
        $this->allThirdpartyThersoldProducts = 0;
        $this->allThirdpartyNonThersoldProducts = 0;
        $this->allMailoutproducts = 0;
        $this->isThersoldActive = 0;
        $this->allProductsShippingExcemption = 0;
        $this->allmailoutShppingException = 0;
        $this->byMistakeFreeshippingZeroApplying = 0;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return bool|array|Result
     */
    public function collectRates(RateRequest $request){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shipping-price-rules.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $this->setRequest($request);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $config = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');

        $isThresholdActive = $config->getValue('carriers/thresholdshippingperproduct/active', $storeScope);
        $thresholdFixedPrice = $config->getValue('carriers/thresholdshippingperproduct/fixedprice', $storeScope);
        $thresholdPrice = $config->getValue('carriers/thresholdshippingperproduct/thresholdprice', $storeScope);
        $thresholdSkus = $config->getValue('carriers/thresholdshippingperproduct/skus', $storeScope);
        $thresholdTitle = $config->getValue('carriers/thresholdshippingperproduct/title', $storeScope);
        $shippingPerProductTitle = $config->getValue('carriers/shippingperproduct/title');

       
        $fixed_price_skus = explode(PHP_EOL, $thresholdSkus);
        $fixed_price_sku = array_map('trim', $fixed_price_skus);

        $orderTotal = $request->getBaseSubtotalInclTax();

        $result = [];
        /** @var \MageWorx\ShippingRules\Model\Carrier $carrier */
        $carrier = $this->findCarrier();
        if (!$carrier) {
            return $result;
        }

        $this->addData($carrier->getData());
        $this->_code = $carrier->getData('carrier_code');

        $storeId = $this->storeResolver->getCurrentStoreId();
        $methods = $carrier->getMethods($storeId);
        if (empty($methods)) {
            return $result;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        $ids = $this->getApplicapleItemIds($request,$fixed_price_sku,$isThresholdActive,$orderTotal,$thresholdPrice,$thresholdFixedPrice);

        $mailOutProducts = array();
        $mailoutShippingExemptionList = array();

        $thirdpartyThersold = array();
        $thirdpartyThersoldShippingExemtionList = array();

        $thirdpartyNonThersold = array();
        $thirdpartyNonThersoldShippingExemtionList = array();

        $allProducts = array();
        $allShippingExemptionList = array();




        $shippingThirdpartyNonThersoldProductPrice = 0;

        foreach ($ids as $skuItem) {
            if($skuItem['price'] == 0 && $skuItem['is_shipping_exemption'] == 0 && $skuItem['type'] != 'mailout'){
                $this->byMistakeFreeshippingZeroApplying = 1;
            }

            if($skuItem['type'] == 'mailout'){
                if($skuItem['is_shipping_exemption'] == 1){
                   array_push($mailoutShippingExemptionList, $skuItem['sku']);
                }
               array_push($mailOutProducts,$skuItem['sku']);
            }

            if($skuItem['type'] == 'thirdparty' && $skuItem['isthesold'] == 1){
                if($skuItem['is_shipping_exemption'] == 1){
                    array_push($thirdpartyThersoldShippingExemtionList, $skuItem['sku']);
                }
                array_push($thirdpartyThersold, $skuItem['sku']);
            }

            if($skuItem['type'] == 'thirdparty' && $skuItem['isthesold'] == 0){
                if($skuItem['is_shipping_exemption'] == 1){
                    array_push($thirdpartyNonThersoldShippingExemtionList, $skuItem['sku']);
                    //$shippingThirdpartyNonThersoldProductPrice += ($skuItem['price'] * $skuItem['qty']);
                }else{
                    $shippingThirdpartyNonThersoldProductPrice += ($skuItem['price'] * $skuItem['qty']);
                }
                array_push($thirdpartyNonThersold, $skuItem['sku']);
                //$shippingThirdpartyNonThersoldProductPrice += ($skuItem['price'] * $skuItem['qty']);
            }

            if($skuItem['is_shipping_exemption'] == 1){
                array_push($allShippingExemptionList, $skuItem['sku']);
            }
            array_push($allProducts, $skuItem['sku']);
        }

        if($this->byMistakeFreeshippingZeroApplying == 1){
            throw new \Exception("Few products are don't having shipping, please contact customer care");
        }


        #only ThirdpartyThersol  , Thirdparty Non-Thersold , Mailout Products

        if(count($allProducts) === count($allShippingExemptionList)){
            $this->allProductsShippingExcemption = 1;
        }

        if(empty($mailOutProducts)){
            $this->allThirdPartyProducts = 1;
        }

        if(count($allProducts) === count($mailOutProducts)){
            $this->allMailoutproducts = 1;
        }

        if(count($mailOutProducts) === count($mailoutShippingExemptionList)){
            $this->allmailoutShppingException = 1;
        }

        if(count($thirdpartyNonThersold) === count($thirdpartyNonThersoldShippingExemtionList)){
            $this->allThirdpartyNonThersoldProducts = 1;
        }

        if(count($thirdpartyThersold) === count($thirdpartyThersoldShippingExemtionList)){
            $this->allThirdpartyThersoldProducts = 1;
        }



        $shippingPriceCalThirdPartyNonThersold = 0;
        $shippingTitleThirdPartyNonThersold = '';

        $shippingPriceCalNonThirdPartyNonThersold = 0;
        $shippingTitleNonThirdPartyNonThersold = '';


        $thirdPartyProductShippingPriceTotal = 0;
        $thirdPartyProductShippingTitle = '';

       
        if($this->allThirdpartyNonThersoldProducts != 1 && (count($thirdpartyNonThersold) > 0)){
            if($shippingThirdpartyNonThersoldProductPrice > 0) {
                $shippingPriceCalThirdPartyNonThersold = $shippingThirdpartyNonThersoldProductPrice;
                if ($shippingPerProductTitle) {
                    $shippingTitleThirdPartyNonThersold = $shippingPerProductTitle;
                } else {
                    $shippingTitleThirdPartyNonThersold = 'Shipping Per Product';
                }
            }
        }

        if($this->allThirdpartyThersoldProducts != 1 && (count($thirdpartyThersold) > 0)){
            
             if($isThresholdActive){
                 if ($orderTotal >= $thresholdPrice) {
                     if($thresholdFixedPrice > 0) {
                         $this->isThersoldActive = 1;
                         $shippingPriceCalThirdPartyThersold = $thresholdFixedPrice;
                         if ($thresholdTitle) {
                             $shippingTitleThirdPartyThersold = $thresholdTitle;
                         } else {
                             $shippingTitleThirdPartyThersold = "Threshold Price";
                         }
                     }
                 }
             }
        }else{
            $shippingPriceCalNonThirdPartyNonThersold = 0;
            $shippingTitleNonThirdPartyNonThersold =  '';
        }

        if($this->allThirdpartyNonThersoldProducts != 1 && $this->allThirdpartyThersoldProducts != 1){
            $thirdPartyProductShippingPriceTotal = $shippingPriceCalThirdPartyNonThersold + $shippingPriceCalThirdPartyThersold;
            $thirdPartyProductShippingTitle =  $shippingTitleThirdPartyNonThersold .' '. $shippingTitleThirdPartyThersold;
        }elseif ($this->allThirdpartyNonThersoldProducts != 1){
            $thirdPartyProductShippingPriceTotal = $shippingPriceCalThirdPartyNonThersold;
            $thirdPartyProductShippingTitle = $shippingTitleThirdPartyNonThersold;
        }elseif ($this->allThirdpartyThersoldProducts != 1){
            $thirdPartyProductShippingPriceTotal = $shippingPriceCalThirdPartyThersold;
            $thirdPartyProductShippingTitle = $shippingTitleThirdPartyThersold;
        }

        /** @var \MageWorx\ShippingRules\Model\Carrier\Method $methodData */
        foreach ($methods as $methodData) {
            if (!$methodData->getActive()) {
                continue;
            }
            if($this->allProductsShippingExcemption == 1){
                $title = "Free Shipping";
            }elseif($this->allmailoutShppingException == 1){
                $title = $thirdPartyProductShippingTitle;
            }else{
                $title = $carrier->getTitle() .' '. $thirdPartyProductShippingTitle;
            }

            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier($this->getId());
            //$method->setCarrierTitle($title);
            $method->setCarrierTitle('');
            $method->setMethod($methodData->getData('code'));
            $method->setCost($methodData->getData('cost'));
            $method = $this->applyRates($method, $methodData, $thirdPartyProductShippingPriceTotal,$thirdPartyProductShippingTitle);
            $logger->info("method code:".$method->getMethod());
            if ($method) {
				$logger->info("allThirdPartyProducts:".$this->allThirdPartyProducts);
				$logger->info("allmailoutShppingException:".$this->allmailoutShppingException);
				
				if(($this->allThirdPartyProducts == 1 || $this->allmailoutShppingException == 1) && $method->getMethod() != "thirdparty")
					continue;
				
				if($this->allThirdPartyProducts == 0 && $this->allmailoutShppingException == 0 && $method->getMethod() == "thirdparty")
					continue; 
				
                if ($methodData->getAllowFreeShipping() && $request->getFreeShipping() === true) {
                    $method->setPrice('0.00');
                }

                if ($methodData->getDescription()) {
                    $method->setData('method_description', $methodData->getDescription());
                }
                  $result->append($method);
            }
        }
        return $result;
    }

    /**
     * @param RateRequest|null $request
     * @return $this
     */
    protected function setRequest(RateRequest $request = null)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return RateRequest
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Find corresponding carrier in the collection
     *
     * @return \MageWorx\ShippingRules\Model\Carrier|null
     */
    protected function findCarrier()
    {
        $carrier = $this->carrierFactory
            ->create()
            ->load($this->getData('id'), 'carrier_code');

        return $carrier;
    }

    /**
     * Get all data of the carrier specified by code (carrier_code)
     * It's possible to get the specified parameter ($param) of the carrier
     *
     * @param $code
     * @param null $param
     * @return mixed|null
     */
    protected function getSpecificCarrierData($code, $param = null)
    {
        $item = $this->carriersCollection->getItemByColumnValue('carrier_code', $code);
        if (!$item) {
            return null;
        }

        if (!$param) {
            return $item->getData();
        }

        return $item->getData($param);
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     * @api
     */
    public function getAllowedMethods()
    {
        $carrier = $this->findCarrier();
        if (!$carrier) {
            return [];
        }

        return $carrier->getMethodsCollection()->toAllowedMethodsArray();
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $method
     * @param \MageWorx\ShippingRules\Model\Carrier\Method $methodData
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method|null
     */
    protected function applyRates(
        \Magento\Quote\Model\Quote\Address\RateResult\Method $method,
        \MageWorx\ShippingRules\Model\Carrier\Method $methodData,
        $thirdPartyProductShippingPriceTotal,
        $thirdPartyProductShippingTitle
    ) {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shipping-applyrates.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $logger->info("apply Rates");
        $logger->info($thirdPartyProductShippingPriceTotal);
        $logger->info($thirdPartyProductShippingTitle);

        $disableMethodWithoutValidRates = $methodData->getDisabledWithoutValidRates();
        $request = $this->getRequest();
        $rates = $methodData->getRates() ? $methodData->getRates() : [];
        $ratesApplied = [];

        /** @var \MageWorx\ShippingRules\Model\Carrier\Method\Rate $rate */
        foreach ($rates as $rate) {
            if (!$rate->validateRequest($request)) {
                continue;
            }
            $ratesApplied[] = $rate;
        }
        $price = 0;
        $title = '';
        if($this->allProductsShippingExcemption == 1){
            $title = "Free Shipping";
        }elseif($this->allmailoutShppingException == 1){
            $title = $thirdPartyProductShippingTitle;
            $price = $thirdPartyProductShippingPriceTotal;
        }else{
            $title = $methodData->getData('title') .' '. $thirdPartyProductShippingTitle;
            $price = $methodData->getData('price') + $thirdPartyProductShippingPriceTotal;
        }

        $method->setMethodTitle($title);
        $method->setPrice($price);

        if ($ratesApplied) {
            $filteredRates = $this->filterRatesBeforeApply($ratesApplied, $request, $methodData);
            /** @var \MageWorx\ShippingRules\Model\Carrier\Method\Rate $validRate */
            foreach ($filteredRates as $validRate) {
                $method = $validRate->applyRateToMethod($method, $request, $methodData);
            }
        } elseif ($disableMethodWithoutValidRates) {
            return null;
        }

        if ($methodData->isNeedToDisplayEstimatedDeliveryTime()) {
            $titleWithDate = $method->getMethodTitle() .
                $methodData->getEstimatedDeliveryTimeMessageFormatted(' (', ')');
            $method->setMethodTitle($titleWithDate);
        }

        return $method;
    }

    protected function filterRatesBeforeApply(
        $rates,
        RateRequest $request,
        \MageWorx\ShippingRules\Model\Carrier\Method $methodData
    ) {
        if (!$rates) {
            return $rates;
        }

        if ($methodData->getMultipleRatesPrice()) {
            $multipleRatesCalculationType = $methodData->getMultipleRatesPrice();
        } else {
            $multipleRatesCalculationType = $this->storeManager
                ->getStore()
                ->getConfig('mageworx_shippingrules/main/multiple_rates_price');
        }

        switch ($multipleRatesCalculationType) {
            case \MageWorx\ShippingRules\Model\Carrier\Method\Rate::MULTIPLE_RATES_PRICE_CALCULATION_MAX_PRIORITY:
                $resultRate = $this->getRateWithMaxPriority($rates);
                break;
            case \MageWorx\ShippingRules\Model\Carrier\Method\Rate::MULTIPLE_RATES_PRICE_CALCULATION_MAX_PRICE:
                $resultRate = $this->getRateWithMaxPrice($rates, $request, $methodData);
                break;
            case \MageWorx\ShippingRules\Model\Carrier\Method\Rate::MULTIPLE_RATES_PRICE_CALCULATION_MIN_PRICE:
                $resultRate = $this->getRateWithMinPrice($rates, $request, $methodData);
                break;
            case \MageWorx\ShippingRules\Model\Carrier\Method\Rate::MULTIPLE_RATES_PRICE_CALCULATION_SUM_UP:
            default:
                return $rates;
        }

        $resultRates = [$resultRate->getId() => $resultRate];

        return $resultRates;
    }

    /**
     * Find rate with max priority in array of rates
     *
     * @param array $rates
     * @return \MageWorx\ShippingRules\Model\Carrier\Method\Rate
     */
    protected function getRateWithMaxPriority($rates)
    {
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method\Rate $currentRate */
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method\Rate $rate */
        foreach ($rates as $currentRate) {
            if (!isset($rate) || $rate->getPriority() <= $currentRate->getPriority()) {
                $rate = $currentRate;
            }
        }

        return $rate;
    }

    /**
     * Find rate with max price in array of rates
     *
     * @param array $rates
     * @return \MageWorx\ShippingRules\Model\Carrier\Method\Rate
     */
    protected function getRateWithMaxPrice(
        $rates,
        RateRequest $request,
        \MageWorx\ShippingRules\Model\Carrier\Method $methodData
    ) {
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method\Rate $currentRate */
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method\Rate $rate */
        $actualRateCalculatedPrice = 0;
        foreach ($rates as $currentRate) {
            $currentRatePrice = $currentRate->getCalculatedPrice($request, $methodData);
            if (!isset($rate) || $actualRateCalculatedPrice <= $currentRatePrice) {
                $rate = $currentRate;
                $actualRateCalculatedPrice = $currentRatePrice;
            }
        }

        return $rate;
    }

    /**
     * Find rate with min price in array of rates
     *
     * @param array $rates
     * @return \MageWorx\ShippingRules\Model\Carrier\Method\Rate
     */
    protected function getRateWithMinPrice(
        $rates,
        RateRequest $request,
        \MageWorx\ShippingRules\Model\Carrier\Method $methodData
    ) {
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method\Rate $currentRate */
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method\Rate $rate */
        $actualRateCalculatedPrice = 0;
        foreach ($rates as $currentRate) {
            $currentRatePrice = $currentRate->getCalculatedPrice($request, $methodData);
            if (!isset($rate) || $actualRateCalculatedPrice >= $currentRatePrice) {
                $rate = $currentRate;
                $actualRateCalculatedPrice = $currentRatePrice;
            }
        }

        return $rate;
    }

    protected function getApplicapleItemIds($request,$skus,$isThresholdActive,$orderTotal,$thresholdPrice,$thresholdFixedPrice){
        $ids = [];
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shipping-price-product.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $shippingPrice = 0;
        foreach ($request->getAllItems() as $item) {
            if ($item->getParentItem() || $item->getProduct()->isVirtual()) {
                continue;
            }
            if (!$item->getFreeShipping()) {
                $id = $item->getProduct()->getId();
                if (isset($ids[$id])) {
                    $ids[$id]['qty'] += $item->getQty();
                } else {
                    $ids[$id]['qty'] = $item->getQty();
                }
                $product = $this->_productFactory->create()->load($id);
                if($product->getTypeId() == 'simple' || $product->getTypeId() == 'bundle'){
                    #checking it's Mailout product or not
                    if($product->getIsMailoutProduct() != 1){
                        $ids[$id]['type'] = 'thirdparty';
                        if($isThresholdActive && ($orderTotal >= $thresholdPrice)) {
                            $isInThresoldArray = array_search($product->getSku(), $skus);
                            if (is_numeric($isInThresoldArray)) { #$isInThresoldArray || $isInThresoldArray === 0)
                                $ids[$id]['price'] = $thresholdFixedPrice;
                                $ids[$id]['isthesold'] = 1;
                                if($product->getIsShippingExemption()) {
                                    $ids[$id]['is_shipping_exemption'] = 1;
                                }else {
                                    $ids[$id]['is_shipping_exemption'] = 0;
                                }
                            } else {
                                if($product->getIsShippingExemption()) {
                                    $ids[$id]['price'] = 0;
                                    $ids[$id]['is_shipping_exemption'] = 1;
                                }else {
                                    if ($product->getData('shipping_per_product') > 0.000001) {
                                        $ids[$id]['price'] = $product->getData('shipping_per_product');
                                    } else {
                                        $ids[$id]['price'] = 13;
                                    }
                                    $ids[$id]['is_shipping_exemption'] = 0;
                                }
                                $ids[$id]['isthesold'] = 0;
                            }
                        }else{
                            if($product->getIsShippingExemption()) {
                                $ids[$id]['price'] = 0;
                                $ids[$id]['is_shipping_exemption'] = 1;
                            }else{
                                if ($product->getData('shipping_per_product') > 0.000001) {
                                    $ids[$id]['price'] = 13;
                                } else {
                                    $ids[$id]['price'] = 14;
                                }
                                $ids[$id]['is_shipping_exemption'] = 0;
                            }
                            $ids[$id]['isthesold'] = 0;
                        }
                    }else{
                        if($product->getIsShippingExemption()) {
                            $ids[$id]['is_shipping_exemption'] = 1;
                        }else{
                            $ids[$id]['is_shipping_exemption'] = 0;
                        }
                        $ids[$id]['type'] = 'mailout';
                        $ids[$id]['price'] = $shippingPrice;
                    }
                }
                $ids[$id]['sku'] = $product->getSku();
            }
        }
		$logger->info("hellow");
        $logger->info($ids);
        return $ids;
    }
}
