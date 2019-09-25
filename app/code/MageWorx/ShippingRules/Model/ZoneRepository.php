<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use MageWorx\ShippingRules\Api\Data\ZoneInterface;
use MageWorx\ShippingRules\Api\Data\ZoneInterfaceFactory;
use MageWorx\ShippingRules\Api\ZoneRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageWorx\ShippingRules\Model\ResourceModel\Zone as ResourceZone;
use MageWorx\ShippingRules\Model\ResourceModel\Zone\CollectionFactory as ZoneCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ZoneRepository implements ZoneRepositoryInterface
{
    /**
     * @var ResourceZone
     */
    protected $resource;

    /**
     * @var ZoneFactory
     */
    protected $zoneFactory;

    /**
     * @var ZoneCollectionFactory
     */
    protected $zoneCollectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var ZoneInterfaceFactory
     */
    protected $dataZoneFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceZone $resource
     * @param ZoneFactory $zoneFactory
     * @param ZoneInterfaceFactory $dataZoneFactory
     * @param ZoneCollectionFactory $zoneCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceZone $resource,
        ZoneFactory $zoneFactory,
        ZoneInterfaceFactory $dataZoneFactory,
        ZoneCollectionFactory $zoneCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->zoneFactory = $zoneFactory;
        $this->zoneCollectionFactory = $zoneCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataZoneFactory = $dataZoneFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Zone data
     *
     * @param Zone $zone
     * @return Zone
     * @throws CouldNotSaveException
     */
    public function save(Zone $zone)
    {
        try {
            /** @var \MageWorx\ShippingRules\Model\Zone $zone */
            $this->resource->save($zone);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the zone: %1',
                $exception->getMessage()
            ));
        }

        return $zone;
    }

    /**
     * Load Zone data by given Zone Identity
     *
     * @param string $zoneId
     * @return Zone
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($zoneId)
    {
        /** @var Zone $zone */
        $zone = $this->zoneFactory->create();
        $zone->getResource()->load($zone, $zoneId);
        if (!$zone->getId()) {
            throw new NoSuchEntityException(__('Zone with id "%1" does not exist.', $zoneId));
        }

        return $zone;
    }

    /**
     * Load Zone data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return \MageWorx\ShippingRules\Model\ResourceModel\Zone\Collection
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Zone\Collection $collection */
        $collection = $this->zoneCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter(
                    $filter->getField(),
                    [
                        $condition => $filter->getValue()
                    ]
                );
            }
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $zones = [];
        /** @var Zone $zoneModel */
        foreach ($collection as $zoneModel) {
            /** @var ZoneInterface $zoneData */
            $zoneData = $this->dataZoneFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $zoneData,
                $zoneModel->getData(),
                'MageWorx\ShippingRules\Api\Data\ZoneInterface'
            );
            $zones[] = $this->dataObjectProcessor->buildOutputDataArray(
                $zoneData,
                'MageWorx\ShippingRules\Api\Data\ZoneInterface'
            );
        }
        $searchResults->setItems($zones);

        return $searchResults;
    }

    /**
     * Delete Zone
     *
     * @param Zone $zone
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Zone $zone)
    {
        try {
            $this->resource->delete($zone);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the zone: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * Delete Zone by given Zone Identity
     *
     * @param string $zoneId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($zoneId)
    {
        return $this->delete($this->getById($zoneId));
    }

    /**
     * Get empty zone
     *
     * @return Zone|ZoneInterface
     */
    public function getEmptyEntity()
    {
        /** @var Zone $zone */
        $zone = $this->zoneFactory->create();

        return $zone;
    }
}
