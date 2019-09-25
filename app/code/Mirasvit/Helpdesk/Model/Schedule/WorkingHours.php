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



namespace Mirasvit\Helpdesk\Model\Schedule;


/**
 * Class WorkingHours
 * @package Mirasvit\Helpdesk\Model\Schedule
 * @method int getWeekdayId()
 * @method $this setWeekdayId(int $param)
 * @method string getTimeFrom()
 * @method $this setTimeFrom(string $param)
 * @method string getTimeTo()
 * @method $this setTimeTo(string $param)
 */
class WorkingHours extends \Magento\Framework\DataObject
{
    /**
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->localeLists = $localeLists;
        $this->localeDate = $localeDate;
    }

    /**
     * @return string
     */
    public function getTimeFromLocalized()
    {
        $dateFrom = $this->getTimeFrom();

        return $this->localeDate->formatDateTime(
            new \DateTime($dateFrom),
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::SHORT,
            null,
            'UTC'
        );
    }

    /**
     * @return string
     */
    public function getTimeToLocalized()
    {
        $dateTo = $this->getTimeTo();

        return $this->localeDate->formatDateTime(
            new \DateTime($dateTo),
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::SHORT,
            null,
            'UTC'
        );
    }

    /**
     * @return string
     */
    public function getWorkingTime()
    {
        $workingTime = __('Closed');
        if (!$this->isClosed()) {
            $workingTime = $this->getTimeFromLocalized() . ' - ' . $this->getTimeToLocalized();
        } elseif ($this->getType() == \Mirasvit\Helpdesk\Model\Config::SCHEDULE_TYPE_ALWAYS) {
            $workingTime = __('Opened 24hr');
        }

        return $workingTime;
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return !$this->getTimeTo() || !$this->getTimeFrom();
    }

    /**
     * @return string
     */
    public function getWeekdayLocalized()
    {
        $days = $this->localeLists->getOptionWeekdays();
        foreach ($days as $day) {
            if ($day['value'] == $this->getWeekdayId()) {
                return $day['label'];
            }
        }
    }
}