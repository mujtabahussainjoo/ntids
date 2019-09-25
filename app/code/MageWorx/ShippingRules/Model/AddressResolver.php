<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\Locale\ResolverInterface;
use MageWorx\ShippingRules\Helper\Data as Helper;
use Magento\Checkout\Model\Session as CheckoutSession;
use MageWorx\GeoIP\Model\Geoip;
use MageWorx\ShippingRules\Helper\Data;
use MageWorx\ShippingRules\Api\AddressResolverInterface;

/**
 * Class AddressResolver
 * @package MageWorx\ShippingRules\Model
 *
 * Used to resolve current customers address based on the selection in popup or on the geo ip database
 */
class AddressResolver implements AddressResolverInterface
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var Geoip
     */
    protected $geoIp;

    /**
     * @var RegionCollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * @var \MageWorx\ShippingRules\Model\Zone|null
     */
    protected $zone;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * ChannelButtons constructor.
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param ResolverInterface $localeResolver
     * @param Helper $helper
     * @param Geoip $geoip
     * @param RegionCollectionFactory $regionCollectionFactory
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        ResolverInterface $localeResolver,
        Data $helper,
        Geoip $geoip,
        RegionCollectionFactory $regionCollectionFactory,
        RegionFactory $regionFactory
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->localeResolver = $localeResolver;
        $this->helper = $helper;
        $this->geoIp = $geoip;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Get array of the regions by country_id (used as key)
     *
     * @return array
     */
    public function getRegionJsonList()
    {
        $collectionByCountry = [];
        /** @var \Magento\Directory\Model\ResourceModel\Region\Collection $collection */
        $collection = $this->regionCollectionFactory->create();
        /** @var \Magento\Directory\Model\Region $item */
        foreach ($collection as $item) {
            $collectionByCountry[$item->getData('country_id')][] = $item->getData();
        }

        return $collectionByCountry;
    }

    /**
     * Get visitors country id
     *
     * @return int
     */
    public function getCountryId()
    {
        return $this->getShippingAddress()->getCountryId();
    }

    /**
     * Get visitors region id
     *
     * @return string
     */
    public function getRegionId()
    {
        return $this->getShippingAddress()->getRegionId();
    }

    /**
     * Get visitors region (as string)
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->getShippingAddress()->getRegion();
    }

    /**
     * Get visitors country name
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->getShippingAddress()->getCountryModel()->getName($this->localeResolver->getLocale());
    }

    /**
     * Get visitors region code
     *
     * @return string
     */
    public function getRegionCode()
    {
        return $this->getShippingAddress()->getRegionCode();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    private function getShippingAddress()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();
        /** @var \Magento\Quote\Model\Quote\Address $shippingAddress */
        $shippingAddress = $quote->getShippingAddress();

        if (!$shippingAddress->getCountryId()) {
            $this->resolveAddressData();
        }

        return $shippingAddress;
    }

    /**
     * Resolve current address data and store it in the shipping address (without save!)
     */
    private function resolveAddressData()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();
        /** @var \Magento\Quote\Model\Quote\Address $shippingAddress */
        $shippingAddress = $quote->getShippingAddress();

        $customerData = $this->geoIp->getCurrentLocation();
        if ($customerData->getCode() && $this->helper->isCountryAllowed($customerData->getCode())) {
            /** @var \Magento\Directory\Model\Country $currentCountry */
            $currentCountry = $shippingAddress
                ->getCountryModel()
                ->loadByCode($customerData->getCode());

            if (!$currentCountry) {
                return;
            }

            $shippingAddress->setCountryId($currentCountry->getId());
            $shippingAddress->setRegion($customerData->getRegion());
            $shippingAddress->setRegionCode($customerData->getRegionCode());
            $shippingAddress->setCity($customerData->getCity());
            $shippingAddress->setPostcode($customerData->getPosttalCode());

            $regionModel = $this->regionFactory->create();
            if ($customerData->getRegionCode() && $currentCountry->getId()) {
                $regionModel->loadByCode($customerData->getRegionCode(), $currentCountry->getId());
                $regionId = $regionModel->getRegionId();
                $shippingAddress->setRegionId($regionId);
            }
        }
    }
}
