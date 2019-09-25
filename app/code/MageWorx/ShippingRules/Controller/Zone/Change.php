<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Zone;

use Magento\Customer\Model\Session;
use Magento\Directory\Model\CountryInformationAcquirer;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\LayoutFactory;
use MageWorx\GeoIP\Model\Geoip;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\NoSuchEntityException;

class Change extends Action
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var Geoip
     */
    protected $geoIp;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var CountryInformationAcquirer
     */
    protected $countryInformationAcquirer;

    /**
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param LayoutFactory $layoutFactory
     * @param Geoip $geoIp
     * @param Session $customerSession
     * @param DataObjectFactory $dataObjectFactory
     * @param JsonFactory $resultJsonFactory
     * @param CheckoutSession $checkoutSession
     * @param CountryInformationAcquirer $countryInformationAcquirer
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        LayoutFactory $layoutFactory,
        Geoip $geoIp,
        Session $customerSession,
        DataObjectFactory $dataObjectFactory,
        JsonFactory $resultJsonFactory,
        CheckoutSession $checkoutSession,
        CountryInformationAcquirer $countryInformationAcquirer
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layoutFactory = $layoutFactory;
        $this->geoIp = $geoIp;
        $this->session = $customerSession;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->jsonResultFactory = $resultJsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->countryInformationAcquirer = $countryInformationAcquirer;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $countryCode = $this->getRequest()->getParam('country_code');
        if ($countryCode) {
            $data['country_id'] = $countryCode;
            $data['country_code'] = $countryCode;
            $data['region_code'] = $this->getRequest()->getParam('region_code');
            if (!$data['region_code']) {
                $data['region'] = $this->getRequest()->getParam('region');
                $data['region_id'] = null;
            } else {
                $data['region'] = null;
            }
            $data['country_name'] = $this->getCountryNameById($data['country_id']);
            $data['country'] = $this->getCountryNameById($data['country_id']);

            if ($data['region_code'] && $data['country_id']) {
                $countryInfo = $this->getCountryInfo($data['country_id']);
                if (!$countryInfo->getAvailableRegions()) {
                    $data['region_id'] = null;
                } else {
                    /** @var \Magento\Directory\Api\Data\RegionInformationInterface|null $region */
                    foreach ($countryInfo->getAvailableRegions() as $region) {
                        if ($region->getCode() == $data['region_code']) {
                            $data['region_id'] = $region->getId();
                            break;
                        }
                    }
                }
            }

            $this->session->setData('customer_location', $data);
            $quote = $this->checkoutSession->getQuote();
            $shippingAddress = $quote->getShippingAddress()->addData($data);
            $shippingAddress->getResource()->save($shippingAddress);
            $quote->getResource()->save($quote);
            $this->checkoutSession->setQuoteId($quote->getId());

            $dataObject = $this->dataObjectFactory->create($data);
            $this->session->setCustomerLocation($dataObject);
            $result->setData(['success' => true, 'time' => time(), 'customer_data' => $data]);
        } else {
            $result->setData(['success' => false, 'time' => time(), 'customer_data' => []]);
        }

        return $result;
    }

    /**
     * Get country name by country id
     *
     * @param $id
     * @return mixed|null|string
     */
    protected function getCountryNameById($id)
    {
        try {
            $countryInfo = $this->countryInformationAcquirer->getCountryInfo($id);

            return $countryInfo->getFullNameLocale();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * Retrieve country information by id
     *
     * @param $id
     * @return \Magento\Directory\Api\Data\CountryInformationInterface|\Magento\Directory\Model\Data\CountryInformation
     */
    protected function getCountryInfo($id)
    {
        $countryInfo = $this->countryInformationAcquirer->getCountryInfo($id);

        return $countryInfo;
    }
}
