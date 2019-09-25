<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use MageWorx\ShippingRules\Api\Data\RuleInterface;
use MageWorx\ShippingRules\Api\Data\RuleInterfaceFactory;
use MageWorx\ShippingRules\Api\RuleRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageWorx\ShippingRules\Model\ResourceModel\Rule as ResourceRule;
use MageWorx\ShippingRules\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class RuleRepository implements RuleRepositoryInterface
{
    /**
     * @var ResourceRule
     */
    protected $resource;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var RuleCollectionFactory
     */
    protected $ruleCollectionFactory;

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
     * @var RuleInterfaceFactory
     */
    protected $dataRuleFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceRule $resource
     * @param RuleFactory $ruleFactory
     * @param RuleInterfaceFactory $dataRuleFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceRule $resource,
        RuleFactory $ruleFactory,
        RuleInterfaceFactory $dataRuleFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->ruleFactory = $ruleFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRuleFactory = $dataRuleFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Rule data
     *
     * @param Rule $rule
     * @return Rule
     * @throws CouldNotSaveException
     */
    public function save(Rule $rule)
    {
        try {
            /** @var \MageWorx\ShippingRules\Model\Rule $rule */
            $this->resource->save($rule);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rule: %1',
                $exception->getMessage()
            ));
        }

        return $rule;
    }

    /**
     * Load Rule data by given Rule Identity
     *
     * @param string $ruleId
     * @return Rule
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($ruleId)
    {
        /** @var Rule $rule */
        $rule = $this->ruleFactory->create();
        $rule->getResource()->load($rule, $ruleId);
        if (!$rule->getId()) {
            throw new NoSuchEntityException(__('Rule with id "%1" does not exist.', $ruleId));
        }

        return $rule;
    }

    /**
     * Load Rule data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return \MageWorx\ShippingRules\Model\ResourceModel\Rule\Collection
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->ruleCollectionFactory->create();

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue());
                    continue;
                }
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
        $rules = [];
        /** @var Rule $ruleModel */
        foreach ($collection as $ruleModel) {
            /** @var RuleInterface $ruleData */
            $ruleData = $this->dataRuleFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $ruleData,
                $ruleModel->getData(),
                'MageWorx\ShippingRules\Api\Data\RuleInterface'
            );
            $rules[] = $this->dataObjectProcessor->buildOutputDataArray(
                $ruleData,
                'MageWorx\ShippingRules\Api\Data\RuleInterface'
            );
        }
        $searchResults->setItems($rules);

        return $searchResults;
    }

    /**
     * Delete Rule
     *
     * @param Rule $rule
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Rule $rule)
    {
        try {
            $this->resource->delete($rule);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the rule: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * Delete Rule by given Rule Identity
     *
     * @param string $ruleId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($ruleId)
    {
        return $this->delete($this->getById($ruleId));
    }

    /**
     * Get empty rule
     *
     * @return Rule|RuleInterface
     */
    public function getEmptyEntity()
    {
        /** @var Rule $rule */
        $rule = $this->ruleFactory->create();

        return $rule;
    }
}
