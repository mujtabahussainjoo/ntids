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

class Followup extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Helpdesk\Helper\Notification
     */
    protected $helpdeskNotification;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Helper\Notification $helpdeskNotification
     * @param \Magento\Framework\App\Helper\Context  $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Helper\Notification $helpdeskNotification,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->helpdeskNotification = $helpdeskNotification;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     *
     * @return void
     */
    public function process($ticket)
    {
        if ($ticket->getFpPriorityId()) {
            $ticket->setPriorityId($ticket->getFpPriorityId());
        }
        if ($ticket->getFpStatusId()) {
            $ticket->setStatusId($ticket->getFpStatusId());
        }
        if ($ticket->getFpDepartmentId()) {
            $ticket->setDepartmentId($ticket->getFpDepartmentId());
        }
        if ($ticket->getFpUserId()) {
            $ticket->setUserId($ticket->getFpUserId());
        }
        if ($ticket->getFpIsRemind()) {
            $this->helpdeskNotification->sendNotificationReminder($ticket);
        }
        $ticket->setData('fp_execute_at', null)
                ->setData('fp_period_value', null)
                ->setData('fp_period_unit', null)
                ->setData('fp_is_remind', false);
        $ticket->save();
    }
}
