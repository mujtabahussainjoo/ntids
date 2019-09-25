<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use MageWorx\ShippingRules\Api\MethodRepositoryInterface;
use Psr\Log\LoggerInterface;

abstract class Method extends Action
{
    const BACK_TO_PARAM = 'back_to';
    const BACK_TO_CARRIER_PARAM = 'to_carrier';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Date
     */
    protected $dateFilter;

    /**
     * @var MethodRepositoryInterface
     */
    protected $methodRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param MethodRepositoryInterface $methodRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        Date $dateFilter,
        MethodRepositoryInterface $methodRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->fileFactory = $fileFactory;
        $this->dateFilter = $dateFilter;
        $this->methodRepository = $methodRepository;
        $this->logger = $logger;
    }

    /**
     * Initiate method
     *
     * @return void
     */
    protected function _init()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if (!$id && $this->getRequest()->getParam('entity_id')) {
            $id = (int)$this->getRequest()->getParam('entity_id');
        }

        if ($id) {
            $method = $this->methodRepository->getById($id);
        } else {
            $method = $this->methodRepository->getEmptyEntity();
        }

        $this->coreRegistry->register(
            \MageWorx\ShippingRules\Model\Carrier\Method::CURRENT_METHOD,
            $method
        );
    }

    /**
     * Initiate action
     *
     * @return Quote
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('MageWorx_ShippingRules::shippingrules_carrier')
            ->_addBreadcrumb(__('Carriers'), __('Carriers'));

        return $this;
    }

    /**
     * Check: whether it is necessary to redirect the administrator to the carrier-edit page
     *
     * @param array $data
     * @return bool
     */
    public function isBackToCarrier($data = [])
    {
        if (($this->getRequest()->getParam(static::BACK_TO_PARAM) &&
            $this->getRequest()->getParam(static::BACK_TO_PARAM == static::BACK_TO_CARRIER_PARAM))) {
            return true;
        }

        if (isset($data[static::BACK_TO_PARAM]) && $data[static::BACK_TO_PARAM] == static::BACK_TO_CARRIER_PARAM) {
            return true;
        }

        return false;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_ShippingRules::carrier');
    }
}
