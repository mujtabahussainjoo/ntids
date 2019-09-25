<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Controller\Adminhtml\Jobs;

use Amasty\CronScheduler\Controller\Adminhtml\AbstractJobs;
use Magento\Framework\Controller\ResultFactory;

class Index extends AbstractJobs
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_CronScheduler::jobs_scheduler');
        $resultPage->getConfig()->getTitle()->prepend(__('Cron Jobs'));
        $resultPage->addBreadcrumb(__('Cron Jobs'), __('Cron Jobs'));

        return $resultPage;
    }
}
