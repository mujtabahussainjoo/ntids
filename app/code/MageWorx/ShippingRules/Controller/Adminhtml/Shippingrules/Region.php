<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use MageWorx\ShippingRules\Api\RegionRepositoryInterface;
use MageWorx\ShippingRules\Model\Region as RegionModel;
use Psr\Log\LoggerInterface;

abstract class Region extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var RegionRepositoryInterface
     */
    protected $regionRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param RegionRepositoryInterface $regionRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        RegionRepositoryInterface $regionRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->regionRepository = $regionRepository;
        $this->logger = $logger;
    }

    /**
     * Initiate region
     *
     * @return void
     */
    protected function _initRegion()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if (!$id && $this->getRequest()->getParam('entity_id')) {
            $id = (int)$this->getRequest()->getParam('entity_id');
        }

        if ($id) {
            $region = $this->regionRepository->getById($id);
        } else {
            $region = $this->regionRepository->getEmptyEntity();
        }

        $this->coreRegistry->register(
            RegionModel::CURRENT_REGION,
            $region
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
        return $this->_authorization->isAllowed('MageWorx_ShippingRules::region');
    }
}
