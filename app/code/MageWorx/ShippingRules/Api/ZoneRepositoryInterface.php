<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorx\ShippingRules\Model\Zone;

/**
 * Pop-up Zone CRUD interface.
 * @api
 */
interface ZoneRepositoryInterface
{
    /**
     * Save zone.
     *
     * @param Zone $zone
     * @return Zone
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Zone $zone);

    /**
     * Retrieve zone.
     *
     * @param int $zoneId
     * @return Zone
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($zoneId);

    /**
     * Retrieve zones matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete zone.
     *
     * @param Zone $zone
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Zone $zone);

    /**
     * Delete zone by ID.
     *
     * @param int $zoneId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($zoneId);

    /**
     * Get empty zone
     *
     * @return Zone|Data\ZoneInterface
     */
    public function getEmptyEntity();
}
