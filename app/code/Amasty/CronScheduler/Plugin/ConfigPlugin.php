<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Plugin;

use Amasty\CronScheduler\Model\Repository\JobsRepository;
use Amasty\CronScheduler\Model\Jobs;

class ConfigPlugin
{
    /**
     * @var JobsRepository
     */
    private $jobsRepository;

    public function __construct(
        JobsRepository $jobsRepository
    ) {
        $this->jobsRepository = $jobsRepository;
    }

    public function afterGetJobs(\Magento\Cron\Model\Config $subject, array $result)
    {
        $jobsFromDb = $this->jobsRepository->getAll();
        $savedJobs = [];

        foreach ($jobsFromDb as $savedJob) {
            $savedJobs[$savedJob->getJobCode()] = $savedJob;
        }

        foreach ($result as $group => &$jobs) {
            foreach ($jobs as &$job) {
                if (isset($job['name'])) {
                    if (array_key_exists($job['name'], $savedJobs)
                        && isset($job['schedule'])
                        && $savedJobs[$job['name']]->getModifiedSchedule() !== $job['schedule']
                        && $savedJobs[$job['name']]->getStatus() != Jobs::STATUS_DISABLED
                    ) {
                        $job['schedule'] = $savedJobs[$job['name']]->getModifiedSchedule();
                    } elseif (array_key_exists($job['name'], $savedJobs)
                        && $savedJobs[$job['name']]->getStatus() == Jobs::STATUS_DISABLED
                    ) {
                        unset($jobs[$job['name']]);
                    }
                }
            }
        }

        return $result;
    }
}
