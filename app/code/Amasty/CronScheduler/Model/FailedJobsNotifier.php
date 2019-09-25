<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Model;

use Amasty\CronScheduler\Model\ConfigProvider;
use Amasty\CronScheduler\Model\EmailSender;
use Amasty\CronScheduler\Model\Repository\JobsRepository;
use Amasty\CronScheduleList\Model\ScheduleCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

class FailedJobsNotifier
{
    /**
     * @var \Amasty\CronScheduler\Model\ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Amasty\CronScheduler\Model\EmailSender
     */
    private $emailSender;

    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var JobsRepository
     */
    private $jobsRepository;

    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(
        ConfigProvider $configProvider,
        EmailSender $emailSender,
        ScheduleCollectionFactory $scheduleCollectionFactory,
        JobsRepository $jobsRepository,
        DateTime $dateTime
    ) {
        $this->configProvider = $configProvider;
        $this->emailSender = $emailSender;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->jobsRepository = $jobsRepository;
        $this->dateTime = $dateTime;
    }

    public function updateFailedJobs()
    {
        if (!$this->configProvider->getIsEnabled()) {
            return;
        }
        $interval = $this->configProvider->getNotificationInterval() * 60;
        $allFailedSchedules = $this->scheduleCollectionFactory->create()->getLastFailedJobs();

        /** @var \Magento\Cron\Model\Schedule $failedSchedule */
        foreach ($allFailedSchedules as $failedSchedule) {
            $failedJob = $this->jobsRepository->getByCode($failedSchedule->getJobCode());

            if ($failedJob === null) {
                continue;
            }
            $failedScheduleTime = $this->dateTime->gmtTimestamp($failedSchedule->getExecutedAt());
            $failedJobTime = $this->dateTime->gmtTimestamp($failedJob->getFailTime());

            if ($failedJob->getWasNotified() === null) {
                $this->emailSender->sendEmail($failedSchedule);
                $failedJob->setWasNotified(true);
                $failedJob->setFailTime($failedScheduleTime);
                $this->jobsRepository->save($failedJob);
            }

            if ((bool)$failedJob->getWasNotified() === true && $failedScheduleTime > $failedJobTime) {
                $failedJob->setFailTime($failedScheduleTime);
                $failedJob->setWasNotified(false);
                $this->jobsRepository->save($failedJob);
            }
        }

        /** @var \Amasty\CronScheduler\Model\Jobs $job */
        foreach ($this->jobsRepository->getAllFailed() as $job) {
            if ((bool)$job->getWasNotified() === false
                && $this->dateTime->gmtTimestamp() - $interval > $this->dateTime->gmtTimestamp($job->getFailTime())
            ) {
                $this->emailSender->sendEmail(
                    $this->scheduleCollectionFactory->create()
                        ->getFailedScheduleByJobCode($job->getJobCode())
                );
                $job->setWasNotified(true);
                $this->jobsRepository->save($job);
            }
        }
    }
}
