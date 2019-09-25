<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api\Data;

interface RuleInterface
{
    /**
     * Get specified action types for the rule
     *
     * @return mixed[]
     */
    public function getActionType();

    /**
     * Get corresponding shipping methods
     *
     * @return string
     */
    public function getShippingMethods();

    /**
     * Get amounts
     *
     * @return mixed[]
     */
    public function getAmount();

    /**
     * Get simple action
     *
     * @return string
     */
    public function getSimpleAction();

    /**
     * Is need to stop another rules
     *
     * @return bool
     */
    public function getStopRulesProcessing();

    /**
     * Get all disabled shipping methods
     *
     * @return mixed[]
     */
    public function getDisabledShippingMethods();

    /**
     * Days of week in PHP: from 0 to 6
     *
     * @return int
     */
    public function getDaysOfWeek();

    /**
     * Time from & to in minutes from 0 to 1440
     *
     * @return int
     */
    public function getTimeFrom();

    /**
     * Time from & to in minutes from 0 to 1440
     *
     * @return int
     */
    public function getTimeTo();

    /**
     * Is rule use time
     *
     * @return bool
     */
    public function getUseTime();

    /**
     * Is time enabled in the rule
     *
     * @return bool
     */
    public function getTimeEnabled();

    /**
     * Get shipping rule customer group Ids
     *
     * @return mixed[]
     */
    public function getCustomerGroupIds();

    /**
     * Get rule condition combine model instance
     *
     * @return \MageWorx\ShippingRules\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance();

    /**
     * Get rule condition product combine model instance
     *
     * @return \MageWorx\ShippingRules\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance();

    /**
     * Get rule associated store Ids
     *
     * @return mixed[]
     */
    public function getStoreIds();

    /**
     * Retrieve rule name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName();

    /**
     * Get rules description text
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get date when rule is active from
     *
     * @return mixed
     */
    public function getFromDate();

    /**
     * Get date when rule is active to
     *
     * @return mixed
     */
    public function getToDate();

    /**
     * Check is rule active
     *
     * @return int|bool
     */
    public function getIsActive();

    /**
     * Get serialized rules conditions
     *
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * Get serialized rules actions
     *
     * @return string
     */
    public function getActionsSerialized();

    /**
     * Sort Order of the rule
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Retrieve rule ID
     *
     * @return int
     */
    public function getRuleId();

    /**
     * Flag: display or not error message for the disabled methods
     *
     * @return int
     */
    public function getDisplayErrorMessage();

    /**
     * Get error message with a {{carrier_title}} and {{method_title}} variables in the body
     *
     * @return string
     */
    public function getErrorMessage();

    /**
     * Get store specific error messages with a {{carrier_title}} and {{method_title}} variables in the bodies
     *
     * @return mixed[]
     */
    public function getStoreErrmsgs();

    /**
     * Get changed titles of shipping methods
     *
     * @return mixed[]
     */
    public function getChangedTitles();
}
