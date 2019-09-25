<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\ResourceModel;

use Aheadworks\Rbslider\Api\Data\StatisticInterface;
use Aheadworks\Rbslider\Api\Data\StatisticInterfaceFactory;
use Aheadworks\Rbslider\Api\StatisticRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Rbslider\Api\Data\StatisticSearchResultsInterface;
use Aheadworks\Rbslider\Api\Data\StatisticSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Rbslider\Model\Statistic as StatisticModel;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Rbslider\Model\StatisticFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class StatisticRepository
 * @package Aheadworks\Rbslider\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StatisticRepository implements StatisticRepositoryInterface
{
    /**
     * @var StatisticInterfaceFactory
     */
    private $statisticDataFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var StatisticFactory
     */
    private $statisticFactory;

    /**
     * @var StatisticSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @param StatisticInterfaceFactory $statisticDataFactory
     * @param EntityManager $entityManager
     * @param StatisticFactory $statisticFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StatisticSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        StatisticInterfaceFactory $statisticDataFactory,
        EntityManager $entityManager,
        StatisticFactory $statisticFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StatisticSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->statisticDataFactory = $statisticDataFactory;
        $this->entityManager = $entityManager;
        $this->statisticFactory = $statisticFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(StatisticInterface $statistic)
    {
        return $this->entityManager->save($statistic);
    }

    /**
     * {@inheritdoc}
     */
    public function get($statisticId)
    {
        /** @var StatisticInterface $statistic */
        $statistic = $this->statisticDataFactory->create();
        $this->entityManager->load($statistic, $statisticId);
        if (!$statistic->getId()) {
            throw NoSuchEntityException::singleField('statisticId', $statisticId);
        }
        return $statistic;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var StatisticSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \Aheadworks\Rbslider\Model\ResourceModel\Statistic\Collection $collection */
        $collection = $this->statisticFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, StatisticInterface::class);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == 'slide_id') {
                    $collection->addSlideFilter($filter->getValue());
                } elseif ($filter->getField() == 'banner_id') {
                    $collection->addBannerFilter($filter->getValue());
                } else {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $fields[] = $filter->getField();
                    $conditions[] = [$condition => $filter->getValue()];
                }
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $collection
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $statistics = [];
        /** @var StatisticModel $statisticModel */
        foreach ($collection as $statisticModel) {
            $statistics[] = $this->getStatisticDataObject($statisticModel);
        }
        $searchResults->setItems($statistics);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(StatisticInterface $statistic)
    {
        return $this->deleteById($statistic->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($statisticId)
    {
        $statistic = $this->get($statisticId);
        $this->entityManager->delete($statistic);
        return true;
    }

    /**
     * Retrieves statistic data object using Statistic Model
     *
     * @param StatisticModel $statistic
     * @return StatisticInterface
     */
    private function getStatisticDataObject(StatisticModel $statistic)
    {
        /** @var StatisticInterface $statisticDataObject */
        $statisticDataObject = $this->statisticDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $statisticDataObject,
            $statistic->getData(),
            StatisticInterface::class
        );
        $statisticDataObject->setId($statistic->getId());

        return $statisticDataObject;
    }
}
