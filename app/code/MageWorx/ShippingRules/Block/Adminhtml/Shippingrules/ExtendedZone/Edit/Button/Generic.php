<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\ExtendedZone\Edit\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use MageWorx\ShippingRules\Model\ExtendedZone;
use Magento\Framework\App\RequestInterface;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone as ExtendedZoneController;
use MageWorx\ShippingRules\Api\ExtendedZoneRepositoryInterface;

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
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ExtendedZoneRepositoryInterface
     */
    protected $zoneRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param RequestInterface $request
     * @param ExtendedZoneRepositoryInterface $zoneRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RequestInterface $request,
        ExtendedZoneRepositoryInterface $zoneRepository
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->request = $request;
        $this->zoneRepository = $zoneRepository;
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
     * Get zone: current or empty
     *
     * @return \MageWorx\ShippingRules\Model\Carrier\Method
     */
    public function getZone()
    {
        $zone = $this->registry->registry(ExtendedZone::REGISTRY_KEY);
        if ($zone) {
            return $zone;
        }

        return $this->zoneRepository->getEmptyEntity();
    }

    /**
     * Get button additional data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        return $data;
    }
}
