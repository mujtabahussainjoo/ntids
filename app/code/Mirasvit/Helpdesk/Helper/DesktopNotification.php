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

use \Mirasvit\Helpdesk\Model;
use \Mirasvit\Helpdesk\Model\Config;

class DesktopNotification
{
    /**
     * @var Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $auth;

    /**
     * @param \Magento\Framework\Registry                               $registry
     * @param Model\Config                                              $config
     * @param Model\ResourceModel\DesktopNotification\CollectionFactory $desktopNotificationCollectionFactory
     * @param Model\DesktopNotificationFactory                          $desktopNotificationFactory
     * @param Model\ResourceModel\Ticket\CollectionFactory              $ticketCollectionFactory
     * @param \Magento\Backend\Model\Auth                               $auth
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        Model\Config $config,
        Model\ResourceModel\DesktopNotification\CollectionFactory $desktopNotificationCollectionFactory,
        Model\DesktopNotificationFactory $desktopNotificationFactory,
        Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Magento\Backend\Model\Auth $auth
    ) {
        $this->registry = $registry;
        $this->config = $config;
        $this->desktopNotificationCollectionFactory = $desktopNotificationCollectionFactory;
        $this->desktopNotificationFactory = $desktopNotificationFactory;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->auth = $auth;
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Model\ResourceModel\Ticket\Collection
     */
    public function getNewTickets()
    {
        $collection = $this->ticketCollectionFactory->create();
        $collection->getSelect()->columns('*')
            ->where('folder=' . \Mirasvit\Helpdesk\Model\Config::FOLDER_INBOX .
                ' AND user_id = 0');

        return $collection;
    }

    /**
     * @return int
     */
    public function getNewTicketsNumber()
    {
        $collection = $this->ticketCollectionFactory->create();
        $collection->getSelect()->columns('COUNT(*) as cnt')
            ->where('folder=' . \Mirasvit\Helpdesk\Model\Config::FOLDER_INBOX .
                ' AND user_id = 0');

        return $collection->load()->getFirstItem()->getCnt();
    }


    /**
     * @param \Magento\User\Model\User|\Magento\Backend\Model\Auth\Credential\StorageInterface $user
     * @return int
     */
    public function getUserMessagesNumber($user)
    {
        $collection = $this->ticketCollectionFactory->create();

        $collection->getSelect()->columns('COUNT(*) as cnt')
            ->where('main_table.user_id = ' . $user->getId() .
                ' AND folder=' . \Mirasvit\Helpdesk\Model\Config::FOLDER_INBOX);

        return $collection->load()->getFirstItem()->getCnt();
    }

    /**
     * @param \Magento\User\Model\User $user
     * @return array
     * @throws \Exception
     */
    public function getUnreadMeassagesForUser($user)
    {
        $collection = $this->desktopNotificationCollectionFactory->create()->getMessagesCollection($user);
        $messages = [];
        /** @var \Mirasvit\Helpdesk\Model\DesktopNotification $notification */
        foreach ($collection as $notification) {
            $ticket = $notification->getTicket();
            $store = $ticket->getStore();
            $storeName = '';
            if ($store) {
                $storeName = $store->getName();
            }
            $message = [
                'ticket_id' => $notification->getTicketId(),
                'url' => $notification->getTicket()->getBackendUrl(),
                'title' => __('%1 Help Desk', $storeName),
                'message' => $this->getMessage($user, $notification),
            ];
            $notification->addReadByUserId($user->getId());
            $notification->save();

            if ($message['message'] != false) {
                $messages[] = $message;
            }
        }

        if (($count = count($messages)) > 3) {
            $messages = array_slice($messages, -3);
            $messages[0]['message'] = __('You have %1 unread notifications.', $count);
        }

        return $messages;
    }

    /**
     * @param \Magento\User\Model\User $user
     * @param \Mirasvit\Helpdesk\Model\DesktopNotification $notification
     * @return \Magento\Framework\Phrase|string
     */
    private function getMessage($user, $notification)
    {
        $message = false;
        switch ($notification->getNotificationType()) {
            case Config::NOTIFICATION_TYPE_NEW_TICKET:
                if ($this->isAllowedNotificationAboutNewTicket($user, $notification)) {
                    $message = __('Ticket "%1" was created.', $notification->getSubject());
                }
                break;
            case Config::NOTIFICATION_TYPE_NEW_MESSAGE:
                if ($this->getConfig()->getDesktopNotificationAboutMessage()) {
                    $message = __(
                        'New message was added to the ticket "%1"', $notification->getSubject()
                    );
                }
                break;
            case Config::NOTIFICATION_TYPE_NEW_ASSIGN:
                $userId = $notification->getTicket()->getUserId();
                if ($this->getConfig()->getDesktopNotificationAboutAssign() && $userId == $user->getId()) {
                    $message = __('Ticket "%1" was assigned to you.', $notification->getSubject());
                }
                break;
        }
        return $message;
    }

    /**
     * @param \Magento\User\Model\User $user
     * @param \Mirasvit\Helpdesk\Model\DesktopNotification $notification
     * @return bool
     */
    private function isAllowedNotificationAboutNewTicket($user, $notification)
    {
        $isAllowedForAllUsers = in_array(
            Config\Source\Notification\Users::ALL_USERS,
            $this->getConfig()->getDesktopNotificationAboutTicketUserIds()
        );
        $isAllowedForCurrentUser = in_array(
                $user->getId(), $this->getConfig()->getDesktopNotificationAboutTicketUserIds()
            );
        if (!$isAllowedForAllUsers || !$isAllowedForCurrentUser) {
            return false;
        }
        $userId = $notification->getTicket()->getUserId();
        if ($userId == 0 || $userId == $user->getId()) { //ticket is not assigned yet
            return true;
        }
        return false;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Message $message
     * @return void
     */
    public function onMessageCreated(\Mirasvit\Helpdesk\Model\Message $message)
    {
        if (!$this->registry->registry('is_ticked_created')) {
            $ticket = $message->getTicket();
            if ($ticket->getUserId() != $ticket->getOrigData('user_id')) {
                $this->registry->register('is_new_message_and_assign', 1);
                $this->createNotification($ticket, Config::NOTIFICATION_TYPE_NEW_ASSIGN);
            } else {
                $this->createNotification($message, Config::NOTIFICATION_TYPE_NEW_MESSAGE);
            }
        }
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @return void
     */
    public function onTicketCreated($ticket)
    {
        $this->registry->register('is_ticked_created', 1);
        $this->createNotification($ticket, Config::NOTIFICATION_TYPE_NEW_TICKET);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @return void
     */
    public function onTicketChanged($ticket)
    {
        if ($this->registry->registry('is_new_message_and_assign')) { //notified earlier
            return;
        }
        if ($ticket->getUserId() != $ticket->getOrigData('user_id')) {
            $this->createNotification($ticket, Config::NOTIFICATION_TYPE_NEW_ASSIGN);
        }
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket|int $ticket
     * @return Model\DesktopNotification
     */
    public function getNotificationsByTicket($ticket)
    {
        if (is_object($ticket)) {
            $ticket = $ticket->getId();
        }

        return $this->desktopNotificationCollectionFactory->create()->addFieldToFilter('ticket_id', $ticket);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Message|\Mirasvit\Helpdesk\Model\Ticket $object
     * @param string                                                           $type
     * @throws \Exception
     * @return void
     */
    protected function createNotification($object, $type)
    {
        //remove old messages about this ticket
        $collection = $this->desktopNotificationCollectionFactory->create()
            ->addFieldToFilter('ticket_id', $object->getTicketId());
        foreach ($collection as $item) {
            $item->delete();
        }

        //add new message
        /** @var \Mirasvit\Helpdesk\Model\DesktopNotification $notification */
        $notification = $this->desktopNotificationFactory->create()
            ->setTicketId($object->getTicketId())
            ->setMessageId($object->getMessageId())
            ->setNotificationType($type)
        ;
        if ($user = $this->auth->getUser()) {
            $notification->addReadByUserId($user->getId());
        }
        $notification->save();
    }
}