<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorx\ShippingRules\Model\Carrier\Method\Rate;

/**
 * Extended Rate CRUD interface.
 * @api
 */
interface RateRepositoryInterface
{
    /**
     * Save rate.
     *
     * @param Rate $rate
     * @return Rate
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Rate $rate);

    /**
     * Retrieve rate.
     *
     * @param int $rateId
     * @return Rate
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($rateId);

    /**
     * Retrieve rate by its code
     *
     * @param string $code
     * @return \MageWorx\ShippingRules\Model\Carrier\Method\Rate|\MageWorx\ShippingRules\Api\Data\RateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode($code);

    /**
     * Retrieve rates matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $returnRawObjects
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $returnRawObjects = false);

    /**
     * Delete rate.
     *
     * @param Rate $rate
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Rate $rate);

    /**
     * Delete rate by ID.
     *
     * @param int $rateId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($rateId);

    /**
     * Get empty Rate
     *
     * @return Rate|Data\RateInterface
     */
    public function getEmptyEntity();
}
