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

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\ReportApi\Api\Config\AggregatorInterface;
use Mirasvit\ReportApi\Api\Config\TypeInterface;

class Select extends Str implements TypeInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $options;

    public function __construct(
        ObjectManagerInterface $objectManager,
        $options
    ) {
        $this->objectManager = $objectManager;
        $this->options       = $options;
    }

    public function getType()
    {
        return self::TYPE_SELECT;
    }

    public function getJsType()
    {
        return self::JS_TYPE_SELECT;
    }

    public function getJsFilterType()
    {
        return self::FILTER_TYPE_SELECT;
    }

    public function getFormattedValue($actualValue, AggregatorInterface $aggregator)
    {
        $options = $this->getOptions();

        $values  = explode(',', $actualValue);
        $results = [];
        foreach ($options as $option) {
            if (in_array($option['value'], $values)) {
                $results[] = trim($option['label']);
            }
        }

        $results = array_filter($results);

        return count($results) ? implode(', ', $results) : __(self::NA, $actualValue);
    }

    public function getOptions()
    {
        if (is_array($this->options)) {
            return $this->options;
        } else {
            $source = $this->objectManager->create($this->options);
            if ($source instanceof OptionSourceInterface) {
                return $source->toOptionArray();
            } else {
                throw new \Exception("Source {$this->options} must implement OptionSourceInterface");
            }
        }
    }
}
