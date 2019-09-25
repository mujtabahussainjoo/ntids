<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Cart;

use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Directory\Model\ResourceModel\Country\Collection as CountryCollection;
use Magento\Directory\Model\ResourceModel\Region\Collection as RegionCollection;
use MageWorx\GeoIP\Model\Geoip;

class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @var CountryCollection
     */
    protected $countryCollection;

    /**
     * @var RegionCollection
     */
    protected $regionCollection;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterface
     */
    protected $defaultShippingAddress = null;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var Geoip
     */
    protected $geoIp;

    /**
     * @param AttributeMerger $merger
     * @param CountryCollection $countryCollection
     * @param RegionCollection $regionCollection
     * @param CheckoutSession $checkoutSession
     * @param Geoip $geoip
     */
    public function __construct(
        AttributeMerger $merger,
        CountryCollection $countryCollection,
        RegionCollection $regionCollection,
        CheckoutSession $checkoutSession,
        Geoip $geoip
    ) {
        $this->merger = $merger;
        $this->countryCollection = $countryCollection;
        $this->regionCollection = $regionCollection;
        $this->checkoutSession = $checkoutSession;
        $this->geoIp = $geoip;
    }

    /**
     * Show City in Shipping Estimation
     *
     * @return bool
     * @codeCoverageIgnore
     */
    protected function isCityActive()
    {
        return true;
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

    private function resolveAddressData()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();
        /** @var \Magento\Quote\Model\Quote\Address $shippingAddress */
        $shippingAddress = $quote->getShippingAddress();

        $customerData = $this->geoIp->getCurrentLocation();
        if ($customerData->getCode()) {
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
        }
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        $elements = [
            'city' => [
                'value' =>  $this->getShippingAddress()->getCity()
            ],
            'country_id' => [
                'value' => $this->getShippingAddress()->getCountryId()
            ],
            'region_id' => [
                'value' => $this->getShippingAddress()->getRegionCode()
            ],
            'region' => [
                'value' => $this->getShippingAddress()->getRegionCode()
            ],
            'postcode' => [
                'value' => $this->getShippingAddress()->getPostcode()
            ]
        ];

        if (isset($jsLayout['components']['block-summary']['children']['block-shipping']['children']
            ['address-fieldsets']['children'])
        ) {
            $fieldSetPointer = &$jsLayout['components']['block-summary']['children']['block-shipping']
            ['children']['address-fieldsets']['children'];
            $fieldSetPointer = array_merge_recursive($fieldSetPointer, $elements);
            $fieldSetPointer['region_id']['config']['skipValidation'] = true;
        }
        return $jsLayout;
    }
}
