<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Block\Zone;

use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use MageWorx\GeoIP\Model\Geoip;
use MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone\CollectionFactory as ExtendedZoneCollectionFactory;
use MageWorx\ShippingRules\Helper\Data as Helper;

class Selector extends Template
{

    /**
     * @var Session|\Magento\Backend\Model\Session\Quote
     */
    protected $session;

    /**
     * @var Geoip
     */
    protected $geoIp;

    /**
     * @var ExtendedZoneCollectionFactory
     */
    protected $ezCollectionFactory;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @param Template\Context $context
     * @param Session $checkoutSession
     * @param CustomerSession $customerSession
     * @param Geoip $geoip
     * @param ExtendedZoneCollectionFactory $ezCollectionFactory
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $checkoutSession,
        CustomerSession $customerSession,
        Geoip $geoip,
        ExtendedZoneCollectionFactory $ezCollectionFactory,
        Helper $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->session = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->geoIp = $geoip;
        $this->ezCollectionFactory = $ezCollectionFactory;
        $this->helper = $helper;
    }

    /**
     * JSON Data to create modal component
     *
     * @return string
     */
    public function getDataJson()
    {
        return json_encode([
            'html' => $this->getContent(),
            'country' => $this->getShippingAddress()->getCountryModel()->getName(),
            'country_code' => $this->getShippingAddress()->getCountryId(),
            'country_list' => $this->getShippingAddress()->getCountryModel()->getCollection()->toOptionArray(),
            'save_url' => $this->getUrl('mageworx_shippingrules/zone/change')
        ]);
    }

    /**
     * Returns current customers shipping address from the quote
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getShippingAddress()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->session->getQuote();

        /** @var \Magento\Quote\Model\Quote\Address $shippingAddress */
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            $storedData = $this->customerSession->getData('customer_location');
            if (!empty($storedData) && is_array($storedData)) {
                $shippingAddress->addData($storedData);
            }
        }

        return $shippingAddress;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        $currentCountry = $this->getCurrentCountry();
        if (!$currentCountry) {
            $customerData = $this->geoIp->getCurrentLocation();
            if ($customerData->getCode()) {
                $currentCountry = $this->getShippingAddress()
                    ->getCountryModel()
                    ->loadByCode($customerData->getCode())
                    ->getName();
            }
        }

        if (!$currentCountry) {
            $label = <<<CONTENT
Please, select you shipping region.
CONTENT;
        } else {
            $label = <<<CONTENT
Your Shipping Country: $currentCountry
CONTENT;
        }

        return __($label);
    }

    /**
     * Get current country name
     *
     * @return string
     */
    public function getCurrentCountry()
    {
        return $this->getShippingAddress()->getCountryModel()->getName();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $content = <<<CONTENT
<div id="shipping-zone-modal-public-content">
</div>
CONTENT;

        return $content;
    }

    /**
     * Retrieve serialized JS layout configuration ready to use in template
     *
     * @return string
     */
    public function getJsLayout()
    {
        $extendedZones = $this->getExtendedZones();
        /** @var \Magento\Directory\Model\ResourceModel\Country\Collection $countryListCollection */
        $countryListCollection = $this->getShippingAddress()
            ->getCountryModel()
            ->getCollection();
        $countryList = $countryListCollection->loadByStore()
            ->toOptionArray();

        $additionalData = [
            'components' => [
                'location' => [
                    'data' => [
                        'loc_test' => 1,
                        'html' => $this->getContent(),
                        'country' => $this->getShippingAddress()->getCountryModel()->getName(),
                        'country_code' => $this->getShippingAddress()->getCountryId(),
                        'country_list' => $countryList,
                        'region' => $this->getShippingAddress()->getRegion(),
                        'region_code' => $this->getShippingAddress()->getRegionCode(),
                        'region_id' => $this->getShippingAddress()->getRegionId(),
                        'save_url' => $this->getUrl('mageworx_shippingrules/zone/change'),
                        'extended_zones' => $extendedZones,
                        'display_address_only' => $this->helper->isOnlyAddressFieldsShouldBeShown()
                    ],
                ],
            ],
        ];
        $this->jsLayout = array_merge_recursive($this->jsLayout, $additionalData);

        return json_encode($this->jsLayout);
    }

    /**
     * @return array|\MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone[]
     */
    private function getExtendedZones()
    {
        $outputItems = [];
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone\Collection $collection */
        $collection = $this->ezCollectionFactory->create();
        $collection->addIsActiveFilter();
        $collection->addStoreFilter($this->_storeManager->getStore()->getId());
        $collection->setOrder('priority');
        $items = $collection->getItems();
        /** @var \MageWorx\ShippingRules\Model\ExtendedZone $item */
        foreach ($items as $item) {
            $outputItems[] = [
                'id' => $item->getId(),
                'image' => $this->helper->getImageUrl($item->getImage(), Helper::IMAGE_TYPE_FRONTEND_PREVIEW),
                'name' => $item->getLabel($this->_storeManager->getStore()->getId()),
                'description' => $item->getDescription(),
                'countries' => $item->getCountriesId(),
            ];
        }

        return $outputItems;
    }
}
