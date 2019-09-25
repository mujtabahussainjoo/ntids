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

class Satisfaction extends \Magento\Framework\DataObject
{
    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\SatisfactionFactory
     */
    protected $satisfactionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory
     */
    protected $messageCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\CollectionFactory
     */
    protected $satisfactionCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Notification
     */
    protected $helpdeskNotification;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Model\TicketFactory                                $ticketFactory
     * @param \Mirasvit\Helpdesk\Model\SatisfactionFactory                          $satisfactionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory      $messageCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\CollectionFactory $satisfactionCollectionFactory
     * @param \Mirasvit\Helpdesk\Helper\Notification                                $helpdeskNotification
     * @param \Magento\Framework\App\Helper\Context                                 $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Mirasvit\Helpdesk\Model\SatisfactionFactory $satisfactionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory $messageCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\CollectionFactory $satisfactionCollectionFactory,
        \Mirasvit\Helpdesk\Helper\Notification $helpdeskNotification,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->ticketFactory = $ticketFactory;
        $this->satisfactionFactory = $satisfactionFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->satisfactionCollectionFactory = $satisfactionCollectionFactory;
        $this->helpdeskNotification = $helpdeskNotification;
        $this->context = $context;
        parent::__construct();
    }

    /**
     * @param string $messageUid
     * @param int    $rate
     *
     * @return \Mirasvit\Helpdesk\Model\Satisfaction
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addRate($messageUid, $rate)
    {
        $message = $this->getMessageByUid($messageUid);
        $satisfaction = $this->getSatisfactionByMessage($message);

        $ticket = $this->ticketFactory->create()->load($message->getTicketId());

        $satisfaction->setTicketId($message->getTicketId())
            ->setMessageId($message->getId())
            ->setCustomerId($message->getCustomerId())
            ->setUserId($message->getUserId())
            ->setStoreId($ticket->getStoreId())
            ->setRate($rate)
            ->save();

        $this->helpdeskNotification->sendNotificationStaffNewSatisfaction($satisfaction);

        return $satisfaction;
    }

    /**
     * @param string $messageUid
     * @param string $comment
     *
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addComment($messageUid, $comment)
    {
        $message = $this->getMessageByUid($messageUid);
        $satisfaction = $this->getSatisfactionByMessage($message);
        $satisfaction->setComment($comment)
            ->save();

        $this->helpdeskNotification->sendNotificationStaffNewSatisfaction($satisfaction);
    }

    /**
     * @param string $messageUid
     *
     * @return \Mirasvit\Helpdesk\Model\Message
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMessageByUid($messageUid)
    {
        $messages = $this->messageCollectionFactory->create()
                    ->addFieldToFilter('uid', $messageUid);
        if (!$messages->count()) {
            throw new \Magento\Framework\Exception\LocalizedException('Wrong URL');
        }
        $message = $messages->getFirstItem();

        return $message;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Message $message
     *
     * @return \Mirasvit\Helpdesk\Model\Satisfaction
     */
    public function getSatisfactionByMessage($message)
    {
        $satisfactions = $this->satisfactionCollectionFactory->create()
            ->addFieldToFilter('message_id', $message->getId());
        if ($satisfactions->count()) {
            $satisfaction = $satisfactions->getFirstItem();
        } else {
            $satisfaction = $this->satisfactionFactory->create();
        }
        $satisfaction->setTicketId($message->getTicketId());

        return $satisfaction;
    }
}
