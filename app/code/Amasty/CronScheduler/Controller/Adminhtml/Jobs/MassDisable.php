<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Controller\Adminhtml\Jobs;

use Amasty\CronScheduler\Model\Jobs;
use Magento\Framework\Exception\LocalizedException;

class MassDisable extends AbstractMassActions
{
    public function runAction($collection)
    {
        $disabledJobs = 0;
        $failedJobs = 0;

        foreach ($collection->getItems() as $job) {
            try {
                if ($job->getStatus() == Jobs::STATUS_ENABLED) {
                    $job->setStatus(Jobs::STATUS_DISABLED);
                    $this->jobsRepository->save($job);
                    $disabledJobs++;
                }
            } catch (LocalizedException $e) {
                $failedJobs++;
            } catch (\Exception $e) {
                $this->logger->error($e);
                $failedJobs++;
            }
        }

        if ($disabledJobs !== 0) {
            $this->messageManager->addSuccessMessage(
                __('%1 job(s) has been successfully disabled', $disabledJobs)
            );
        }

        if ($failedJobs !== 0) {
            $this->messageManager->addErrorMessage(
                __('%1 job(s) has been failed to disable', $failedJobs)
            );
        }
    }
}
