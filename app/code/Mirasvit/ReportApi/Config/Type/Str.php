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

class Str implements TypeInterface
{
    public function getType()
    {
        return self::TYPE_STR;
    }

    public function getAggregators()
    {
        return [AggregatorInterface::TYPE_NONE, AggregatorInterface::TYPE_CONCAT];
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
        return self::FILTER_TYPE_TEXT;
    }

    public function getFormattedValue($actualValue, AggregatorInterface $aggregator)
    {
        if ($actualValue === null) {
            return __('N/A');
        }

        return $actualValue;
    }

    public function getPk($actualValue, AggregatorInterface $aggregator)
    {
        return $actualValue;
    }
}
