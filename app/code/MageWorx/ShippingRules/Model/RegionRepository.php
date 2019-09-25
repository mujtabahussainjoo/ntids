<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use MageWorx\ShippingRules\Api\Data\RegionInterface;
use MageWorx\ShippingRules\Api\Data\RegionInterfaceFactory;
use MageWorx\ShippingRules\Api\RegionRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageWorx\ShippingRules\Model\ResourceModel\Region as ResourceRegion;
use MageWorx\ShippingRules\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class RegionRepository implements RegionRepositoryInterface
{
    /**
     * @var ResourceRegion
     */
    protected $resource;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var RegionCollectionFactory
     */
    protected $regionCollectionFactory;

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
     * @var RegionInterfaceFactory
     */
    protected $dataRegionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceRegion $resource
     * @param RegionFactory $regionFactory
     * @param RegionInterfaceFactory $dataRegionFactory
     * @param RegionCollectionFactory $regionCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceRegion $resource,
        RegionFactory $regionFactory,
        RegionInterfaceFactory $dataRegionFactory,
        RegionCollectionFactory $regionCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->regionFactory = $regionFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRegionFactory = $dataRegionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Region data
     *
     * @param Region $region
     * @return Region
     * @throws CouldNotSaveException
     */
    public function save(Region $region)
    {
        try {
            /** @var \MageWorx\ShippingRules\Model\Region $region */
            $this->resource->save($region);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the region: %1',
                $exception->getMessage()
            ));
        }

        return $region;
    }

    /**
     * Load Region data by given Region Identity
     *
     * @param string $regionId
     * @return Region
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($regionId)
    {
        /** @var Region $region */
        $region = $this->regionFactory->create();
        $region->getResource()->load($region, $regionId);
        if (!$region->getId()) {
            throw new NoSuchEntityException(__('Region with id "%1" does not exist.', $regionId));
        }

        return $region;
    }

    /**
     * Load Region data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return \MageWorx\ShippingRules\Model\ResourceModel\Region\Collection
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Region\Collection $collection */
        $collection = $this->regionCollectionFactory->create();
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
        $regions = [];
        /** @var Region $regionModel */
        foreach ($collection as $regionModel) {
            /** @var RegionInterface $regionData */
            $regionData = $this->dataRegionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $regionData,
                $regionModel->getData(),
                'MageWorx\ShippingRules\Api\Data\RegionInterface'
            );
            $regions[] = $this->dataObjectProcessor->buildOutputDataArray(
                $regionData,
                'MageWorx\ShippingRules\Api\Data\RegionInterface'
            );
        }
        $searchResults->setItems($regions);

        return $searchResults;
    }

    /**
     * Delete Region
     *
     * @param Region $region
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Region $region)
    {
        try {
            $this->resource->delete($region);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the region: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * Delete Region by given Region Identity
     *
     * @param string $regionId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($regionId)
    {
        return $this->delete($this->getById($regionId));
    }

    /**
     * Get empty Region
     *
     * @return Region|RegionInterface
     */
    public function getEmptyEntity()
    {
        /** @var Region $region */
        $region = $this->regionFactory->create();

        return $region;
    }
}
