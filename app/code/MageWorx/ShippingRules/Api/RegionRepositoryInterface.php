<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorx\ShippingRules\Model\Region;

/**
 * Extended Region CRUD interface.
 * @api
 */
interface RegionRepositoryInterface
{
    /**
     * Save region.
     *
     * @param Region $region
     * @return Region
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Region $region);

    /**
     * Retrieve region.
     *
     * @param int $regionId
     * @return Region
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($regionId);

    /**
     * Retrieve regions matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete region.
     *
     * @param Region $region
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Region $region);

    /**
     * Delete region by ID.
     *
     * @param int $regionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($regionId);

    /**
     * Get empty Region
     *
     * @return Region|Data\RegionInterface
     */
    public function getEmptyEntity();
}
