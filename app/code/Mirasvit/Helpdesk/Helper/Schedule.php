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



namespace Mirasvit\Helpdesk\Helper;

class Schedule
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ScheduleFactory
     */
    protected $scheduleFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $localeLists;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $workingDays = [];

    /**
     * @param \Mirasvit\Helpdesk\Model\Config                      $config
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Locale\ListsInterface             $localeLists
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager
     * @param \Mirasvit\Helpdesk\Model\ScheduleFactory             $scheduleFactory
     * @param \Magento\Framework\App\Helper\Context                $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mirasvit\Helpdesk\Model\ScheduleFactory $scheduleFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->config = $config;
        $this->localeDate = $localeDate;
        $this->localeLists = $localeLists;
        $this->storeManager = $storeManager;
        $this->scheduleFactory = $scheduleFactory;
        $this->context = $context;
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Schedule
     */
    public function getCurrentSchedule()
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var \Mirasvit\Helpdesk\Model\ResourceModel\Schedule\Collection $collection */
        $collection = $this->scheduleFactory->create()->getCollection()->addStoreFilter($storeId);
        $collection->addCurrentFilter();
        if ($collection->count()) {
            return $collection->getLastItem();
        }
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Schedule[]
     */
    public function getUpcomingScheduleCollection()
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var \Mirasvit\Helpdesk\Model\ResourceModel\Schedule\Collection $collection */
        $collection = $this->scheduleFactory->create()->getCollection()->addStoreFilter($storeId);
        $collection->addCurrentScheduleFilter($this->getConfig()->getScheduleShowHolidayScheduleBeforeDays())
                    ->setOrder('sort_order', \Magento\Framework\Data\Collection::SORT_ORDER_DESC);
        if ($currentSchedule = $this->getCurrentSchedule()) {
            $collection->addFieldToFilter('schedule_id', ['neq' => $currentSchedule->getId()]);
        }
        return $collection;
    }

    /**
     * @return array
     */
    public function getWeekDays()
    {
        if (!$this->workingDays) {
            $this->workingDays = [];

            $days = $this->localeLists->getOptionWeekdays();
            foreach ($days as $day) {
                $this->workingDays[$day['value']] = $day['label'];
            }
        }

        return $this->workingDays;
    }
}
