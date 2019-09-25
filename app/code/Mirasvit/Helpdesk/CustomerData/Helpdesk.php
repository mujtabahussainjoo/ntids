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


namespace Mirasvit\Helpdesk\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Mirasvit\Helpdesk\Helper\Ticket;
use Magento\Customer\Model\Session;

class Helpdesk implements SectionSourceInterface
{
    /**
     * @var Ticket
     */
    protected $ticket;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Session $customerSession
     * @param Ticket  $ticket
     */
    public function __construct(
        Session $customerSession,
        Ticket $ticket
    ) {
        $this->customerSession = $customerSession;
        $this->ticket = $ticket;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $amount = 0;
        if ($tickets = $this->getAmount()) {
            $amount = $this->ticket->formatTicketLabel($tickets);
        }

        return [
            'amount' => $amount,
        ];
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->ticket->getOpenTicketsAmount($this->customerSession->getCustomerId());
    }
}
