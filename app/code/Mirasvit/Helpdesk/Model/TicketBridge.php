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


namespace Mirasvit\Helpdesk\Model;

use \Mirasvit\Helpdesk\Api\Data\TicketInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class TicketBridge extends \Magento\Framework\Model\AbstractModel implements TicketInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::KEY_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(self::KEY_CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getExternalId()
    {
        return $this->getData(self::KEY_EXTERNAL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setExternalId($externalId)
    {
        return $this->setData(self::KEY_EXTERNAL_ID, $externalId);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->getData(self::KEY_USER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId($userId)
    {
        return $this->setData(self::KEY_USER_ID, $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->getData(self::KEY_SUBJECT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        return $this->setData(self::KEY_SUBJECT, $subject);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(self::KEY_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::KEY_DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriorityId()
    {
        return $this->getData(self::KEY_PRIORITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriorityId($priorityId)
    {
        return $this->setData(self::KEY_PRIORITY_ID, $priorityId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusId()
    {
        return $this->getData(self::KEY_STATUS_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusId($statusId)
    {
        return $this->setData(self::KEY_STATUS_ID, $statusId);
    }

    /**
     * {@inheritdoc}
     */
    public function getDepartmentId()
    {
        return $this->getData(self::KEY_DEPARTMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setDepartmentId($departmentId)
    {
        return $this->setData(self::KEY_DEPARTMENT_ID, $departmentId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::KEY_CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::KEY_CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteAddressId()
    {
        return $this->getData(self::KEY_QUOTE_ADDRESS_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteAddressId($quoteAddressId)
    {
        return $this->setData(self::KEY_QUOTE_ADDRESS_ID, $quoteAddressId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::KEY_CUSTOMER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::KEY_CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerName()
    {
        return $this->getData(self::KEY_CUSTOMER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::KEY_CUSTOMER_NAME, $customerName);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::KEY_ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::KEY_ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastReplyName()
    {
        return $this->getData(self::KEY_LAST_REPLY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastReplyName($lastReplyName)
    {
        return $this->setData(self::KEY_LAST_REPLY_NAME, $lastReplyName);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastReplyAt()
    {
        return $this->getData(self::KEY_LAST_REPLY_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastReplyAt($lastReplyAt)
    {
        return $this->setData(self::KEY_LAST_REPLY_AT, $lastReplyAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getReplyCnt()
    {
        return $this->getData(self::KEY_REPLY_CNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setReplyCnt($replyCnt)
    {
        return $this->setData(self::KEY_REPLY_CNT, $replyCnt);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::KEY_STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::KEY_STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::KEY_CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::KEY_CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::KEY_UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::KEY_UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getFolder()
    {
        return $this->getData(self::KEY_FOLDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setFolder($folder)
    {
        return $this->setData(self::KEY_FOLDER, $folder);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailId()
    {
        return $this->getData(self::KEY_EMAIL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailId($emailId)
    {
        return $this->setData(self::KEY_EMAIL_ID, $emailId);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstReplyAt()
    {
        return $this->getData(self::KEY_FIRST_REPLY_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstReplyAt($firstReplyAt)
    {
        return $this->setData(self::KEY_FIRST_REPLY_AT, $firstReplyAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstSolvedAt()
    {
        return $this->getData(self::KEY_FIRST_SOLVED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstSolvedAt($firstSolvedAt)
    {
        return $this->setData(self::KEY_FIRST_SOLVED_AT, $firstSolvedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getFpPeriodUnit()
    {
        return $this->getData(self::KEY_FP_PERIOD_UNIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFpPeriodUnit($fpPeriodUnit)
    {
        return $this->setData(self::KEY_FP_PERIOD_UNIT, $fpPeriodUnit);
    }

    /**
     * {@inheritdoc}
     */
    public function getFpPeriodValue()
    {
        return $this->getData(self::KEY_FP_PERIOD_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFpPeriodValue($fpPeriodValue)
    {
        return $this->setData(self::KEY_FP_PERIOD_VALUE, $fpPeriodValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getFpExecuteAt()
    {
        return $this->getData(self::KEY_FP_EXECUTE_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFpExecuteAt($fpExecuteAt)
    {
        return $this->setData(self::KEY_FP_EXECUTE_AT, $fpExecuteAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getFpIsRemind()
    {
        return $this->getData(self::KEY_FP_IS_REMIND);
    }

    /**
     * {@inheritdoc}
     */
    public function setFpIsRemind($fpIsRemind)
    {
        return $this->setData(self::KEY_FP_IS_REMIND, $fpIsRemind);
    }

    /**
     * {@inheritdoc}
     */
    public function getFpRemindEmail()
    {
        return $this->getData(self::KEY_FP_REMIND_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setFpRemindEmail($fpRemindEmail)
    {
        return $this->setData(self::KEY_FP_REMIND_EMAIL, $fpRemindEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getFpPriorityId()
    {
        return $this->getData(self::KEY_FP_PRIORITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setFpPriorityId($fpPriorityId)
    {
        return $this->setData(self::KEY_FP_PRIORITY_ID, $fpPriorityId);
    }

    /**
     * {@inheritdoc}
     */
    public function getFpStatusId()
    {
        return $this->getData(self::KEY_FP_STATUS_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setFpStatusId($fpStatusId)
    {
        return $this->setData(self::KEY_FP_STATUS_ID, $fpStatusId);
    }

    /**
     * {@inheritdoc}
     */
    public function getFpDepartmentId()
    {
        return $this->getData(self::KEY_FP_DEPARTMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setFpDepartmentId($fpDepartmentId)
    {
        return $this->setData(self::KEY_FP_DEPARTMENT_ID, $fpDepartmentId);
    }

    /**
     * {@inheritdoc}
     */
    public function getFpUserId()
    {
        return $this->getData(self::KEY_FP_USER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setFpUserId($fpUserId)
    {
        return $this->setData(self::KEY_FP_USER_ID, $fpUserId);
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        return $this->getData(self::KEY_CHANNEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setChannel($channel)
    {
        return $this->setData(self::KEY_CHANNEL, $channel);
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelData()
    {
        return $this->getData(self::KEY_CHANNEL_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setChannelData($channelData)
    {
        return $this->setData(self::KEY_CHANNEL_DATA, $channelData);
    }

    /**
     * {@inheritdoc}
     */
    public function getThirdPartyEmail()
    {
        return $this->getData(self::KEY_THIRD_PARTY_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setThirdPartyEmail($thirdPartyEmail)
    {
        return $this->setData(self::KEY_THIRD_PARTY_EMAIL, $thirdPartyEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchIndex()
    {
        return $this->getData(self::KEY_SEARCH_INDEX);
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchIndex($searchIndex)
    {
        return $this->setData(self::KEY_SEARCH_INDEX, $searchIndex);
    }

    /**
     * {@inheritdoc}
     */
    public function getCc()
    {
        return $this->getData(self::KEY_CC);
    }

    /**
     * {@inheritdoc}
     */
    public function setCc($cc)
    {
        return $this->setData(self::KEY_CC, $cc);
    }

    /**
     * {@inheritdoc}
     */
    public function getBcc()
    {
        return $this->getData(self::KEY_BCC);
    }

    /**
     * {@inheritdoc}
     */
    public function setBcc($bcc)
    {
        return $this->setData(self::KEY_BCC, $bcc);
    }

    /**
     * {@inheritdoc}
     */
    public function getMergedTicketId()
    {
        return $this->getData(self::KEY_MERGED_TICKET_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setMergedTicketId($mergedTicketId)
    {
        return $this->setData(self::KEY_MERGED_TICKET_ID, $mergedTicketId);
    }
}
