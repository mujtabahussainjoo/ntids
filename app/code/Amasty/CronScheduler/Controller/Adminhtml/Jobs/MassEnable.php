<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Controller\Adminhtml\Jobs;

use Amasty\CronScheduler\Model\Jobs;
use Magento\Framework\Exception\LocalizedException;

class MassEnable extends AbstractMassActions
{
    public function runAction($collection)
    {
        $enabledJobs = 0;
        $failedJobs = 0;

        foreach ($collection->getItems() as $job) {
            try {
                if ($job->getStatus() == Jobs::STATUS_DISABLED) {
                    $job->setStatus(Jobs::STATUS_ENABLED);
                    $this->jobsRepository->save($job);
                    $enabledJobs++;
                }
            } catch (LocalizedException $e) {
                $failedJobs++;
            } catch (\Exception $e) {
                $this->logger->error($e);
                $failedJobs++;
            }
        }

        if ($enabledJobs !== 0) {
            $this->messageManager->addSuccessMessage(
                __('%1 job(s) has been successfully enabled', $enabledJobs)
            );
        }

        if ($failedJobs !== 0) {
            $this->messageManager->addErrorMessage(
                __('%1 job(s) has been failed to enable', $failedJobs)
            );
        }
    }
}
