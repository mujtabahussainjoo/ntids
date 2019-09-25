<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use MageWorx\ShippingRules\Api\ExtendedZoneRepositoryInterface;
use MageWorx\ShippingRules\Model\ExtendedZone as ExtendedZoneModel;
use Psr\Log\LoggerInterface;

abstract class ExtendedZone extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var ExtendedZoneRepositoryInterface
     */
    protected $zoneRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ExtendedZoneRepositoryInterface $zoneRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ExtendedZoneRepositoryInterface $zoneRepository,
        LoggerInterface $logger
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
        if ($id) {
            $zone = $this->zoneRepository->getById($id);
        } else {
            $zone = $this->zoneRepository->getEmptyEntity();
        }

        $this->coreRegistry->register(
            ExtendedZoneModel::REGISTRY_KEY,
            $zone
        );
    }

    /**
     * Initiate action
     *
     * @return ExtendedZone
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
        return $this->_authorization->isAllowed('MageWorx_ShippingRules::extended_zones');
    }
}
