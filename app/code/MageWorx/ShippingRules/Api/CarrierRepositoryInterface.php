<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorx\ShippingRules\Model\Carrier;

/**
 * Extended Carrier CRUD interface.
 * @api
 */
interface CarrierRepositoryInterface
{
    /**
     * Save carrier.
     *
     * @param Carrier $carrier
     * @return Carrier
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Carrier $carrier);

    /**
     * Retrieve carrier.
     *
     * @param int $carrierId
     * @return Carrier
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($carrierId);

    /**
     * Retrieve carrier by its code
     *
     * @param string $code
     * @return \MageWorx\ShippingRules\Model\Carrier|\MageWorx\ShippingRules\Api\Data\CarrierInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode($code);

    /**
     * Retrieve carriers matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $returnRawObjects
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $returnRawObjects = false);

    /**
     * Delete carrier.
     *
     * @param Carrier $carrier
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Carrier $carrier);

    /**
     * Delete carrier by ID.
     *
     * @param int $carrierId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($carrierId);

    /**
     * Get empty Carrier
     *
     * @return Carrier|Data\CarrierInterface
     */
    public function getEmptyEntity();
}
