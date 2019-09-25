<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Controller\Adminhtml\Jobs;

use Magento\Framework\Exception\LocalizedException;

class MassDelete extends AbstractMassActions
{
    public function runAction($collection)
    {
        $deletedJobs = 0;
        $failedJobs = 0;

        foreach ($collection->getItems() as $job) {
            try {
                $this->jobsRepository->delete($job);
                $deletedJobs++;
            } catch (LocalizedException $e) {
                $failedJobs++;
            } catch (\Exception $e) {
                $this->logger->error($e);
                $failedJobs++;
            }
        }

        if ($deletedJobs !== 0) {
            $this->messageManager->addSuccessMessage(
                __('%1 job(s) has been successfully deleted', $deletedJobs)
            );
        }

        if ($failedJobs !== 0) {
            $this->messageManager->addErrorMessage(
                __('%1 job(s) has been failed to delete', $failedJobs)
            );
        }
    }
}
