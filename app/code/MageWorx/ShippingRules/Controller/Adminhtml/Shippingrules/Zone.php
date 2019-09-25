<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules;

use Magento\Backend\App\Action;
use MageWorx\ShippingRules\Model\Zone as ZoneModel;

abstract class Zone extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \MageWorx\ShippingRules\Model\ZoneFactory
     */
    protected $zoneFactory;

    /**
     * @var \MageWorx\ShippingRules\Api\ZoneRepositoryInterface
     */
    protected $zoneRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \MageWorx\ShippingRules\Api\ZoneRepositoryInterface $zoneRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \MageWorx\ShippingRules\Api\ZoneRepositoryInterface $zoneRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->zoneRepository = $zoneRepository;
        $this->logger = $logger;
    }

    /**
     * Initiate zone
     *
     * @return void
     */
    protected function _initZone()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if (!$id && $this->getRequest()->getParam('entity_id')) {
            $id = (int)$this->getRequest()->getParam('entity_id');
        }

        if ($id) {
            $zone = $this->zoneRepository->getById($id);
        } else {
            $zone = $this->zoneRepository->getEmptyEntity();
        }

        $this->coreRegistry->register(
            ZoneModel::CURRENT_ZONE,
            $zone
        );
    }

    /**
     * Initiate action
     *
     * @return Zone
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();

        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_ShippingRules::zone');
    }
}
