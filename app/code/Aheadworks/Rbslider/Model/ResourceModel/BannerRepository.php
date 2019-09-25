<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\ResourceModel;

use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Aheadworks\Rbslider\Api\BannerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Rbslider\Model\BannerFactory;
use Aheadworks\Rbslider\Model\Banner as BannerModel;
use Aheadworks\Rbslider\Api\Data\BannerInterfaceFactory;
use Aheadworks\Rbslider\Model\BannerRegistry;
use Aheadworks\Rbslider\Api\Data\BannerSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Rbslider\Model\Converter\Condition as ConditionConverter;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Rbslider\Api\Data\BannerSearchResultsInterface;

/**
 * Class BannerRepository
 * @package Aheadworks\Rbslider\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BannerRepository implements BannerRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var BannerFactory
     */
    private $bannerFactory;

    /**
     * @var BannerInterfaceFactory
     */
    private $bannerDataFactory;

    /**
     * @var BannerRegistry
     */
    private $bannerRegistry;

    /**
     * @var BannerSearchResultsInterfaceFactory
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
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @param EntityManager $entityManager
     * @param BannerFactory $bannerFactory
     * @param BannerInterfaceFactory $bannerDataFactory
     * @param BannerRegistry $bannerRegistry
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param BannerSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ConditionConverter $conditionConverter
     */
    public function __construct(
        EntityManager $entityManager,
        BannerFactory $bannerFactory,
        BannerInterfaceFactory $bannerDataFactory,
        BannerRegistry $bannerRegistry,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        BannerSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ConditionConverter $conditionConverter
    ) {
        $this->entityManager = $entityManager;
        $this->bannerFactory = $bannerFactory;
        $this->bannerDataFactory = $bannerDataFactory;
        $this->bannerRegistry = $bannerRegistry;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->conditionConverter = $conditionConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(BannerInterface $banner)
    {
        /** @var BannerModel $bannerModel */
        $bannerModel = $this->bannerFactory->create();
        if ($bannerId = $banner->getId()) {
            $this->entityManager->load($bannerModel, $bannerId);
        }
        $bannerModel->addData(
            $this->dataObjectProcessor->buildOutputDataArray($banner, BannerInterface::class)
        );

        $bannerModel->beforeSave();
        $this->entityManager->save($bannerModel);
        $banner = $this->getBannerDataObject($bannerModel);
        $this->bannerRegistry->push($banner);
        return $banner;
    }

    /**
     * {@inheritdoc}
     */
    public function get($bannerId)
    {
        if (null === $this->bannerRegistry->retrieve($bannerId)) {
            /** @var BannerInterface $bannerModel */
            $bannerModel = $this->bannerDataFactory->create();
            $this->entityManager->load($bannerModel, $bannerId);
            if (!$bannerModel->getId()) {
                throw NoSuchEntityException::singleField('bannerId', $bannerId);
            } else {
                $bannerModel = $this->convertBannerConditionsToDataModel($bannerModel);
                $this->bannerRegistry->push($bannerModel);
            }
        }
        return $this->bannerRegistry->retrieve($bannerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var BannerSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \Aheadworks\Rbslider\Model\ResourceModel\Banner\Collection $collection */
        $collection = $this->bannerFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, BannerInterface::class);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
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

        $banners = [];
        /** @var BannerModel $bannerModel */
        foreach ($collection as $bannerModel) {
            $banners[] = $this->convertBannerConditionsToDataModel($bannerModel);
        }
        $searchResults->setItems($banners);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(BannerInterface $banner)
    {
        return $this->deleteById($banner->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($bannerId)
    {
        $banner = $this->bannerRegistry->retrieve($bannerId);
        if (null === $banner) {
            /** @var BannerInterface $banner */
            $banner = $this->bannerDataFactory->create();
            $this->entityManager->load($banner, $bannerId);
            if (!$banner->getId()) {
                throw NoSuchEntityException::singleField('bannerId', $bannerId);
            }
        }
        $this->entityManager->delete($banner);
        $this->bannerRegistry->remove($bannerId);
        return true;
    }

    /**
     * Retrieves banner data object using Banner Model
     *
     * @param BannerModel $banner
     * @return BannerInterface
     */
    private function getBannerDataObject(BannerModel $banner)
    {
        /** @var BannerInterface $bannerDataObject */
        $bannerDataObject = $this->bannerDataFactory->create();
        $banner = $this->convertBannerConditionsToDataModel($banner);
        $this->dataObjectHelper->populateWithArray(
            $bannerDataObject,
            $banner->getData(),
            BannerInterface::class
        );
        $bannerDataObject->setId($banner->getId());

        return $bannerDataObject;
    }

    /**
     * Convert banner conditions from array to data model
     *
     * @param BannerInterface $banner
     * @return BannerInterface
     */
    private function convertBannerConditionsToDataModel(BannerInterface $banner)
    {
        if ($banner->getProductCondition()) {
            $conditionArray = unserialize($banner->getProductCondition());
            $conditionDataModel = $this->conditionConverter
                ->arrayToDataModel($conditionArray);
            $banner->setProductCondition($conditionDataModel);
        } else {
            $banner->setProductCondition(null);
        }

        return $banner;
    }
}
