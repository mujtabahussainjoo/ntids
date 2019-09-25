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

class Ticket extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketCollectionFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context                           $context
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory
    ) {
        $this->context = $context;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param int $amount
     * @return string
     */
    public function formatTicketLabel($amount)
    {
        if ($amount == 1) {
            return $amount . ' ' . __('Open Ticket');
        } else {
            return $amount . ' ' . __('Open Tickets');
        }
    }

    /**
     * @param int $customerId
     * @return int
     */
    public function getOpenTicketsAmount($customerId)
    {
        return $this->ticketCollectionFactory->create()->countOpenTickets($customerId);
    }
}