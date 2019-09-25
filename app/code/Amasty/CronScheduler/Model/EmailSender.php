<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Model;

use Amasty\CronScheduleList\Model\ScheduleCollection;
use Amasty\CronScheduler\Model\ConfigProvider;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Store\Model\StoreManagerInterface;

class EmailSender
{
    const JOB_CODE_VAR = '{{job_code}}';

    const EXECUTED_AT_VAR = '{{executed_at}}';

    const MESSAGE_VAR = '{{message}}';

    /**
     * @var ScheduleCollection
     */
    private $scheduleCollection;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var TemplateInterface
     */
    private $template;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ScheduleCollection $scheduleCollection,
        ConfigProvider $configProvider,
        TransportBuilder $transportBuilder,
        TemplateInterface $template,
        StoreManagerInterface $storeManager
    ) {
        $this->scheduleCollection = $scheduleCollection;
        $this->configProvider = $configProvider;
        $this->transportBuilder = $transportBuilder;
        $this->template = $template;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Cron\Model\Schedule $jobSchedule
     */
    public function sendEmail($jobSchedule)
    {
        if (!$this->configProvider->getIsEnabled()) {
            return;
        }
        $senderEmail = $this->configProvider->getSenderEmail();
        $subject = $this->formatVariables($this->configProvider->getEmailSubject(), $jobSchedule);
        $recievers = preg_split('/\n|\r\n?/', $this->configProvider->getRecievers());
        $emailContent = $this->formatVariables($this->configProvider->getEmailContent(), $jobSchedule);

        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
            'store' => $this->storeManager->getStore()
        ];
        $templateVars = [
            'emailContent' => $emailContent,
            'subject' => $subject
        ];
        $to = $recievers;
        $transport = $this->transportBuilder->setTemplateIdentifier('cron_error_template')
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($senderEmail)
            ->addTo($to)
            ->getTransport();
        $transport->sendMessage();
    }

    /**
     * @param string $string
     * @param \Magento\Cron\Model\Schedule $schedule
     *
     * @return string
     */
    private function formatVariables($string, $schedule)
    {
        $string = str_replace(self::JOB_CODE_VAR, $schedule->getJobCode(), $string);
        $string = str_replace(self::MESSAGE_VAR, $schedule->getMessages(), $string);
        $string = str_replace(self::EXECUTED_AT_VAR, $schedule->getExecutedAt(), $string);

        return $string;
    }
}
