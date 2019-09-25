<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Logger;

use Magento\Customer\Model\Session;
use Magento\Directory\Model\CountryInformationAcquirer;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\LayoutFactory;
use MageWorx\GeoIP\Model\Geoip;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\ShippingRules\Api\Data\ZoneInterfaceFactory;

class Index extends Action
{
    /**
     * @var \MageWorx\ShippingRules\Model\Logger
     */
    protected $logger;

    /**
     * @param Context $context
     * @param \MageWorx\ShippingRules\Model\Logger $logger
     */
    public function __construct(
        Context $context,
        \MageWorx\ShippingRules\Model\Logger $logger
    ) {
        parent::__construct($context);
        $this->logger = $logger;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $jsonResult */
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $story = $this->logger->getStory();
        $jsonResult->setData(['success' => true, 'time' => time(), 'story' => $story]);

        return $jsonResult;
    }
}
