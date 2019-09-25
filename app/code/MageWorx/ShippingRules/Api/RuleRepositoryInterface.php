<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorx\ShippingRules\Model\Rule;

/**
 * Extended Rule CRUD interface.
 * @api
 */
interface RuleRepositoryInterface
{
    /**
     * Save rule.
     *
     * @param Rule $rule
     * @return Rule
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Rule $rule);

    /**
     * Retrieve rule.
     *
     * @param int $ruleId
     * @return Rule
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($ruleId);

    /**
     * Retrieve rules matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete rule.
     *
     * @param Rule $rule
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Rule $rule);

    /**
     * Delete rule by ID.
     *
     * @param int $ruleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($ruleId);

    /**
     * Get empty rule
     *
     * @return Rule|Data\RuleInterface
     */
    public function getEmptyEntity();
}
