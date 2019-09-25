<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Region\Edit\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use MageWorx\ShippingRules\Model\Region;
use Magento\Framework\App\RequestInterface;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Region as RegionController;
use MageWorx\ShippingRules\Model\RegionFactory;

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
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param RequestInterface $request
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RequestInterface $request,
        RegionFactory $regionFactory
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->request = $request;
        $this->regionFactory = $regionFactory;
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
     * Get region: current or empty
     *
     * @return \MageWorx\ShippingRules\Model\Region
     */
    public function getRegion()
    {
        $registry = $this->registry;
        $region = $registry->registry(Region::CURRENT_REGION);
        if (!$region) {
            $region = $this->regionFactory->create();
        }

        return $region;
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
