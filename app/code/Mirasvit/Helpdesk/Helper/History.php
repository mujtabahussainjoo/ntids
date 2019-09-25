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

class History extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Helpdesk\Model\HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\StatusFactory
     */
    protected $statusFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\PriorityFactory
     */
    protected $priorityFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Model\HistoryFactory    $historyFactory
     * @param \Mirasvit\Helpdesk\Model\StatusFactory     $statusFactory
     * @param \Mirasvit\Helpdesk\Model\PriorityFactory   $priorityFactory
     * @param \Magento\User\Model\UserFactory            $userFactory
     * @param \Mirasvit\Helpdesk\Model\DepartmentFactory $departmentFactory
     * @param \Mirasvit\Helpdesk\Model\TicketFactory     $ticketFactory
     * @param \Magento\Framework\App\Helper\Context      $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\HistoryFactory $historyFactory,
        \Mirasvit\Helpdesk\Model\StatusFactory $statusFactory,
        \Mirasvit\Helpdesk\Model\PriorityFactory $priorityFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Mirasvit\Helpdesk\Model\DepartmentFactory $departmentFactory,
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->historyFactory = $historyFactory;
        $this->statusFactory = $statusFactory;
        $this->priorityFactory = $priorityFactory;
        $this->userFactory = $userFactory;
        $this->departmentFactory = $departmentFactory;
        $this->ticketFactory = $ticketFactory;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @param string                          $triggeredBy - type, like Config::USER, Config::RULE
     * @param array                           $by - array of objects
     *
     * @return \Mirasvit\Helpdesk\Model\History
     */
    public function getHistoryRecord($ticket, $triggeredBy, $by)
    {
        $history = $this->historyFactory->create();
        $history->setTicketId($ticket->getId());
        $history->setTriggeredBy($triggeredBy);
        switch ($triggeredBy) {
            case Config::CUSTOMER:
                $history->setName($by['customer']->getName());
                break;
            case Config::USER:
                $history->setName($by['user']->getName());
                break;
            case Config::THIRD:
                $history->setName($by['email']->getSenderNameOrEmail());
                break;
            case Config::RULE:
                $history->setName(__("Rule '%1'", $by['rule']->getName()));
                break;
        }
        return $history;
    }

    /**
     * Returns a text like 'Status: In Progress => Closed'
     *
     * @param \Magento\Framework\DataObject $stateBefore
     * @param \Magento\Framework\DataObject $stateAfter
     * @param object $fieldFactory
     * @param string $field
     * @param string $fieldLabel
     * @return bool|\Magento\Framework\Phrase
     */
    protected function getText($stateBefore, $stateAfter, $fieldFactory, $field, $fieldLabel)
    {
        $text = false;
        if ($stateBefore->getData($field) != $stateAfter->getData($field)) {
            if ($stateBefore->getData($field)) {
                $oldStatus = $fieldFactory->create()->load($stateBefore->getData($field));
                $newStatus = $fieldFactory->create()->load($stateAfter->getData($field));
                $text = __(
                    '%1: %2 => %3',
                    $fieldLabel,
                    $oldStatus->getName(),
                    $newStatus->getName()
                );
            } else {
                $newStatus = $fieldFactory->create()->load($stateAfter->getData($field));
                $text = __('%1: %2', $fieldLabel, $newStatus->getName());
            }
        }
        return $text;
    }

    /**
     * We call this functions after changes of the ticket
     *
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @param \Magento\Framework\DataObject $stateBefore
     * @param \Magento\Framework\DataObject $stateAfter
     * @param string                          $triggeredBy
     * @param string                          $by
     *
     * @return void
     */
    public function changeTicket($ticket, $stateBefore, $stateAfter, $triggeredBy, $by)
    {
        if (!$ticket->getId()) { //new ticket
            return;
        }
        $history = $this->getHistoryRecord($ticket, $triggeredBy, $by);
        $text = [];
        $text[] = $this->getText($stateBefore, $stateAfter, $this->statusFactory, 'status_id', __('Status'));
        $text[] = $this->getText($stateBefore, $stateAfter, $this->priorityFactory, 'priority_id', __('Priority'));
        $text[] = $this->getText($stateBefore, $stateAfter, $this->userFactory, 'user_id', __('Owner'));
        $text[] = $this->getText(
            $stateBefore, $stateAfter, $this->departmentFactory, 'department_id', __('Department')
        );
        $text = array_diff($text, [false]);//remove empty values

        if ($ticket->getMergedTicketId()) {
            $newTicket = $this->ticketFactory->create()->load($ticket->getMergedTicketId());
            $text[] = __('Ticket was merged to: %1', $newTicket->getCode());
        }
        if (isset($by['codes'])) {
            $text[] = __('Ticket was merged with: %1', implode(', ', $by['codes']));
        }
        $history->addMessage($text);
    }

    /**
     * We call this functions after adding messages to the ticket
     *
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @param string                          $triggeredBy
     * @param string                          $by
     * @param string                          $messageType
     *
     * @return void
     */
    public function addMessage($ticket, $triggeredBy, $by, $messageType)
    {
        $history = $this->getHistoryRecord($ticket, $triggeredBy, $by);
        $text = [];
        switch ($messageType) {
            case Config::MESSAGE_PUBLIC:
                $text[] = __('Message added');
                break;
            case Config::MESSAGE_INTERNAL:
                $text[] = __('Internal note added');
                break;
            case Config::MESSAGE_PUBLIC_THIRD:
                $text[] = __('Third party message added');
                break;
            case Config::MESSAGE_INTERNAL_THIRD:
                $text[] = __('Private third party message added');
                break;
        }
        $history->addMessage($text);
    }
}
