<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Directory\Model\RegionFactory;
use MageWorx\GeoIP\Model\Geoip;
use MageWorx\ShippingRules\Helper\Data;
use Psr\Log\LoggerInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var Geoip
     */
    protected $geoIp;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param CheckoutSession $checkoutSession
     * @param Geoip $geoIp
     * @param RegionFactory $regionFactory
     * @param Data $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Geoip $geoIp,
        RegionFactory $regionFactory,
        Data $helper,
        LoggerInterface $logger
    ) {
    
        $this->checkoutSession = $checkoutSession;
        $this->geoIp = $geoIp;
        $this->regionFactory = $regionFactory;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $resultConfig = [];
        try {
            $customerData = $this->geoIp->getCurrentLocation();
            if (!$customerData->getCode()) {
                return $resultConfig;
            }

            if (!$this->helper->isCountryAllowed($customerData->getCode())) {
                return $resultConfig;
            }

            $resultConfig = [
                'defaultCountryId' => $customerData->getCode(),
                'defaultRegion' => $customerData->getRegion(),
            ];

            if ($customerData->getRegionCode()) {
                $regionModel = $this->regionFactory
                    ->create()
                    ->loadByCode(
                        $customerData->getRegionCode(),
                        $customerData->getCode()
                    );
                $resultConfig['defaultRegionId'] = $regionModel->getId();
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $resultConfig;
    }
}
