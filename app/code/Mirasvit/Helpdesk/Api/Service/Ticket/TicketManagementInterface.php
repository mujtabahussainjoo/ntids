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


namespace Mirasvit\Helpdesk\Api\Service\Ticket;

use Mirasvit\Helpdesk\Api\Data\TicketInterface;

interface TicketManagementInterface
{
    /**
     * @param TicketInterface $ticket
     * @return bool
     */
    public function isRmaExists($ticket);

    /**
     * @param TicketInterface $ticket
     * @return array
     */
    public function getRmas($ticket);

    /**
     * @param TicketInterface $ticket
     * @return array
     */
    public function getRmasOptions($ticket);

    /**
     * @param TicketInterface $ticket
     * @return void
     */
    public function logTicketDeletion($ticket);
}