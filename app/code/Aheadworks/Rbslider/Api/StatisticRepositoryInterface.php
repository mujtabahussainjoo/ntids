<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Api;

/**
 * Statistic CRUD interface.
 * @api
 */
interface StatisticRepositoryInterface
{
    /**
     * Save statistic.
     *
     * @param \Aheadworks\Rbslider\Api\Data\StatisticInterface $statistic
     * @return \Aheadworks\Rbslider\Api\Data\StatisticInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Rbslider\Api\Data\StatisticInterface $statistic);

    /**
     * Retrieve statistic.
     *
     * @param int $statisticId
     * @return \Aheadworks\Rbslider\Api\Data\StatisticInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($statisticId);

    /**
     * Retrieve statistics matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Rbslider\Api\Data\StatisticSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete statistic.
     *
     * @param \Aheadworks\Rbslider\Api\Data\StatisticInterface $statistic
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\Rbslider\Api\Data\StatisticInterface $statistic);

    /**
     * Delete statistic by ID.
     *
     * @param int $statisticId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($statisticId);
}
