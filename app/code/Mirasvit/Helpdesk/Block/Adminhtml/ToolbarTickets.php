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


namespace Mirasvit\Helpdesk\Block\Adminhtml;

/**
 * Customer account dropdown link
 */
class ToolbarTickets extends \Magento\Backend\Block\Template
{
    public function __construct(
        \Mirasvit\Helpdesk\Helper\DesktopNotification $helpdeskTicket,
        \Mirasvit\Helpdesk\Helper\User $helpdeskUser,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config         = $config;
        $this->helpdeskUser   = $helpdeskUser;
        $this->helpdeskTicket = $helpdeskTicket;
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Config
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @return int
     */
    public function getNotificationInterval()
    {
        return $this->getConfig()->getDesktopNotificationCheckPeriod();
    }

    /**
     * @return string
     */
    public function getCheckNotificationUrl()
    {
        return $this->getUrl('helpdesk/ticket/checknotification');
    }

    /**
     * @return string
     */
    public function getNotificationDescriptionLength()
    {
        return 100;
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection
     */
    public function getLatestNewTickets()
    {
        return $this->helpdeskTicket->getNewTickets($this->getUser());
    }

    /**
     * @return int
     */
    public function getNewTickets()
    {
        return $this->helpdeskTicket->getNewTicketsNumber($this->getUser());
    }

    /**
     * @return int
     */
    public function getUserTickets()
    {
        return $this->helpdeskTicket->getUserMessagesNumber($this->getUser());
    }

    /**
     * @return bool|\Mirasvit\Helpdesk\Model\User
     */
    public function getUser()
    {
        return $this->helpdeskUser->getHelpdeskUser();
    }
}
