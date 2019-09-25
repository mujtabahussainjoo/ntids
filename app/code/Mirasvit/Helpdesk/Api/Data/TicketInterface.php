<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Helpdesk\Api\Data;

interface TicketInterface
{
    const KEY_ID                = 'ticket_id';
    const KEY_CODE              = 'code';
    const KEY_EXTERNAL_ID       = 'external_id';
    const KEY_USER_ID           = 'user_id';
    const KEY_SUBJECT           = 'subject';
    const KEY_DESCRIPTION       = 'description';
    const KEY_PRIORITY_ID       = 'priority_id';
    const KEY_STATUS_ID         = 'status_id';
    const KEY_DEPARTMENT_ID     = 'department_id';
    const KEY_CUSTOMER_ID       = 'customer_id';
    const KEY_QUOTE_ADDRESS_ID  = 'quote_address_id';
    const KEY_CUSTOMER_EMAIL    = 'customer_email';
    const KEY_CUSTOMER_NAME     = 'customer_name';
    const KEY_ORDER_ID          = 'order_id';
    const KEY_LAST_REPLY_NAME   = 'last_reply_name';
    const KEY_LAST_REPLY_AT     = 'last_reply_at';
    const KEY_REPLY_CNT         = 'reply_cnt';
    const KEY_STORE_ID          = 'store_id';
    const KEY_CREATED_AT        = 'created_at';
    const KEY_UPDATED_AT        = 'updated_at';
    const KEY_FOLDER            = 'folder';
    const KEY_EMAIL_ID          = 'email_id';
    const KEY_FIRST_REPLY_AT    = 'first_reply_at';
    const KEY_FIRST_SOLVED_AT   = 'first_solved_at';
    const KEY_FP_PERIOD_UNIT    = 'fp_period_unit';
    const KEY_FP_PERIOD_VALUE   = 'fp_period_value';
    const KEY_FP_EXECUTE_AT     = 'fp_execute_at';
    const KEY_FP_IS_REMIND      = 'fp_is_remind';
    const KEY_FP_REMIND_EMAIL   = 'fp_remind_email';
    const KEY_FP_PRIORITY_ID    = 'fp_priority_id';
    const KEY_FP_STATUS_ID      = 'fp_status_id';
    const KEY_FP_DEPARTMENT_ID  = 'fp_department_id';
    const KEY_FP_USER_ID        = 'fp_user_id';
    const KEY_CHANNEL           = 'channel';
    const KEY_CHANNEL_DATA      = 'channel_data';
    const KEY_THIRD_PARTY_EMAIL = 'third_party_email';
    const KEY_SEARCH_INDEX      = 'search_index';
    const KEY_CC                = 'cc';
    const KEY_BCC               = 'bcc';
    const KEY_MERGED_TICKET_ID  = 'merged_ticket_id';

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getExternalId();

    /**
     * @param string $externalId
     * @return $this
     */
    public function setExternalId($externalId);

    /**
     * @return int
     */
    public function getUserId();

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId);

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return int
     */
    public function getPriorityId();

    /**
     * @param int $priorityId
     * @return $this
     */
    public function setPriorityId($priorityId);

    /**
     * @return int
     */
    public function getStatusId();

    /**
     * @param int $statusId
     * @return $this
     */
    public function setStatusId($statusId);

    /**
     * @return int
     */
    public function getDepartmentId();

    /**
     * @param int $departmentId
     * @return $this
     */
    public function setDepartmentId($departmentId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @return int
     */
    public function getQuoteAddressId();

    /**
     * @param int $quoteAddressId
     * @return $this
     */
    public function setQuoteAddressId($quoteAddressId);

    /**
     * @return string
     */
    public function getCustomerEmail();

    /**
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * @return string
     */
    public function getCustomerName();

    /**
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * @return string
     */
    public function getLastReplyName();

    /**
     * @param string $lastReplyName
     * @return $this
     */
    public function setLastReplyName($lastReplyName);

    /**
     * @return string
     */
    public function getLastReplyAt();

    /**
     * @param string $lastReplyAt
     * @return $this
     */
    public function setLastReplyAt($lastReplyAt);

    /**
     * @return int
     */
    public function getReplyCnt();

    /**
     * @param int $replyCnt
     * @return $this
     */
    public function setReplyCnt($replyCnt);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return string
     */
    public function getFolder();

    /**
     * @param string $folder
     * @return $this
     */
    public function setFolder($folder);

    /**
     * @return int
     */
    public function getEmailId();

    /**
     * @param int $emailId
     * @return $this
     */
    public function setEmailId($emailId);

    /**
     * @return string
     */
    public function getFirstReplyAt();

    /**
     * @param string $firstReplyAt
     * @return $this
     */
    public function setFirstReplyAt($firstReplyAt);

    /**
     * @return string
     */
    public function getFirstSolvedAt();

    /**
     * @param string $firstSolvedAt
     * @return $this
     */
    public function setFirstSolvedAt($firstSolvedAt);

    /**
     * @return string
     */
    public function getFpPeriodUnit();

    /**
     * @param string $fpPeriodUnit
     * @return $this
     */
    public function setFpPeriodUnit($fpPeriodUnit);

    /**
     * @return int
     */
    public function getFpPeriodValue();

    /**
     * @param int $fpPeriodValue
     * @return $this
     */
    public function setFpPeriodValue($fpPeriodValue);

    /**
     * @return string
     */
    public function getFpExecuteAt();

    /**
     * @param string $fpExecuteAt
     * @return $this
     */
    public function setFpExecuteAt($fpExecuteAt);

    /**
     * @return int
     */
    public function getFpIsRemind();

    /**
     * @param int $fpIsRemind
     * @return $this
     */
    public function setFpIsRemind($fpIsRemind);

    /**
     * @return string
     */
    public function getFpRemindEmail();

    /**
     * @param string $fpRemindEmail
     * @return $this
     */
    public function setFpRemindEmail($fpRemindEmail);

    /**
     * @return int
     */
    public function getFpPriorityId();

    /**
     * @param int $fpPriorityId
     * @return $this
     */
    public function setFpPriorityId($fpPriorityId);

    /**
     * @return int
     */
    public function getFpStatusId();

    /**
     * @param int $fpStatusId
     * @return $this
     */
    public function setFpStatusId($fpStatusId);

    /**
     * @return int
     */
    public function getFpDepartmentId();

    /**
     * @param int $fpDepartmentId
     * @return $this
     */
    public function setFpDepartmentId($fpDepartmentId);

    /**
     * @return int
     */
    public function getFpUserId();

    /**
     * @param int $fpUserId
     * @return $this
     */
    public function setFpUserId($fpUserId);

    /**
     * @return string
     */
    public function getChannel();

    /**
     * @param string $channel
     * @return $this
     */
    public function setChannel($channel);

    /**
     * @return string
     */
    public function getChannelData();

    /**
     * @param string $channelData
     * @return $this
     */
    public function setChannelData($channelData);

    /**
     * @return string
     */
    public function getThirdPartyEmail();

    /**
     * @param string $thirdPartyEmail
     * @return $this
     */
    public function setThirdPartyEmail($thirdPartyEmail);

    /**
     * @return string
     */
    public function getSearchIndex();

    /**
     * @param string $searchIndex
     * @return $this
     */
    public function setSearchIndex($searchIndex);

    /**
     * @return string
     */
    public function getCc();

    /**
     * @param string $cc
     * @return $this
     */
    public function setCc($cc);

    /**
     * @return string
     */
    public function getBcc();

    /**
     * @param string $bcc
     * @return $this
     */
    public function setBcc($bcc);

    /**
     * @return string
     */
    public function getMergedTicketId();

    /**
     * @param string $mergedTicketId
     * @return $this
     */
    public function setMergedTicketId($mergedTicketId);
}
