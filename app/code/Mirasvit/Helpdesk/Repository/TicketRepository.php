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


namespace Mirasvit\Helpdesk\Repository;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

use Mirasvit\Helpdesk\Model\Config;
use Mirasvit\Helpdesk\Repository;

class TicketRepository implements \Mirasvit\Helpdesk\Api\Repository\TicketRepositoryInterface
{
    use \Mirasvit\Helpdesk\Repository\RepositoryFunction\GetList;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket[]
     */
    protected $instances = [];

    public function __construct(
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket $ticketResource,
        \Mirasvit\Helpdesk\Api\Data\TicketSearchResultsInterfaceFactory $searchResultsFactory,
        \Mirasvit\Helpdesk\Api\Service\Ticket\TicketManagementInterface $ticketManagement
    ) {
        $this->objectFactory        = $ticketFactory;
        $this->ticketResource       = $ticketResource;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->ticketManagement     = $ticketManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function save($ticket)
    {
        $this->ticketResource->save($ticket);

        return $ticket;
    }

    /**
     * {@inheritdoc}
     */
    public function get($ticketId)
    {
        if (!isset($this->instances[$ticketId])) {
            /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
            $ticket = $this->objectFactory->create()->load($ticketId);
            if (!$ticket->getId()) {
                throw NoSuchEntityException::singleField('id', $ticketId);
            }
            $this->instances[$ticketId] = $ticket;
        }

        return $this->instances[$ticketId];
    }

    /**
     * {@inheritdoc}
     */
    public function delete($ticket)
    {
        try {
            $ticketId = $ticket->getId();
            $this->ticketResource->delete($ticket);
            $this->ticketManagement->logTicketDeletion($this);
        } catch (\Exception $e) {
            throw new StateException(
                __(
                    'Cannot delete ticket with id %1',
                    $ticket->getId()
                ),
                $e
            );
        }
        unset($this->instances[$ticketId]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($ticketId)
    {
        $ticket = $this->get($ticketId);

        return  $this->delete($ticket);
    }
}
