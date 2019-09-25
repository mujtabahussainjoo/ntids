<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Method\Edit\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use MageWorx\ShippingRules\Model\Carrier\Method;
use Magento\Framework\App\RequestInterface;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Method as MethodController;
use MageWorx\ShippingRules\Api\MethodRepositoryInterface;

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
     * @var MethodRepositoryInterface
     */
    protected $methodRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param RequestInterface $request
     * @param MethodRepositoryInterface $methodRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RequestInterface $request,
        MethodRepositoryInterface $methodRepository
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->request = $request;
        $this->methodRepository = $methodRepository;
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
     * Get method: current or empty
     *
     * @return \MageWorx\ShippingRules\Model\Carrier\Method
     */
    public function getMethod()
    {
        $registry = $this->registry;
        $method = $registry->registry(Method::CURRENT_METHOD);
        if ($method) {
            return $method;
        }

        return $this->methodRepository->getEmptyEntity();
    }

    /**
     * Check is need to return admin to the carrier edit controller
     *
     * @return bool
     */
    public function isBackToCarrier()
    {
        $backTo = $this->request->getParam(MethodController::BACK_TO_PARAM);
        if ($backTo && $backTo == MethodController::BACK_TO_CARRIER_PARAM) {
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
