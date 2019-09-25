<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Model\Repository;

use Amasty\CronScheduler\Api\JobsRepositoryInterface;
use Amasty\CronScheduler\Api\Data\JobsInterface;
use Amasty\CronScheduler\Model\Jobs;
use Amasty\CronScheduler\Model\JobsFactory;
use Amasty\CronScheduler\Model\ResourceModel\Jobs as JobsResource;
use Amasty\CronScheduler\Model\ResourceModel\Jobs\Collection;
use Amasty\CronScheduler\Model\ResourceModel\Jobs\CollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class JobsRepository implements JobsRepositoryInterface
{
    /**
     * @var JobsFactory
     */
    private $jobsFactory;

    /**
     * @var JobsResource
     */
    private $jobsResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Model data storage
     *
     * @var array
     */
    private $jobs;

    public function __construct(
        JobsFactory $jobsFactory,
        JobsResource $jobsResource,
        CollectionFactory $collectionFactory
    ) {
        $this->jobsFactory = $jobsFactory;
        $this->jobsResource = $jobsResource;
        $this->collectionFactory = $collectionFactory;
    }

    public function save(JobsInterface $job)
    {
        try {
            if ($job->getId()) {
                $job = $this->getById($job->getId())
                    ->addData($job->getData());
            }
            $this->jobsResource->save($job);
            unset($this->jobs[$job->getId()]);
        } catch (\Exception $e) {
            if ($job->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save job with ID %1. Error: %2',
                        [$job->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save job. Error: %1', $e->getMessage()));
        }

        return $job;
    }

    public function getById($id)
    {
        if (!isset($this->jobs[$id])) {
            /** @var \Amasty\CronScheduler\Model\Jobs $job */
            $job = $this->jobsFactory->create();
            $this->jobsResource->load($job, $id);

            if (!$job->getId()) {
                throw new NoSuchEntityException(__('Job with specified ID "%1" not found.', $id));
            }
            $this->jobs[$id] = $job;
        }

        return $this->jobs[$id];
    }

    public function delete(JobsInterface $job)
    {
        try {
            $this->jobsResource->delete($job);
            unset($this->jobs[$job->getId()]);
        } catch (\Exception $e) {
            if ($job->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove job with ID %1. Error: %2',
                        [$job->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove job. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function deleteById($id)
    {
        $job = $this->getById($id);

        $this->delete($job);
    }

    public function getByCode($code)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        /** @var Jobs $job */
        $job = $collection->addFieldToFilter(JobsInterface::CODE, $code)->getFirstItem();

        if ($job->getId()) {
            return $job;
        }

        return null;
    }

    public function getAll()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        return $collection->getItems();
    }

    public function getAllFailed()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->getSelect()->where(JobsInterface::FAIL_TIME . ' IS NOT NULL');

        return $collection->getItems();
    }
}
