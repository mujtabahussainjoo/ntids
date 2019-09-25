<?php

namespace MageWorx\ShippingRules\Model\Carrier;

use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \MageWorx\ShippingRules\Model\ResourceModel\Carrier\CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Registry $coreRegistry,
        \MageWorx\ShippingRules\Model\ResourceModel\Carrier\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->coreRegistry = $coreRegistry;
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Carrier\Collection collection */
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        /** @var \MageWorx\ShippingRules\Model\Carrier $carrier */
        $carrier = $this->coreRegistry->registry('current_carrier');
        if (!empty($carrier)) {
            $carrierId = $carrier->getId();
            $this->loadedData[$carrierId] = $carrier->getData();
        }

        return $this->loadedData;
    }
}
