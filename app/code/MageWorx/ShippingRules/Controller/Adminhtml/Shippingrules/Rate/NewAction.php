<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use MageWorx\ShippingRules\Api\RateRepositoryInterface;
use MageWorx\ShippingRules\Api\MethodRepositoryInterface;
use Psr\Log\LoggerInterface;

class NewAction extends \MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param RateRepositoryInterface $rateRepository
     * @param MethodRepositoryInterface $methodRepository
     * @param LoggerInterface $logger
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        Date $dateFilter,
        RateRepositoryInterface $rateRepository,
        MethodRepositoryInterface $methodRepository,
        LoggerInterface $logger,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $dateFilter,
            $rateRepository,
            $methodRepository,
            $logger
        );
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * New action
     *
     * @return void
     * @throws LocalizedException
     */
    public function execute()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->setPageSize(1)
            ->setCurrentPage(1)
            ->create();
        $methods = $this->methodRepository->getList($searchCriteria);
        if ($methods->getTotalCount() < 1) {
            $this->messageManager->addErrorMessage(__('You must create at least one shipping method before you are able to add new rate.'));
            $this->_redirect('mageworx_shippingrules/*');
        } else {
            $this->_forward('edit');
        }
    }
}
