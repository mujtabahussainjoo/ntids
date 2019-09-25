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

class Report extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Helpdesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Html
     */
    protected $helpdeskHtml;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @param \Mirasvit\Helpdesk\Model\DepartmentFactory           $departmentFactory
     * @param \Mirasvit\Helpdesk\Model\Config                      $config
     * @param \Magento\Framework\Stdlib\DateTime\DateTime          $date
     * @param \Mirasvit\Helpdesk\Helper\Html                       $helpdeskHtml
     * @param \Magento\Framework\App\Helper\Context                $context
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\DepartmentFactory $departmentFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Mirasvit\Helpdesk\Helper\Html $helpdeskHtml,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->departmentFactory = $departmentFactory;
        $this->config = $config;
        $this->date = $date;
        $this->helpdeskHtml = $helpdeskHtml;
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        parent::__construct($context);
    }

    const TODAY = 'today';
    const YESTERDAY = 'yesterday';
    const THIS_WEEK = 'week';
    const PREVIOUS_WEEK = 'prev_week';
    const THIS_MONTH = 'month';
    const PREVIOUS_MONTH = 'prev_month';
    const THIS_QUARTER = 'quarter';
    const PREVIOUS_QUARTER = 'prev_quarter';
    const THIS_YEAR = 'year';
    const PREVIOUS_YEAR = 'prev_year';

    const LAST_24H = 'last_24h';
    const LAST_7D = 'last_7d';
    const LAST_30D = 'last_30d';
    const LAST_3M = 'last_3m';
    const LAST_12M = 'last_12m';

    const LIFETIME = 'lifetime';
    const CUSTOM = 'custom';

    /**
     * @return string
     */
    public function dateFormat()
    {
        return $this->localeDate->getDateFormat(\IntlDateFormatter::MEDIUM);
    }

    /**
     * @return array
     */
    public function getSolvedStatuses()
    {
        return $this->config->getSolvedStatuses();
    }

    /**
     * @return string
     */
    public function calendarDateFormat()
    {
        return $this->dateFormat();
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public function timeCallback($value)
    {
        $m = floor(($value % 3600) / 60);
        $h = floor(($value % 86400) / 3600);
        $d = floor(($value % 2592000) / 86400);
        $month = floor($value / 2592000);

        $output = [];

        if ($month > 0) {
            $output [] = "$month m";#.($M > 1 ? 'months' : 'month');
        }
        if ($d > 0) {
            $output [] = "$d d";#.($d > 1 ? 'days' : 'day');
        }
        if ($h > 0) {
            $output [] = "$h h";#.($h > 1 ? 'hours' : 'hour');
        }
        if ($m > 0) {
            $output [] = "$m m";#.($m > 1 ? 'mins' : 'min');
        }

        return implode(' ', $output);
    }

    /**
     * @param string $value
     * @param object $row
     * @param object $column
     *
     * @return bool|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function periodCallback($value, $row, $column)
    {
        $column = $column->getGrid()->getCollection()->getFilterData()->getPeriod();

        if ($value === '') {
            return '';
        }

        switch ($column) {
            case 'month':
                $value = date('M, Y', strtotime($value));
                break;

            case 'day_of_week':
                $value = date('D', strtotime("Monday +$value days"));
                break;

            case 'hour_of_day':
                $value = date('h:00 A', strtotime('0000-00-00 '.$value.':00:00'));
                break;

            default:
                $value = date('d M, Y', strtotime($value));
                break;
        }

        return $value;
    }

    /**
     * @param string $value
     * @param object $row
     * @param object $column
     *
     * @return string
     */
    public function groupByCallback($value, $row, $column)
    {
        $column = $column->getGrid()->getCollection()->getFilterData()->getGroupBy();

        if ($value === '') {
            return '';
        }

        switch ($column) {
            case 'agent':
                $value = $row->getUserId();
                $users = $this->helpdeskHtml->getAdminUserOptionArray();

                if (isset($users[$value])) {
                    $value = $users[$value];
                } else {
                    $value = '-';
                }
                break;

            case 'department':
                $department = $this->departmentFactory->create()->load($row->getGroupBy());

                if ($department->getId()) {
                    $value = $department->getName();
                } else {
                    $value = '-';
                }
                break;
        }

        return $value;
    }

    /**
     * @param string $value
     * @param object $row
     * @param object $column
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function votesCallback($value, $row, $column)
    {
        $html = [];
        $rateColors = ['#f00', '#B58800', '#00B300'];
        foreach ($rateColors as $idx => $color) {
            $html[] = '<span style="color:'.$color.'">'.$row->getData('satisfaction_rate_'.($idx + 1).'_cnt').'</span>';
        }

        return implode(' · ', $html);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function percentCallback($value)
    {
        return round($value, 1).'%';
    }

    /**
     * @param bool $subintervals
     * @param bool $lifetime
     * @param bool $custom
     *
     * @return array
     */
    public function getIntervals($subintervals = false, $lifetime = false, $custom = false)
    {
        $intervals = [];

        $intervals[self::TODAY] = 'Today';
        $intervals[self::YESTERDAY] = 'Yesterday';

        $intervals[self::THIS_WEEK] = 'This week';
        $intervals[self::PREVIOUS_WEEK] = 'Previous week';

        $intervals[self::THIS_MONTH] = 'This month';
        $intervals[self::PREVIOUS_MONTH] = 'Previous month';

        $intervals[self::THIS_QUARTER] = 'This quarter';
        $intervals[self::PREVIOUS_QUARTER] = 'Previous quarter';

        $intervals[self::THIS_YEAR] = 'This year';
        $intervals[self::PREVIOUS_YEAR] = 'Previous year';

        if ($subintervals) {
            $intervals[self::LAST_24H] = 'Last 24h hours';
            $intervals[self::LAST_7D] = 'Last 7 days';
            $intervals[self::LAST_30D] = 'Last 30 days';
            $intervals[self::LAST_3M] = 'Last 3 months';
            $intervals[self::LAST_12M] = 'Last 12 months';
        }

        if ($lifetime) {
            $intervals[self::LIFETIME] = 'Lifetime';
        }

        if ($custom) {
            $intervals[self::CUSTOM] = 'Custom';
        }

        foreach ($intervals as $code => $label) {
            $label = __($label);

            $hint = $this->getIntervalHint($code);

            if ($hint) {
                $label .= ' / '.$hint;
            }

            $intervals[$code] = $label;
        }

        return $intervals;
    }

    /**
     * @param string $code
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getIntervalHint($code)
    {
        $hint = '';

        $interval = $this->getInterval($code, true);
        $from = $interval->getFrom();
        $to = $interval->getTo();

        switch ($code) {
            case self::TODAY:
            case self::YESTERDAY:
                $hint = $from->get('MMM, d');
                break;

            case self::THIS_MONTH:
            case self::PREVIOUS_MONTH:
                $hint = $from->get('MMM');
                break;

            case self::THIS_QUARTER:
            case self::PREVIOUS_QUARTER:
                $hint = $from->get('MMM').' - '.$to->get('MMM');
                break;

            case self::THIS_YEAR:
            case self::PREVIOUS_YEAR:
                $hint = $from->get('YYYY');
                break;

            case self::LAST_24H:
                $hint = $from->get('MMM, d HH:mm').' - '.$to->get('MMM, d HH:mm');
                break;

            case self::THIS_WEEK:
            case self::PREVIOUS_WEEK:
            case self::LAST_7D:
            case self::LAST_30D:
            case self::LAST_3M:
            case self::LAST_12M:
                $hint = $from->get('MMM, d').' - '.$to->get('MMM, d');
                break;
        }

        return $hint;
    }

    /**
     * @param bool $subintervals
     * @param bool $lifetime
     * @param bool $custom
     *
     * @return array
     */
    public function getIntervalsAsOptions($subintervals = false, $lifetime = false, $custom = false)
    {
        $intervals = $this->getIntervals($subintervals, $lifetime, $custom);
        $options = [];

        foreach ($intervals as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $options;
    }

    /**
     * Return interval (two GMT \Zend_Date).
     *
     * @param string $code
     * @param bool   $timezone
     *
     * @return \Magento\Framework\DataObject
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getInterval($code, $timezone = false)
    {
        $timestamp = $this->date->gmtTimestamp();

        if ($timezone) {
            $timestamp = new \Zend_Date($timestamp);
        }

        $from = new \Zend_Date(
            $timestamp,
            null,
            $this->storeManager->getStore()->getLocaleCode()
        );
        $to = clone $from;

        switch ($code) {
            case self::TODAY:
                $from->setTime('00:00:00');

                $to->setTime('23:59:59');

                break;

            case self::YESTERDAY:
                $from->subDay(1)
                    ->setTime('00:00:00');

                $to->subDay(1)
                    ->setTime('23:59:59');

                break;

            case self::THIS_MONTH:
                $from->setDay(1)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->addDay($to->get(\Zend_Date::MONTH_DAYS) - 1)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_MONTH:
                $from->setDay(1)
                    ->subMonth(1)
                    ->setTime('00:00:00')
                    ;

                $to->setDay(1)
                    ->setTime('23:59:59')
                    ->subMonth(1)
                    ->addDay($to->get(\Zend_Date::MONTH_DAYS) - 1);

                break;

            case self::THIS_QUARTER:
                $month = intval($from->get(\Zend_Date::MONTH) / 4) * 3 + 1;
                $from->setDay(1)
                    ->setMonth($month)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->setMonth($month)
                    ->addMonth(3)
                    ->subDay(1)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_QUARTER:
                $month = intval($from->get(\Zend_Date::MONTH) / 4) * 3 + 1;

                $from->setDay(1)
                    ->setMonth($month)
                    ->setTime('00:00:00')
                    ->subMonth(3);

                $to->setDay(1)
                    ->setMonth($month)
                    ->addMonth(3)
                    ->subDay(1)
                    ->setTime('23:59:59')
                    ->subMonth(3);

                break;

            case self::THIS_YEAR:
                $from->setDay(1)
                    ->setMonth(1)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->setMonth(1)
                    ->addDay($to->get(\Zend_Date::LEAPYEAR) ? 365 : 364)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_YEAR:
                $from->setDay(1)
                    ->setMonth(1)
                    ->setTime('00:00:00')
                    ->subYear(1);

                $to->setDay(1)
                    ->setMonth(1)
                    ->addDay($to->get(\Zend_Date::LEAPYEAR) ? 365 : 364)
                    ->setTime('23:59:59')
                    ->subYear(1);

                break;

            case self::LAST_24H:
                $from->subDay(1);

                break;

            case self::THIS_WEEK:
                $weekday = $from->get(\Zend_Date::WEEKDAY_DIGIT); #0-6

                $from->setTime('00:00:00')
                    ->subDay($weekday);

                $to->setTime('23:59:59')
                    ->addDay(6 - $weekday);

                break;

            case self::PREVIOUS_WEEK:
                $weekday = $from->get(\Zend_Date::WEEKDAY_DIGIT); #0-6

                $from->setTime('00:00:00')
                    ->subDay($weekday)
                    ->subWeek(1);

                $to->setTime('23:59:59')
                    ->addDay(6 - $weekday)
                    ->subWeek(1);

                break;

            case self::LAST_7D:
                $from->subDay(7);

                break;

            case self::LAST_30D:
                $from->subDay(30);

                break;

            case self::LAST_3M:
                $from->subMonth(3);

                break;

            case self::LAST_12M:
                $from->subYear(1);

                break;

            case self::LIFETIME:
                $from->subYear(10);

                $to->addYear(10);

                break;
        }

        return new \Magento\Framework\DataObject([
            'from' => $from,
            'to' => $to, ]);
    }

    /**
     * @param string $code
     * @param int    $offsetDays
     * @param bool   $timezone
     *
     * @return \Magento\Framework\DataObject
     */
    public function getPreviousInterval($code, $offsetDays = 0, $timezone = false)
    {
        $interval = $this->getInterval($code, $timezone);

        $now = new \Zend_Date(
            $this->date->gmtTimestamp(),
            null,
            $this->storeManager->getStore()->getLocaleCode()
        );

        $diff = clone $interval->getTo();
        $diff->sub($interval->getFrom());

        if ($timezone) {
            $diff->sub($this->date->getGmtOffset());
        }

        if ($interval->getTo()->getTimestamp() > $now->getTimestamp()) {
            $interval->getTo()->subTimestamp($interval->getTo()->getTimestamp() - $now->getTimestamp());
        }

        if (intval($offsetDays) > 0) {
            $interval->getFrom()->subDay($offsetDays);
            $interval->getTo()->subDay($offsetDays);
        } else {
            $interval->getFrom()->sub($diff);
            $interval->getTo()->sub($diff);
        }

        return $interval;
    }
}
