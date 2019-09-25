<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Controller\Adminhtml\Jobs;

use Amasty\CronScheduler\Controller\Adminhtml\AbstractJobs;
use Amasty\CronScheduler\Model\Repository\JobsRepository;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends AbstractJobs
{
    /**
     * @var JobsRepository
     */
    private $jobsRepository;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    public function __construct(
        Action\Context $context,
        JobsRepository $jobsRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->jobsRepository = $jobsRepository;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $data = $this->getRequest()->getParam('items', []);
            try {
                foreach ($data as $item) {
                    $job = $this->jobsRepository->getById($item['id']);
                    if ($item['modified_schedule'] === "") {
                        $job->setModifiedSchedule($job->getSchedule());
                    } else {
                        $job->setModifiedSchedule($item['modified_schedule']);
                    }
                    $job->setStatus($item['status']);
                    $this->jobsRepository->save($job);
                }
                $messages[] = __('Changes Saved');
            } catch (\Exception $e) {
                $messages[] = "Error:" . $e->getMessage();
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
