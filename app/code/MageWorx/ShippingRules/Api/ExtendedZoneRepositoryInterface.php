<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorx\ShippingRules\Model\ExtendedZone;

/**
 * Pop-up Zone CRUD interface.
 * @api
 */
interface ExtendedZoneRepositoryInterface
{
    /**
     * Save Pop-up Zone.
     *
     * @param ExtendedZone $extendedZone
     * @return ExtendedZone
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(ExtendedZone $extendedZone);

    /**
     * Retrieve Pop-up Zone.
     *
     * @param int $extendedZoneId
     * @return ExtendedZone
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($extendedZoneId);

    /**
     * Retrieve Pop-up Zones matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Pop-up Zone.
     *
     * @param ExtendedZone $extendedZone
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ExtendedZone $extendedZone);

    /**
     * Delete Pop-up Zone by ID.
     *
     * @param int $extendedZoneId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($extendedZoneId);

    /**
     * Get empty zone
     *
     * @return ExtendedZone|Data\ExtendedZoneDataInterface
     */
    public function getEmptyEntity();
}
