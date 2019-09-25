<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ImportExport;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use MageWorx\ShippingRules\Api\Data\CarrierInterface;
use MageWorx\ShippingRules\Api\Data\MethodInterface;
use MageWorx\ShippingRules\Api\Data\RateInterface;

abstract class AbstractImportExport extends DataObject
{
    const CARRIER_ENTITY_KEY = 'carrier';
    const METHOD_ENTITY_KEY = 'method';
    const RATE_ENTITY_KEY = 'rate';
    const ENTITY_TYPE = 'ie_type';
    const ENTITY_ID = 'id';

    const COLON = '"';
    /**
     * @var \Magento\Framework\Reflection\MethodsMap
     */
    protected $reflectionMethodsMap;

    /**
     * @var array
     */
    protected $entitiesMap = [
        self::CARRIER_ENTITY_KEY => 'MageWorx\ShippingRules\Api\Data\CarrierInterface',
        self::METHOD_ENTITY_KEY => 'MageWorx\ShippingRules\Api\Data\MethodInterface',
        self::RATE_ENTITY_KEY => 'MageWorx\ShippingRules\Api\Data\RateInterface',
    ];

    /**
     * @var array
     */
    protected $entitiesDependency = [
        self::RATE_ENTITY_KEY => self::METHOD_ENTITY_KEY,
        self::METHOD_ENTITY_KEY => self::CARRIER_ENTITY_KEY
    ];

    /**
     * @var \MageWorx\ShippingRules\Api\CarrierRepositoryInterface
     */
    protected $carrierRepository;

    /**
     * @var \MageWorx\ShippingRules\Api\MethodRepositoryInterface
     */
    protected $methodRepository;

    /**
     * @var \MageWorx\ShippingRules\Api\RateRepositoryInterface
     */
    protected $rateRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var array|CarrierInterface[]
     */
    protected $carriers;

    /**
     * @var array|MethodInterface[]
     */
    protected $methods;

    /**
     * @var array|RateInterface[]
     */
    protected $rates;

    /**
     * Data fields cache
     *
     * @var array
     */
    protected $dataFields;

    /**
     * Data fields cache stored by entity
     *
     * @var array
     */
    protected $dataFieldsByEntity;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \MageWorx\ShippingRules\Helper\Data
     */
    protected $helper;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * AggregatedDataObject constructor.
     * @param \Magento\Framework\Reflection\MethodsMap $reflectionMethodsMap
     * @param \MageWorx\ShippingRules\Api\CarrierRepositoryInterface $carrierRepository
     * @param \MageWorx\ShippingRules\Api\MethodRepositoryInterface $methodRepository
     * @param \MageWorx\ShippingRules\Api\RateRepositoryInterface $rateRepository
     * @param \MageWorx\ShippingRules\Helper\Data $helper
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\DataObject\Factory $dataObjectFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param array $entities
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Reflection\MethodsMap $reflectionMethodsMap,
        \MageWorx\ShippingRules\Api\CarrierRepositoryInterface $carrierRepository,
        \MageWorx\ShippingRules\Api\MethodRepositoryInterface $methodRepository,
        \MageWorx\ShippingRules\Api\RateRepositoryInterface $rateRepository,
        \MageWorx\ShippingRules\Helper\Data $helper,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\DataObject\Factory $dataObjectFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $entities = [],
        array $data = []
    ) {
        parent::__construct($data);
        $this->reflectionMethodsMap = $reflectionMethodsMap;
        $this->carrierRepository = $carrierRepository;
        $this->methodRepository = $methodRepository;
        $this->rateRepository = $rateRepository;
        $this->helper = $helper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->storeManager = $storeManager;
        $this->csvProcessor = $csvProcessor;
        $this->objectManager = $objectManager;
        $this->messageManager = $messageManager;
    }

    /**
     * Get headers for the selected entities
     *
     * @param array $entities
     * @return \Magento\Framework\DataObject
     */
    protected function getHeaders($entities = [])
    {
        $dataFields = $this->getDataFields($entities);
        $dataObject = $this->dataObjectFactory->create($dataFields);

        return $dataObject;
    }

    /**
     * Get all available fields for the selected entities (all available by default)
     *
     * @param array $entities
     * @return array
     */
    protected function getDataFields(array $entities = [])
    {
        if (!empty($this->dataFields)) {
            return $this->dataFields;
        }

        if (empty($entities)) {
            $entities = $this->getAllAvailableEntitiesName();
        }

        $fields = [static::ENTITY_TYPE => __('IE Type')];
        if ($this->useIds()) {
            $fields[static::ENTITY_ID] = __('ID');
        }

        foreach ($entities as $entityName) {
            if (empty($this->entitiesMap[$entityName])) {
                continue;
            }

            $entityInterfaceName = $this->entitiesMap[$entityName];
            $methods = $this->reflectionMethodsMap->getMethodsMap($entityInterfaceName);

            // Detect ignored columns
            $classInstance = $this->objectManager->get($entityInterfaceName);
            if ($classInstance instanceof \MageWorx\ShippingRules\Api\ImportExportEntity) {
                $ignoredColumns = $classInstance::getIgnoredColumnsForImportExport();
            } else {
                $ignoredColumns = [];
            }

            foreach ($methods as $methodName => $data) {
                $propertyKeyName = $this->_underscore(substr($methodName, 3));
                if (in_array($propertyKeyName, $ignoredColumns)) {
                    continue;
                }
                $humanReadableKeyName = ucwords(str_replace('_', ' ', $propertyKeyName));
                if ($this->shouldTranslate()) {
                    $humanReadableKeyName = __($humanReadableKeyName);
                }
                $fields[$propertyKeyName] = $humanReadableKeyName;
            }

            // Save data fields cache specified by each entity (separated)
            // @DEBUG
            $this->dataFieldsByEntity[$entityName] = $fields;
        }

        $this->dataFields = $fields;

        return $this->dataFields;
    }

    /**
     * Get name of the all entities available by default
     *
     * @return array
     */
    protected function getAllAvailableEntitiesName()
    {
        return [
            self::CARRIER_ENTITY_KEY,
            self::METHOD_ENTITY_KEY,
            self::RATE_ENTITY_KEY
        ];
    }

    /**
     * Translate or not attribute names?
     *
     * @TODO: make it work with config
     *
     * @return bool
     */
    protected function shouldTranslate()
    {
        return false;
    }

    /**
     * Do we need to use system data?
     *
     * @TODO: make it work with config
     *
     * @return bool
     */
    protected function useSystemData()
    {
        return false;
    }

    /**
     * Get creation time
     *
     * @return int
     */
    protected function getTime()
    {
        return time();
    }

    /**
     * Get store base url
     *
     * @return string
     */
    protected function getHost()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    /**
     * Should or not use ids during import/export
     *
     * @return bool
     */
    protected function useIds()
    {
        return $this->helper->isIdsUsedDuringImport();
    }
}