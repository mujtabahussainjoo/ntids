<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorx\ShippingRules\Model\Carrier\Method;

/**
 * Extended Method CRUD interface.
 * @api
 */
interface MethodRepositoryInterface
{
    /**
     * Save method.
     *
     * @param Method $method
     * @return Method
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Method $method);

    /**
     * Retrieve method.
     *
     * @param int $methodId
     * @return Method
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($methodId);

    /**
     * Retrieve method by its code
     *
     * @param string $code
     * @return \MageWorx\ShippingRules\Model\Carrier\Method|\MageWorx\ShippingRules\Api\Data\MethodInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode($code);

    /**
     * Retrieve methods matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param bool $returnRawObjects
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $returnRawObjects = false);

    /**
     * Delete method.
     *
     * @param Method $method
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Method $method);

    /**
     * Delete method by ID.
     *
     * @param int $methodId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($methodId);

    /**
     * Get empty Method
     *
     * @return Method|Data\MethodInterface
     */
    public function getEmptyEntity();
}
