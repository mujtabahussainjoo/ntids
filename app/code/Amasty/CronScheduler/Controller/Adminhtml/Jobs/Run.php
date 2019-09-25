<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Controller\Adminhtml\Jobs;

use Amasty\CronScheduler\Controller\Adminhtml\AbstractJobs;
use Amasty\CronScheduler\Model\Jobs;
use Amasty\CronScheduler\Model\Repository\JobsRepository;
use Magento\Cron\Model\ScheduleFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Profiler\Driver\Standard\Stat;
use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action;
use Magento\Cron\Model\Schedule;

class Run extends AbstractJobs
{
    /**
     * @var JobsRepository
     */
    private $jobsRepository;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var ScheduleFactory
     */
    private $scheduleFactory;

    /**
     * @var Stat
     */
    private $statProfiler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Action\Context $context,
        JobsRepository $jobsRepository,
        ScheduleFactory $scheduleFactory,
        DateTime $dateTime,
        Stat $statProfiler,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->jobsRepository = $jobsRepository;
        $this->dateTime = $dateTime;
        $this->scheduleFactory = $scheduleFactory;
        $this->statProfiler = $statProfiler;
        $this->logger = $logger;
    }

    public function execute()
    {
        $jobId = $this->getRequest()->getParam('id');
        $job = $this->jobsRepository->getById($jobId);

        if ($job->getStatus() == Jobs::STATUS_DISABLED) {
            $this->messageManager->addErrorMessage(__('Disabled job can\'t be runed'));
            $this->_redirect($this->_redirect->getRefererUrl());

            return;
        }
        $jobCode = $job->getJobCode();
        $schedule = $this->createScheduleItem($job);
        $jobConfig = [
            'instance' => $job->getInstance(),
            'method' => $job->getMethod(),
            'name' => $jobCode,
            'schedule' => $job->getModifiedSchedule()
        ];
        $model = $this->_objectManager->create($jobConfig['instance']);
        $callback = [$model, $jobConfig['method']];

        if (!is_callable($callback)) {
            $schedule->setStatus(Schedule::STATUS_ERROR);
            throw new \Exception(
                sprintf('Invalid callback: %s::%s can\'t be called', $jobConfig['instance'], $jobConfig['method'])
            );
        }
        $this->startProfiling();

        try {
            $this->logger->info(sprintf('Cron Job %s is run', $jobCode));
            call_user_func_array($callback, [$schedule]);
        } catch (\Throwable $e) {
            $schedule->setStatus(Schedule::STATUS_ERROR);
            $this->logger->error(sprintf(
                'Cron Job %s has an error: %s. Statistics: %s',
                $jobCode,
                $e->getMessage(),
                $this->getProfilingStat()
            ));

            if (!$e instanceof \Exception) {
                $e = new \RuntimeException(
                    'Error when running a cron job',
                    0,
                    $e
                );
            }
            throw $e;
        } finally {
            $this->stopProfiling();
        }
        $schedule->setStatus(Schedule::STATUS_SUCCESS)->setFinishedAt(strftime(
            '%Y-%m-%d %H:%M:%S',
            $this->dateTime->gmtTimestamp()
        ))->save();

        $this->logger->info(sprintf(
            'Cron Job %s is successfully finished. Statistics: %s',
            $jobCode,
            $this->getProfilingStat()
        ));
        $this->messageManager->addSuccessMessage(__('The job has been run'));
        
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @param \Amasty\CronScheduler\Model\Jobs $job
     *
     * @return \Magento\Cron\Model\Schedule
     */
    private function createScheduleItem($job)
    {
        $currentDateTime = $this->dateTime->date();
        $schedule = $this->scheduleFactory->create();
        $schedule->setMessages(null);
        $schedule->setJobCode($job->getJobCode());
        $schedule->setStatus(Schedule::STATUS_RUNNING);
        $schedule->setCreatedAt($currentDateTime);
        $schedule->setScheduledAt($currentDateTime);
        $schedule->setExecutedAt($currentDateTime);
        $schedule->save();

        return $schedule;
    }

    /**
     * Starts profiling
     *
     * @return void
     */
    private function startProfiling()
    {
        $this->statProfiler->clear();
        $this->statProfiler->start('job', microtime(true), memory_get_usage(true), memory_get_usage());
    }

    /**
     * Stops profiling
     *
     * @return void
     */
    private function stopProfiling()
    {
        $this->statProfiler->stop('job', microtime(true), memory_get_usage(true), memory_get_usage());
    }

    /**
     * Retrieves statistics in the JSON format
     *
     * @return string
     */
    private function getProfilingStat()
    {
        $stat = $this->statProfiler->get('job');
        unset($stat[Stat::START]);
        return json_encode($stat);
    }
}
