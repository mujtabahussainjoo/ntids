<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Controller\Adminhtml\Jobs;

use Amasty\CronScheduler\Controller\Adminhtml\AbstractJobs;
use Amasty\CronScheduler\Model\Repository\JobsRepository;
use Amasty\CronScheduler\Model\ResourceModel\Jobs\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

abstract class AbstractMassActions extends AbstractJobs
{
    /**
     * @var JobsRepository
     */
    protected $jobsRepository;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        JobsRepository $jobsRepository,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->jobsRepository = $jobsRepository;
        $this->filter = $filter;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider();

        $collection = $this->filter->getCollection($this->collectionFactory->create());

        if ($collection->count() > 0) {
            $this->runAction($collection);
        }

        $this->_redirect($this->_redirect->getRefererUrl());
    }

    abstract public function runAction($collection);
}
