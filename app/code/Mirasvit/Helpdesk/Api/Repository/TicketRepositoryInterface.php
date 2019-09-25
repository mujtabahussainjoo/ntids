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



namespace Mirasvit\Helpdesk\Api\Repository;


interface TicketRepositoryInterface
{
    /**
     * @param \Mirasvit\Helpdesk\Api\Data\TicketInterface|\Mirasvit\Helpdesk\Model\Ticket $ticket
     * @return \Mirasvit\Helpdesk\Api\Data\TicketInterface|\Mirasvit\Helpdesk\Model\Ticket
     */
    public function save($ticket);

    /**
     * @param int $ticketId
     * @return \Mirasvit\Helpdesk\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($ticketId);

    /**
     * @param \Mirasvit\Helpdesk\Api\Data\TicketInterface $ticket Ticket which will deleted
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete($ticket);

    /**
     * @param int $ticketId
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($ticketId);
}
