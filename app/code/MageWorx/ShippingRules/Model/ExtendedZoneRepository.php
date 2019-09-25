<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use MageWorx\ShippingRules\Api\Data\ExtendedZoneDataInterface;
use MageWorx\ShippingRules\Api\Data\ExtendedZoneDataInterfaceFactory;
use MageWorx\ShippingRules\Api\ExtendedZoneRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone as ResourceExtendedZone;
use MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone\CollectionFactory as ExtendedZoneCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ExtendedZoneRepository implements ExtendedZoneRepositoryInterface
{
    /**
     * @var ResourceExtendedZone
     */
    protected $resource;

    /**
     * @var ExtendedZoneFactory
     */
    protected $extendedZoneFactory;

    /**
     * @var ExtendedZoneCollectionFactory
     */
    protected $extendedZoneCollectionFactory;

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
     * @var ExtendedZoneDataInterfaceFactory
     */
    protected $dataExtendedZoneFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceExtendedZone $resource
     * @param ExtendedZoneFactory $extendedZoneFactory
     * @param ExtendedZoneDataInterfaceFactory $dataExtendedZoneFactory
     * @param ExtendedZoneCollectionFactory $extendedZoneCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceExtendedZone $resource,
        ExtendedZoneFactory $extendedZoneFactory,
        ExtendedZoneDataInterfaceFactory $dataExtendedZoneFactory,
        ExtendedZoneCollectionFactory $extendedZoneCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->extendedZoneFactory = $extendedZoneFactory;
        $this->extendedZoneCollectionFactory = $extendedZoneCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataExtendedZoneFactory = $dataExtendedZoneFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Pop-up Zone data
     *
     * @param ExtendedZone $extendedZone
     * @return ExtendedZone
     * @throws CouldNotSaveException
     */
    public function save(ExtendedZone $extendedZone)
    {
        try {
            /** @var \MageWorx\ShippingRules\Model\ExtendedZone $extendedZone */
            $this->resource->save($extendedZone);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Pop-up Zone: %1',
                $exception->getMessage()
            ));
        }

        return $extendedZone;
    }

    /**
     * Load Pop-up Zone data by given Zone Identity
     *
     * @param string $extendedZoneId
     * @return ExtendedZone
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($extendedZoneId)
    {
        /** @var ExtendedZone $extendedZone */
        $extendedZone = $this->extendedZoneFactory->create();
        $extendedZone->getResource()->load($extendedZone, $extendedZoneId);
        if (!$extendedZone->getId()) {
            throw new NoSuchEntityException(__('Pop-up Zone with id "%1" does not exist.', $extendedZoneId));
        }

        return $extendedZone;
    }

    /**
     * Load Pop-up Zone data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return \MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone\Collection
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var \MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone\Collection $collection */
        $collection = $this->extendedZoneCollectionFactory->create();
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
        $extendedZones = [];
        /** @var ExtendedZone $extendedZoneModel */
        foreach ($collection as $extendedZoneModel) {
            /** @var ExtendedZoneDataInterface $extendedZoneData */
            $extendedZoneData = $this->dataExtendedZoneFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $extendedZoneData,
                $extendedZoneModel->getData(),
                'MageWorx\ShippingRules\Api\Data\ExtendedZoneDataInterface'
            );
            $extendedZones[] = $this->dataObjectProcessor->buildOutputDataArray(
                $extendedZoneData,
                'MageWorx\ShippingRules\Api\Data\ExtendedZoneDataInterface'
            );
        }
        $searchResults->setItems($extendedZones);

        return $searchResults;
    }

    /**
     * Delete Pop-up Zone
     *
     * @param ExtendedZone $extendedZone
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ExtendedZone $extendedZone)
    {
        try {
            $this->resource->delete($extendedZone);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Pop-up Zone: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * Delete Pop-up Zone by given Identity
     *
     * @param string $extendedZoneId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($extendedZoneId)
    {
        return $this->delete($this->getById($extendedZoneId));
    }

    /**
     * Get empty zone
     *
     * @return ExtendedZone|ExtendedZoneDataInterface
     */
    public function getEmptyEntity()
    {
        /** @var ExtendedZone $zone */
        $zone = $this->extendedZoneFactory->create();

        return $zone;
    }
}
