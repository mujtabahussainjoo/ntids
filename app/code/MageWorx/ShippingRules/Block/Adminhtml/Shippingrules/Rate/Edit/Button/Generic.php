<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Rate\Edit\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use MageWorx\ShippingRules\Model\Carrier\Method\Rate;
use Magento\Framework\App\RequestInterface;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate as RateController;
use MageWorx\ShippingRules\Api\RateRepositoryInterface;

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
     * @var RateRepositoryInterface
     */
    protected $rateRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param RequestInterface $request
     * @param RateRepositoryInterface $rateRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RequestInterface $request,
        RateRepositoryInterface $rateRepository
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->request = $request;
        $this->rateRepository = $rateRepository;
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
     * Get rate: current or empty
     *
     * @return \MageWorx\ShippingRules\Model\Carrier\Method\Rate
     */
    public function getRate()
    {
        $registry = $this->registry;
        $rate = $registry->registry(Rate::CURRENT_RATE);
        if ($rate) {
            return $rate;
        }

        return $this->rateRepository->getEmptyEntity();
    }

    /**
     * Check is need to return admin to the method edit controller
     *
     * @return bool
     */
    public function isBackToMethod()
    {
        $backTo = $this->request->getParam(RateController::BACK_TO_PARAM);
        if ($backTo && $backTo == RateController::BACK_TO_METHOD_PARAM) {
            return true;
        }

        return false;
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
