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



namespace Mirasvit\Helpdesk\Helper;

use Mirasvit\Helpdesk\Model\Config as Config;

class Ruleevent extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Tag
     */
    protected $helpdeskTag;

    /**
     * @var \Mirasvit\Helpdesk\Helper\History
     */
    protected $helpdeskHistory;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Notification
     */
    protected $helpdeskNotification;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Rule\CollectionFactory   $ruleCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\TicketFactory                                 $ticketFactory
     * @param \Mirasvit\Helpdesk\Model\Config                                 $config
     * @param \Mirasvit\Helpdesk\Helper\Tag                                   $helpdeskTag
     * @param \Mirasvit\Helpdesk\Helper\History                               $helpdeskHistory
     * @param \Mirasvit\Helpdesk\Helper\Notification                          $helpdeskNotification
     * @param \Magento\Framework\App\Helper\Context                           $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Helpdesk\Helper\Tag $helpdeskTag,
        \Mirasvit\Helpdesk\Helper\History $helpdeskHistory,
        \Mirasvit\Helpdesk\Helper\Notification $helpdeskNotification,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->ticketFactory = $ticketFactory;
        $this->config = $config;
        $this->helpdeskTag = $helpdeskTag;
        $this->helpdeskHistory = $helpdeskHistory;
        $this->helpdeskNotification = $helpdeskNotification;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @var array
     */
    protected $sentEmails = [];

    /**
     * @var array
     */
    protected $processedEvents = [];

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $eventType
     *
     * @return void
     */
    public function newEventCheck($eventType)
    {
        $this->sentEmails = false; //on one email address we can send few emails
        $rules = $this->ruleCollectionFactory->create()
            ->addFieldToFilter('is_active', true)
            ->addFieldToFilter('event', $eventType)
            ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER)
            ;
        $tickets = $this->ticketCollectionFactory->create()
            ->addFieldToFilter('folder', Config::FOLDER_INBOX)
            ;

        foreach ($tickets as $ticket) {
            foreach ($rules as $rule) {
                $rule->afterLoad();
                if (!$rule->validate($ticket)) {
                    continue;
                }
                $this->processRule($rule, $ticket);
                if ($rule->getIsStopProcessing()) {
                    break;
                }
            }
        }
    }

    /**
     * @param string                          $eventType
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     *
     * @return void
     */
    public function newEvent($eventType, $ticket)
    {
        $key = $eventType.$ticket->getId();
        if (isset($this->processedEvents[$key])) {
            return;
        } else {
            $this->processedEvents[$key] = true;
        }

        $this->sentEmails = [];
        $collection = $this->ruleCollectionFactory->create()
            ->addFieldToFilter('is_active', true)
            ->addFieldToFilter('event', $eventType)
            ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER)
            ;

        foreach ($collection as $rule) {
            $rule->afterLoad();
            if (!$rule->validate($ticket)) {
                continue;
            }
            $this->processRule($rule, $ticket);
            if ($rule->getIsStopProcessing()) {
                break;
            }
        }
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Rule   $rule
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function processRule($rule, $ticket)
    {
        //reload ticket, because we don't need to show changes non-related to current rule in history
        $stateBefore = $ticket->getState();
        /* set attributes **/
        if ($rule->getStatusId()) {
            $ticket->setStatusId($rule->getStatusId());
        }
        if ($rule->getPriorityId()) {
            $ticket->setPriorityId($rule->getPriorityId());
        }
        if ($rule->getDepartmentId()) {
            $ticket->setDepartmentId($rule->getDepartmentId());
        }
        if ($rule->getUserId()) {
            $ticket->setUserId($rule->getUserId());
        }

        if ($rule->getIsArchive() == Config::IS_ARCHIVE_TO_ARCHIVE) {
            $ticket->setFolder(Config::FOLDER_ARCHIVE);
        } elseif ($rule->getIsArchive() == Config::IS_ARCHIVE_FROM_ARCHIVE) {
            $ticket->setFolder(Config::FOLDER_INBOX);
        }

        if ($tags = $rule->getAddTags()) {
            $this->helpdeskTag->addTags($ticket, $tags);
        }
        if ($tags = $rule->getRemoveTags()) {
            $this->helpdeskTag->removeTags($ticket, $tags);
        }
        $ticket->save();

        /* send notifications **/
        $isSendDepartment = false;
        if ($rule->getIsSendOwner()) {
            if ($user = $ticket->getUser()) {
                $this->_sendEventNotification($user->getEmail(), $user->getName(), $rule, $ticket);
            } else {
                $isSendDepartment = true;
            }
        }
        if ($rule->getIsSendDepartment() || $isSendDepartment) {
            foreach ($ticket->getDepartment()->getUsers() as $user) {
                $this->_sendEventNotification($user->getEmail(), $user->getName(), $rule, $ticket);
            }
        }
        if ($rule->getIsSendUser()) { //small bug here. better to name it getIsSendCustomer
            if ($customer = $ticket->getCustomer()) {
                $this->_sendEventNotification($customer->getEmail(), $customer->getName(), $rule, $ticket);
            }
        }
        if ($otherEmail = $rule->getOtherEmail()) {
            $this->_sendEventNotification($otherEmail, '', $rule, $ticket);
        }
        $stateAfter = $ticket->getState();

        $this->helpdeskHistory->changeTicket($ticket, $stateBefore, $stateAfter, Config::RULE, ['rule' => $rule]);
    }

    /**
     * @param string                          $email
     * @param string                          $name
     * @param \Mirasvit\Helpdesk\Model\Rule   $rule
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     *
     * @return void
     */
    protected function _sendEventNotification($email, $name, $rule, $ticket)
    {
        if (!is_array($this->sentEmails) || !in_array($email, $this->sentEmails)) {
            $variables = [
                'email_subject' => $rule->getEmailSubject(),
                'email_body' => $rule->getEmailBody(),
            ];
            $template = $this->config->getNotificationRuleTemplate($ticket->getStoreId());
            $attachments = [];
            if ($rule->getIsSendAttachment()) {
                $attachments = $ticket->getLastMessage()->getAttachments();
            }

            $email = (strpos($email, ',')) ? explode(',', $email) : (array) $email;

            $this->helpdeskNotification
                ->mail($ticket, false, false, $email, $name, $template, $attachments, $variables);
            $this->sentEmails[] = array_merge($this->sentEmails, $email);
        }
    }
}
