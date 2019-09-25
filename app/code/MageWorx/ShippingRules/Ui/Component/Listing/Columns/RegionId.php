<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class RegionId extends Column
{
    /**
     * @var \MageWorx\ShippingRules\Model\Config\Source\Locale\AllRegions
     */
    private $sourceRegions;

    /**
     * @var \MageWorx\ShippingRules\Helper\Data
     */
    private $helper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \MageWorx\ShippingRules\Model\Config\Source\Locale\AllRegions $sourceRegions
     * @param mixed[] $components
     * @param mixed[] $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \MageWorx\ShippingRules\Model\Config\Source\Locale\AllRegions $sourceRegions,
        \MageWorx\ShippingRules\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->sourceRegions = $sourceRegions;
        $this->helper = $helper;
    }

    /**
     * Prepare Data Source
     *
     * @param mixed[] $dataSource
     * @return mixed[]
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param mixed[] $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        if (!empty($item['region_id'])) {
            $origRegions = explode(',', $item['region_id']);
        }

        if (empty($origRegions)) {
            return __('Any Region');
        }

        if (!is_array($origRegions)) {
            $origRegions = [$origRegions];
        }

        $data = $this->sourceRegions->toKeyValuePairs();

        foreach ($origRegions as $region) {
            if (empty($data[$region])) {
                continue;
            }

            $content .= $data[$region] . "<br/>";
        }

        return $content;
    }
}