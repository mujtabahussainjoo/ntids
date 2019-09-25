<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Model;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Cron
{
    /**
     * @var \Mirasvit\Helpdesk\Model\GatewayFactory
     */
    protected $gatewayFactory;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory
     */
    //protected $scheduleCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory
     */
    protected $gatewayCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Email\CollectionFactory
     */
    protected $emailCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Ruleevent
     */
    protected $helpdeskRuleevent;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Followup
     */
    protected $helpdeskFollowup;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Fetch
     */
    protected $helpdeskFetch;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Email
     */
    protected $helpdeskEmail;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param GatewayFactory                              $gatewayFactory
     * @param ResourceModel\Ticket\CollectionFactory      $ticketCollectionFactory
     * @param ResourceModel\Gateway\CollectionFactory     $gatewayCollectionFactory
     * @param ResourceModel\Email\CollectionFactory       $emailCollectionFactory
     * @param Config                                      $config
     * @param \Mirasvit\Helpdesk\Helper\Ruleevent         $helpdeskRuleevent
     * @param \Mirasvit\Helpdesk\Helper\Followup          $helpdeskFollowup
     * @param \Mirasvit\Helpdesk\Helper\Fetch             $helpdeskFetch
     * @param \Mirasvit\Helpdesk\Helper\Email             $helpdeskEmail
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Filesystem               $filesystem
     * @param \Psr\Log\LoggerInterface                    $logger
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        GatewayFactory $gatewayFactory,
        ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        ResourceModel\Gateway\CollectionFactory $gatewayCollectionFactory,
        ResourceModel\Email\CollectionFactory $emailCollectionFactory,
        Config $config,
        \Mirasvit\Helpdesk\Helper\Ruleevent $helpdeskRuleevent,
        \Mirasvit\Helpdesk\Helper\Followup $helpdeskFollowup,
        \Mirasvit\Helpdesk\Helper\Fetch $helpdeskFetch,
        \Mirasvit\Helpdesk\Helper\Email $helpdeskEmail,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Filesystem $filesystem,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->gatewayCollectionFactory = $gatewayCollectionFactory;
        $this->emailCollectionFactory = $emailCollectionFactory;
        $this->config = $config;
        $this->helpdeskRuleevent = $helpdeskRuleevent;
        $this->helpdeskFollowup = $helpdeskFollowup;
        $this->helpdeskFetch = $helpdeskFetch;
        $this->helpdeskEmail = $helpdeskEmail;
        $this->date = $date;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * @var null
     */
    protected $_lockFile = null;

    /**
     * @var bool
     */
    protected $_fast = false;

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     *
     */
    public function magentoCronEveryHourRun()
    {
        $this->helpdeskRuleevent->newEventCheck(Config::RULE_EVENT_CRON_EVERY_HOUR);

        //        //kill long running processes
        //        $tasks = $this->scheduleCollectionFactory->create()
        //            ->addFieldToFilter('status', \Magento\Cron\Model\Schedule::STATUS_RUNNING)
        //            ->addFieldToFilter('job_code', 'mirasvit_helpdesk')
        //            ->load();
        //
        //        foreach ($tasks as $task) {
        //            $date = $task->getCreatedAt();
        //            $time = strtotime($date);
        //            $diff = $this->date->timestamp() - $time;
        //            if ($diff > 3600) {
        //                $task->delete();
        //            }
        //        }
    }

    /**
     *
     */
    public function magentoCronRun()
    {
        //        echo "RUN CRON\n";
        if ($this->getConfig()->getGeneralIsDefaultCron()) {
            $this->run();
        }
    }

    /**
     *
     */
    public function shellCronRun()
    {
        $this->run();
    }

    /**
     * @param bool $fast
     *
     * @return void
     */
    public function setFast($fast)
    {
        $this->_fast = $fast;
    }

    /**
     *
     */
    public function run()
    {
        @set_time_limit(60 * 30); //30 min. we need this. otherwise script can hang out.
        if (!$this->isLocked() || $this->_fast) {
            $this->lock();

            $this->fetchEmails();
            $this->processEmails();
            $this->runFollowUp();

            $this->unlock();
        } else {
            echo 'Process is locked.';
        }
    }

    /**
     *
     */
    public function runFollowUp()
    {
        $collection = $this->ticketCollectionFactory->create()
            ->addFieldToFilter(
                'fp_execute_at',
                ['lteq' => (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)]
            );
        foreach ($collection as $ticket) {
            $this->helpdeskFollowup->process($ticket);
        }
    }

    /**
     *
     */
    public function fetchEmails()
    {
        $gateways = $this->gatewayCollectionFactory->create()
            ->addFieldToFilter('is_active', true);
        foreach ($gateways as $gateway) {
            $timeNow = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            if (!$this->_fast) {
                if (strtotime($timeNow) - strtotime($gateway->getFetchedAt()) < $gateway->getFetchFrequency() * 60) {
                    continue;
                }
            }
            $message = __('Success');
            try {
                $this->helpdeskFetch->fetch($gateway);
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $this->logger->error("Can't connect to gateway {$gateway->getName()}. " . $e->getMessage());
            }
            //нам нужно загрузить гейтвей еще раз,
            // т.к. его данные могли измениться пока идет фетч
            $gateway = $this->gatewayFactory->create()->load($gateway->getId());
            $gateway->setLastFetchResult($message)
                ->setFetchedAt($timeNow)
                ->save();
        }
    }

    /**
     *
     */
    public function processEmails()
    {
        $emails = $this->emailCollectionFactory->create()
            ->addFieldToFilter('is_processed', false);
        foreach ($emails as $email) {
            $this->helpdeskEmail->processEmail($email);
        }
    }

    /**
     * Возвращает файл лока.
     *
     * @return resource
     */
    protected function _getLockFile()
    {
        if ($this->_lockFile === null) {
            $varDir = $this->filesystem
                ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::TMP)
                ->getAbsolutePath();
            if (!file_exists($varDir)) {
                @mkdir($varDir, 0777, true);
            }
            $file = $varDir . '/helpdesk.lock';

            if (is_file($file)) {
                $this->_lockFile = fopen($file, 'w');
            } else {
                $this->_lockFile = fopen($file, 'x');
            }
            fwrite($this->_lockFile, date('r'));
        }

        return $this->_lockFile;
    }

    /**
     * Лочим файл, любой другой php процесс может узнать
     * что файл залочен.
     * Если процесс упал, файл разлочиться.
     *
     * @return object
     */
    public function lock()
    {
        flock($this->_getLockFile(), LOCK_EX | LOCK_NB);

        return $this;
    }

    /**
     * Lock and block process.
     * If new instance of the process will try validate locking state
     * script will wait until process will be unlocked.
     *
     * @return $this
     */
    public function lockAndBlock()
    {
        flock($this->_getLockFile(), LOCK_EX);

        return $this;
    }

    /**
     * Разлочит файл.
     *
     * @return object
     */
    public function unlock()
    {
        flock($this->_getLockFile(), LOCK_UN);

        return $this;
    }

    /**
     * Проверяет, залочен ли файл.
     *
     * @return bool
     */
    public function isLocked()
    {
        $fp = $this->_getLockFile();
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            flock($fp, LOCK_UN);

            return false;
        }

        return true;
    }

    /**
     *
     */
    public function __destruct()
    {
        if ($this->_lockFile) {
            fclose($this->_lockFile);
        }
    }
}
