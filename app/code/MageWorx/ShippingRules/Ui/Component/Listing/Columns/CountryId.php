<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class CountryId extends Column
{
    /**
     * @var \MageWorx\ShippingRules\Model\Config\Source\Locale\Country
     */
    private $sourceCountry;

    /**
     * @var \MageWorx\ShippingRules\Helper\Data
     */
    private $helper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \MageWorx\ShippingRules\Model\Config\Source\Locale\Country $sourceCountry
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \MageWorx\ShippingRules\Model\Config\Source\Locale\Country $sourceCountry,
        \MageWorx\ShippingRules\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->sourceCountry = $sourceCountry;
        $this->helper = $helper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
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
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        if (!empty($item['country_id'])) {
            $origCountries = explode(',', $item['country_id']);
        }

        if (empty($origCountries)) {
            return __('Any Country');
        }

        if (!is_array($origCountries)) {
            $origCountries = [$origCountries];
        }

        $data = $this->sourceCountry->toKeyValuePairs();

        foreach ($origCountries as $country) {
            if (empty($data[$country])) {
                continue;
            }

            $content .= $data[$country] . "<br/>";
        }

        return $content;
    }
}