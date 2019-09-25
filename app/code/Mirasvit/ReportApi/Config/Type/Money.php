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

use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Mirasvit\ReportApi\Api\Config\AggregatorInterface;
use Mirasvit\ReportApi\Api\Config\TypeInterface;

class Money extends Number implements TypeInterface
{
    private $pricingHelper;

    public function __construct(
        PricingHelper $pricingHelper
    ) {
        $this->pricingHelper = $pricingHelper;
    }

    public function getType()
    {
        return self::TYPE_MONEY;
    }

    public function getAggregators()
    {
        return ['none', 'sum', 'avg'];
    }

    public function getJsType()
    {
        return self::JS_TYPE_MONEY;
    }

    public function getFormattedValue($actualValue, AggregatorInterface $aggregator)
    {
        return $this->pricingHelper->currency($actualValue, true, false);
    }
}
