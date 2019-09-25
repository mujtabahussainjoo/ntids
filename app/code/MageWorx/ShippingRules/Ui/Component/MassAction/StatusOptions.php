<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Ui\Component\MassAction;

use Zend\Stdlib\JsonSerializable;
use Magento\Framework\UrlInterface;

class StatusOptions implements JsonSerializable
{
    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Sub-actions Base URL
     *
     * @var string
     */
    protected $urlPath;

    /**
     * Sub-actions param name
     *
     * @var string
     */
    protected $paramName;

    /**
     * Sub-actions additional params
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(UrlInterface $urlBuilder, array $data = [])
    {
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if (empty($this->options)) {
            $this->prepareOptionsData();

            $this->options['active'] = array_merge_recursive(
                $this->options['active'] = [
                    'type' => 'active',
                    'label' => __('Active'),
                    'url' => $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => '1']
                    )
                ],
                $this->additionalData
            );

            $this->options['inactive'] = array_merge_recursive(
                $this->options['inactive'] = [
                    'type' => 'inactive',
                    'label' => __('Inactive'),
                    'url' => $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => '0']
                    )
                ],
                $this->additionalData
            );

            $this->options = array_values($this->options);
        }

        return $this->options;
    }

    /**
     * Prepare sub-actions addition data
     *
     * @return void
     */
    protected function prepareOptionsData()
    {
        foreach ($this->data as $dataKey => $dataValue) {
            switch ($dataKey) {
                case 'paramName':
                    $this->paramName = $dataValue;
                    break;
                case 'urlPath':
                    $this->urlPath = $dataValue;
                    break;
                default:
                    $this->additionalData[$dataKey] = $dataValue;
                    break;
            }
        }
    }
}
