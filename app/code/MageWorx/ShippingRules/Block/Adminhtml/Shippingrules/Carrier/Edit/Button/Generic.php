<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Carrier\Edit\Button;

use Magento\Framework\Registry;
use MageWorx\ShippingRules\Model\Carrier;
use MageWorx\ShippingRules\Api\CarrierRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Generic implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * @var CarrierRepositoryInterface
     */
    protected $carrierRepository;

    /**
     * Generic constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param CarrierRepositoryInterface $carrierRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CarrierRepositoryInterface $carrierRepository
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        /** @var Context $context */
        $context = $this->context;
        /** @var string $url */
        $url = $context->getUrl($route, $params);

        return $url;
    }

    /**
     * Get carrier from registry or create new (using repository)
     *
     * @return \MageWorx\ShippingRules\Model\Carrier
     */
    public function getCarrier()
    {
        $carrier = $this->registry->registry(Carrier::CURRENT_CARRIER);
        if ($carrier) {
            return $carrier;
        }

        return $this->carrierRepository->getEmptyEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }
}
