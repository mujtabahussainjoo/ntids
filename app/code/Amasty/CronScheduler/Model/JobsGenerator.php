<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Model;

use Magento\Cron\Model\Config\Data as ConfigData;
use Amasty\CronScheduler\Model\Repository\JobsRepository;
use Amasty\CronScheduler\Model\Jobs;
use Amasty\CronScheduler\Model\JobsFactory;

class JobsGenerator
{
    /**
     * @var ConfigData
     */
    private $cronConfig;

    /**
     * @var JobsRepository
     */
    private $jobsRepository;

    /**
     * @var JobsFactory
     */
    private $jobsFactory;

    public function __construct(
        ConfigData $cronConfig,
        JobsRepository $jobsRepository,
        JobsFactory $jobsFactory
    ) {
        $this->cronConfig = $cronConfig;
        $this->jobsRepository = $jobsRepository;
        $this->jobsFactory = $jobsFactory;
    }

    public function execute()
    {
        $allJobs = $this->cronConfig->getJobs();
        $jobsFromDb = $this->jobsRepository->getAll();
        $savedJobs = [];
        $jobsToAdd = [];
        $jobsToDelete = [];

        /** @var Jobs $savedJob */
        foreach ($jobsFromDb as $savedJob) {
            $savedJobs[$savedJob->getJobCode()] = $savedJob;
        }

        foreach ($allJobs as $groupId => $jobs) {
            foreach ($jobs as $jobCode => $job) {
                if ($jobCode === "amasty_cron_activity") {
                    continue;
                }
                if (!array_key_exists($jobCode, $savedJobs)) {
                    /** @var Jobs $jobToSave */
                    $jobToSave = $this->jobsFactory->create();

                    $jobToSave->setGroup($groupId);
                    $jobToSave->setInstance($job['instance']);
                    $jobToSave->setMethod($job['method']);
                    $jobToSave->setJobCode($job['name']);
                    $jobToSave->setSchedule(isset($job['schedule']) ? $job['schedule'] : '');
                    $jobToSave->setModifiedSchedule(isset($job['schedule']) ? $job['schedule'] : '');
                    $jobToSave->setStatus(true);

                    array_push($jobsToAdd, $jobToSave);
                } elseif (isset($job['schedule']) && $job['schedule'] !== $savedJobs[$jobCode]->getSchedule()) {
                    $savedJobs[$jobCode]->setSchedule($job['schedule']);
                    array_push($jobsToAdd, $savedJobs[$jobCode]);
                }
            }

            foreach ($jobsFromDb as $job) {
                if ($job->getGroup() === $groupId && !array_key_exists($job->getJobCode(), $jobs)) {
                    array_push($jobsToDelete, $job);
                }
            }
        }

        foreach ($jobsToAdd as $newJob) {
            $this->jobsRepository->save($newJob);
        }

        foreach ($jobsToDelete as $deletedJob) {
            $this->jobsRepository->delete($deletedJob);
        }
    }
}
