<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use MageWorx\ShippingRules\Api\Data\CarrierInterfaceFactory;
use MageWorx\ShippingRules\Api\CarrierRepositoryInterface;
use MageWorx\ShippingRules\Api\Data\CarrierInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageWorx\ShippingRules\Model\ResourceModel\Carrier as ResourceCarrier;
use MageWorx\ShippingRules\Model\ResourceModel\Carrier\CollectionFactory as CarrierCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class CarrierRepository implements CarrierRepositoryInterface
{
    /**
     * @var ResourceCarrier
     */
    protected $resource;

    /**
     * @var CarrierFactory
     */
    protected $carrierFactory;

    /**
     * @var CarrierCollectionFactory
     */
    protected $carrierCollectionFactory;

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
     * @var CarrierInterfaceFactory
     */
    protected $dataCarrierFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceCarrier $resource
     * @param CarrierFactory $carrierFactory
     * @param CarrierInterfaceFactory $dataCarrierFactory
     * @param CarrierCollectionFactory $carrierCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceCarrier $resource,
        CarrierFactory $carrierFactory,
        CarrierInterfaceFactory $dataCarrierFactory,
        CarrierCollectionFactory $carrierCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->carrierFactory = $carrierFactory;
        $this->carrierCollectionFactory = $carrierCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCarrierFactory = $dataCarrierFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Carrier data
     *
     * @param Carrier $carrier
     * @return Carrier
     * @throws CouldNotSaveException
     */
    public function save(Carrier $carrier)
    {
        try {
            /** @var \MageWorx\ShippingRules\Model\Carrier $carrier */
            $this->resource->save($carrier);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the carrier: %1',
                $exception->getMessage()
            ));
        }

        return $carrier;
    }

    /**
     * Load Carrier data by given Carrier Identity
     *
     * @param string $carrierId
     * @return Carrier
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($carrierId)
    {
        /** @var Carrier $carrier */
        $carrier = $this->carrierFactory->create();
        $this->resource->load($carrier, $carrierId);
        if (!$carrier->getId()) {
            throw new NoSuchEntityException(__('Carrier with id "%1" does not exist.', $carrierId));
        }

        return $carrier;
    }


    /**
     * Retrieve carrier by its code
     *
     * @param string $code
     * @return \MageWorx\ShippingRules\Model\Carrier|\MageWorx\ShippingRules\Api\Data\CarrierInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode($code)
    {
        /** @var Carrier $carrier */
        $carrier = $this->carrierFactory->create();
        $this->resource->load($carrier, $code, 'carrier_code');
        if (!$carrier->getId()) {
            throw new NoSuchEntityException(__('Carrier with code "%1" does not exist.', $code));
        }

        return $carrier;
    }

    /**
     * Load Carrier data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @param bool $returnRawObjects
     * @return \Magento\Framework\Api\SearchResultsInterface|ResourceCarrier\Collection
     */
    public function getList(SearchCriteriaInterface $criteria, $returnRawObjects = false)
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Carrier\Collection $collection */
        $collection = $this->carrierCollectionFactory->create();

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
        $carriers = [];
        /** @var Carrier $carrierModel */
        foreach ($collection as $carrierModel) {
            if ($returnRawObjects) {
                $carriers[] = $carrierModel;
            } else {
                /** @var CarrierInterface $carrierData */
                $carrierData = $this->dataCarrierFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $carrierData,
                    $carrierModel->getData(),
                    'MageWorx\ShippingRules\Api\Data\CarrierInterface'
                );
                $carriers[] = $this->dataObjectProcessor->buildOutputDataArray(
                    $carrierData,
                    'MageWorx\ShippingRules\Api\Data\CarrierInterface'
                );
            }
        }
        $searchResults->setItems($carriers);

        return $searchResults;
    }

    /**
     * Delete Carrier
     *
     * @param Carrier $carrier
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Carrier $carrier)
    {
        try {
            $this->resource->delete($carrier);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the carrier: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * Delete Carrier by given Carrier Identity
     *
     * @param string $carrierId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($carrierId)
    {
        return $this->delete($this->getById($carrierId));
    }

    /**
     * Get empty Carrier
     *
     * @return Carrier|\MageWorx\ShippingRules\Api\Data\CarrierInterface
     */
    public function getEmptyEntity()
    {
        /** @var Carrier $carrier */
        $carrier = $this->carrierFactory->create();

        return $carrier;
    }
}
