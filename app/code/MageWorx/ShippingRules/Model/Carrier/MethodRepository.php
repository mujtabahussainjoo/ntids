<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Carrier;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use MageWorx\ShippingRules\Api\Data\MethodInterfaceFactory;
use MageWorx\ShippingRules\Api\MethodRepositoryInterface;
use MageWorx\ShippingRules\Api\Data\MethodInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageWorx\ShippingRules\Model\ResourceModel\Method as ResourceMethod;
use MageWorx\ShippingRules\Model\ResourceModel\Method\CollectionFactory as MethodCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class MethodRepository implements MethodRepositoryInterface
{
    /**
     * @var ResourceMethod
     */
    protected $resource;

    /**
     * @var MethodFactory
     */
    protected $methodFactory;

    /**
     * @var MethodCollectionFactory
     */
    protected $methodCollectionFactory;

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
     * @var MethodInterfaceFactory
     */
    protected $dataMethodFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ResourceMethod $resource
     * @param MethodFactory $methodFactory
     * @param MethodInterfaceFactory $dataMethodFactory
     * @param MethodCollectionFactory $methodCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceMethod $resource,
        MethodFactory $methodFactory,
        MethodInterfaceFactory $dataMethodFactory,
        MethodCollectionFactory $methodCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->methodFactory = $methodFactory;
        $this->methodCollectionFactory = $methodCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataMethodFactory = $dataMethodFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Method data
     *
     * @param Method $method
     * @return Method
     * @throws CouldNotSaveException
     */
    public function save(Method $method)
    {
        try {
            /** @var Method $method */
            $this->resource->save($method);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the method: %1',
                $exception->getMessage()
            ));
        }

        return $method;
    }

    /**
     * Load Method data by given Method Identity
     *
     * @param string $methodId
     * @return Method
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($methodId)
    {
        /** @var Method $method */
        $method = $this->methodFactory->create();
        $this->resource->load($method, $methodId);
        if (!$method->getId()) {
            throw new NoSuchEntityException(__('Method with id "%1" does not exist.', $methodId));
        }

        return $method;
    }

    /**
     * Retrieve method by its code
     *
     * @param string $code
     * @return \MageWorx\ShippingRules\Model\Carrier\Method|\MageWorx\ShippingRules\Api\Data\MethodInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode($code)
    {
        /** @var Method $method */
        $method = $this->methodFactory->create();
        $this->resource->load($method, $code, 'code');
        if (!$method->getId()) {
            throw new NoSuchEntityException(__('Method with a code "%1" does not exist.', $code));
        }

        return $method;
    }

    /**
     * Load Method data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @param bool $returnRawObjects
     * @return \Magento\Framework\Api\SearchResultsInterface|ResourceMethod\Collection
     */
    public function getList(SearchCriteriaInterface $criteria, $returnRawObjects = false)
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Method\Collection $collection */
        $collection = $this->methodCollectionFactory->create();

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
        $methods = [];
        /** @var Method $methodModel */
        foreach ($collection as $methodModel) {
            if ($returnRawObjects) {
                $methods[] = $methodModel;
            } else {
                /** @var MethodInterface $methodData */
                $methodData = $this->dataMethodFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $methodData,
                    $methodModel->getData(),
                    'MageWorx\ShippingRules\Api\Data\MethodInterface'
                );
                $methods[] = $this->dataObjectProcessor->buildOutputDataArray(
                    $methodData,
                    'MageWorx\ShippingRules\Api\Data\MethodInterface'
                );
            }
        }
        $searchResults->setItems($methods);

        return $searchResults;
    }

    /**
     * Delete Method
     *
     * @param Method $method
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Method $method)
    {
        try {
            $this->resource->delete($method);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the method: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * Delete Method by given Method Identity
     *
     * @param string $methodId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($methodId)
    {
        return $this->delete($this->getById($methodId));
    }

    /**
     * Get empty Method
     *
     * @return Method|MethodInterface
     */
    public function getEmptyEntity()
    {
        /** @var Method $carrier */
        $method = $this->methodFactory->create();

        return $method;
    }
}
