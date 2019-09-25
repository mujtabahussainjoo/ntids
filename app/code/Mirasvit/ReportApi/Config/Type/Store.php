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

use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\ReportApi\Api\Config\AggregatorInterface;
use Mirasvit\ReportApi\Api\Config\TypeInterface;

class Store implements TypeInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function getType()
    {
        return self::TYPE_STORE;
    }

    public function getAggregators()
    {
        return ['none'];
    }

    public function getValueType()
    {
        return self::VALUE_TYPE_NUMBER;
    }

    public function getJsType()
    {
        return self::JS_TYPE_SELECT;
    }

    public function getJsFilterType()
    {
        return self::JS_TYPE_SELECT;
    }

    public function getFormattedValue($actualValue, AggregatorInterface $aggregator)
    {
        $options = $this->getOptions();

        foreach ($options as $option) {
            if ($option['value'] == $actualValue) {
                return $option['label'];
            }
        }

        return self::NA;
    }

    public function getOptions()
    {
        $options  = [];
        $websites = $this->storeManager->getWebsites();

        foreach ($websites as $website) {
            /** @var \Magento\Store\Model\Group $group */
            foreach ($website->getGroups() as $group) {
                /** @var \Magento\Store\Model\Store $store */
                foreach ($group->getStores() as $store) {
                    $options[] = [
                        'label' => $store->getName(),
                        'value' => $store->getId(),
                    ];
                }
            }
        }

        return $options;
    }

    public function getPk($actualValue, AggregatorInterface $aggregator)
    {
        return $actualValue;
    }
}
