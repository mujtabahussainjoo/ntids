<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Amasty\CronScheduleList\Model\ScheduleCollectionFactory as CollectionFactory;

class Timeline extends Template
{
    protected $_template = 'Amasty_CronScheduler::timeline.phtml';

    /**
     * @var CollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var Data
     */
    private $jsonHelper;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        CollectionFactory $scheduleCollectionFactory,
        Data $jsonHelper,
        Template\Context $context,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->jsonHelper = $jsonHelper;
        $this->timezone = $timezone;
    }

    public function getJobsJson()
    {
        $items = $this->scheduleCollectionFactory->create()->getData();

        return $this->jsonHelper->jsonEncode($items);
    }

    public function getServerTimeDifference()
    {
        return $this->timezone->date()->format('P');
    }

    protected function _prepareLayout()
    {
        $this->getToolbar()->addChild(
            'amasty_run_cron_timeline',
            'Magento\Backend\Block\Widget\Button',
            [
                'label'   => __('Run Cron'),
                'title'   => __('Run Cron'),
                'onclick' => 'setLocation(\'' . $this->getUrl(
                        'amasty_cronscheduler/timeline/runJobs'
                    ) . '\')',
                'class'   => 'action-default primary'
            ]
        );

        return parent::_prepareLayout();
    }
}
