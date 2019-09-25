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
 * @package   mirasvit/module-report-api
 * @version   1.0.16
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Config\Type;

use Mirasvit\ReportApi\Api\Config\AggregatorInterface;
use Mirasvit\ReportApi\Api\Config\TypeInterface;

class Date implements TypeInterface
{
    public function getType()
    {
        return self::TYPE_DATE;
    }

    public function getAggregators()
    {
        return ['none', 'hour', 'day', 'day_of_week', 'month', 'quarter', 'week', 'year'/*, 'dateRange'*/];
    }

    public function getValueType()
    {
        return self::VALUE_TYPE_STRING;
    }

    public function getJsType()
    {
        return self::JS_TYPE_TEXT;
    }

    public function getJsFilterType()
    {
        return self::FILTER_TYPE_DATE_RANGE;
    }

    public function getFormattedValue($actualValue, AggregatorInterface $aggregator)
    {
        if ($actualValue === null) {
            return null;
        }

        $value = $actualValue;

        switch ($aggregator->getType()) {
            case AggregatorInterface::TYPE_HOUR:
                if (strlen($value) == 1) {
                    $value = '0' . $value;
                }

                $value .= ':00';
                break;

            case AggregatorInterface::TYPE_DAY:
                $value = date('d M, Y', strtotime($actualValue));
                break;

            case AggregatorInterface::TYPE_DAY_OF_WEEK:
                switch ($actualValue) {
                    case 0:
                        $value = __('Monday');
                        break;
                    case 1:
                        $value = __('Tuesday');
                        break;
                    case 2:
                        $value = __('Wednesday');
                        break;
                    case 3:
                        $value = __('Thursday');
                        break;
                    case 4:
                        $value = __('Friday');
                        break;
                    case 5:
                        $value = __('Saturday');
                        break;
                    case 6:
                        $value = __('Sunday');
                        break;
                }
                break;

            case AggregatorInterface::TYPE_WEEK:
                $value = date('d M, Y', strtotime($actualValue) - 7 * 24 * 60 * 60)
                    . ' - '
                    . date('d M, Y', strtotime($actualValue)) . ' (' . (date('W', strtotime($actualValue)) - 1) . ')';
                break;

            case AggregatorInterface::TYPE_MONTH:
                $value = date('M, Y', strtotime($actualValue));
                break;

            case AggregatorInterface::TYPE_QUARTER:
                $strVal = strtotime($actualValue);
                $year   = date('Y', $strVal);
                switch (date('n', $strVal)) {
                    case 1:
                        $value = 'Jan, ' . $year . ' – Mar, ' . $year;
                        break;
                    case 2:
                        $value = 'Apr, ' . $year . ' – Jun, ' . $year;
                        break;
                    case 3:
                        $value = 'Jul, ' . $year . ' – Sep, ' . $year;
                        break;
                    case 4:
                        $value = 'Oct, ' . $year . ' – Dec, ' . $year;
                        break;
                }
                break;

            case AggregatorInterface::TYPE_YEAR:
                $value = date('Y', strtotime($actualValue));
                break;
            default:
                $value = date('d M, Y H:i', strtotime($actualValue));
        }

        return $value;
    }

    public function getPk($actualValue, AggregatorInterface $aggregator)
    {
        if ($actualValue === null) {
            return null;
        }

        $value = $actualValue;

        switch ($aggregator->getType()) {
            case AggregatorInterface::TYPE_DAY:
                $value = date('d', strtotime($actualValue));
                break;
            case AggregatorInterface::TYPE_WEEK:
                $value = date('W', strtotime($actualValue));
                break;
            case AggregatorInterface::TYPE_MONTH:
                $value = date('m', strtotime($actualValue));
                break;
            case AggregatorInterface::TYPE_QUARTER:
                $value = date('n', strtotime($actualValue));
                break;
            case AggregatorInterface::TYPE_YEAR:
                $value = '0';
                break;
        }

        return $value;
    }
}
