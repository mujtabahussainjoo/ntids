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


namespace Mirasvit\Helpdesk\Repository\Ticket;

use Mirasvit\Helpdesk\Model\Config;
use Mirasvit\Helpdesk\Repository;

class FolderRepository implements \Mirasvit\Helpdesk\Api\Repository\Ticket\FolderRepositoryInterface
{
    public function __construct(
        \Mirasvit\Helpdesk\Model\EmailFactory $emailFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket $ticketResource
    ) {
        $this->emailFactory   = $emailFactory;
        $this->ticketResource = $ticketResource;
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
    public function markAsInbox($ticket)
    {
        /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
        $ticket->setFolder(Config::FOLDER_INBOX);
        $this->save($ticket);
        if ($emailId = $ticket->getEmailId()) {
            $email = $this->emailFactory->create()->load($emailId);
            $email->setPatternId(0)->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function markAsArchive($ticket)
    {
        /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
        $ticket->setFolder(Config::FOLDER_ARCHIVE);
        $this->save($ticket);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsSpam($ticket)
    {
        /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
        $ticket->setFolder(Config::FOLDER_SPAM);
        $this->save($ticket);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsNotSpam($ticket)
    {
        $this->markAsInbox($ticket);
    }

}
