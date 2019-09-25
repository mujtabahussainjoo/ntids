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

class Draft extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Helpdesk\Model\DraftFactory
     */
    protected $draftFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Draft\CollectionFactory
     */
    protected $draftCollectionFactory;

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory
     */
    protected $userCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil
     */
    protected $helpdeskString;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Model\DraftFactory                          $draftFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Draft\CollectionFactory $draftCollectionFactory
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory       $userCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                    $date
     * @param \Mirasvit\Helpdesk\Helper\StringUtil                               $helpdeskString
     * @param \Magento\Framework\App\Helper\Context                          $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\DraftFactory $draftFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Draft\CollectionFactory $draftCollectionFactory,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Mirasvit\Helpdesk\Helper\StringUtil $helpdeskString,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->draftFactory = $draftFactory;
        $this->draftCollectionFactory = $draftCollectionFactory;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->date = $date;
        $this->helpdeskString = $helpdeskString;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @return void
     */
    public function clearDraft($ticket)
    {
        $ticketId = $ticket->getId();
        $collection = $this->draftCollectionFactory->create();
        $collection->addFieldToFilter('ticket_id', $ticketId);
        foreach ($collection as $item) {
            $item->delete();
        }

        return;
    }

    /**
     * @param int $ticketId
     *
     * @return bool|\Mirasvit\Helpdesk\Model\Draft
     */
    public function getSavedDraft($ticketId)
    {
        $collection = $this->draftCollectionFactory->create()
                ->addFieldToFilter('ticket_id', $ticketId);
        if ($collection->count()) {
            return $collection->getFirstItem();
        }

        return false;
    }

    /**
     * @param int         $ticketId
     * @param int         $userId
     * @param bool|string $text
     *
     * @return \Mirasvit\Helpdesk\Model\Draft
     */
    public function getCurrentDraft($ticketId, $userId, $text = false)
    {
        $collection = $this->draftCollectionFactory->create()
                ->addFieldToFilter('ticket_id', $ticketId);
        if ($collection->count()) {
            $draft = $collection->getFirstItem();
        } else {
            $draft = $this->draftFactory->create();
            $draft->setTicketId($ticketId);
        }
        $usersOnline = $draft->getUsersOnline();
        $timeNow = $this->date->gmtTimestamp();
        $usersOnline[$userId] = $timeNow;
        foreach ($usersOnline as $uId => $timestamp) {
            if ($uId == $userId) {
                continue;
            }
            if ($timestamp + 20 < $timeNow) { //other user went offline from this page
                unset($usersOnline[$uId]);
                continue;
            }
        }
        $draft->setUsersOnline($usersOnline);
        if ($text !== false) {
            $draft->setBody($text);
            $draft->setUpdatedBy($userId);
            $draft->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        $draft->save();

        return $draft;
    }

    /**
     * @param int         $ticketId
     * @param int         $userId
     * @param bool|string $text
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getNoticeMessage($ticketId, $userId, $text = false)
    {
        $draft = $this->getCurrentDraft($ticketId, $userId, $text);
        $ids = $draft->getUsersOnline();
        unset($ids[$userId]);
        $ids = array_keys($ids);
        if (!count($ids)) {
            return '';
        }
        $users = $this->userCollectionFactory->create()
                    ->addFieldToFilter('main_table.user_id', $ids);
        $userNames = [];
        $editNotice = '';
        foreach ($users as $user) {
            if (!$draft->getUser()) {
                continue;
            }
            if ($userId != $draft->getUser()->getId()) {
                $editNotice = __('%1 is editing now', $draft->getUser()->getName());
                continue;
            }
            $userNames[] = $user->getName();
        }
        if (count($userNames) == 0) {
            return $editNotice;
        }
        if (count($userNames) == 1) {
            return __('%1 has opened this ticket %2', implode(', ', $userNames), '<br>'.$editNotice);
        } else {
            return __('%1 have opened this ticket %2', implode(', ', $userNames), '<br>'.$editNotice);
        }
    }
}
