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


// @codingStandardsIgnoreFile
// namespace Mirasvit_Ddeboer\Imap;

// use Mirasvit_Ddeboer\Imap\Exception\Exception;

/**
 * An IMAP mailbox (commonly referred to as a ‘folder’).
 */
class Mirasvit_Ddeboer_Imap_Mailbox implements IteratorAggregate
{
    protected $mailbox;
    protected $name;

    /**
     * Constructor.
     *
     * @param string     $name       Mailbox name
     * @param Connection $connection IMAP connection
     */
    public function __construct($name, Mirasvit_Ddeboer_Imap_Connection $connection)
    {
        $this->mailbox = $name;
        $this->connection = $connection;
        $this->name = substr($name, strpos($name, '}') + 1);
    }

    /**
     * Get mailbox name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get number of messages in this mailbox.
     *
     * @return int
     */
    public function count()
    {
        $this->init();

        return imap_num_msg($this->connection->getResource());
    }

    /**
     * Get message ids.
     *
     * @param Mirasvit_Ddeboer_Imap_SearchExpression|string $search Search expression (optional)
     *
     * @return Mirasvit_Ddeboer_Imap_MessageIterator
     */
    public function getMessages($search = null)
    {
        $this->init();

        $query = ($search ? (string) $search : 'ALL');

        // $messageNumbers = imap_search($this->connection->getResource(), $query, SE_UID);
        $messageNumbers = imap_search($this->connection->getResource(), $query);

        if (false == $messageNumbers) {
            // imap_search can also return false
            $messageNumbers = array();
        }

        return new Mirasvit_Ddeboer_Imap_MessageIterator($this->connection->getResource(), $messageNumbers);
    }

    /**
     * Get a message by message number.
     *
     * @param int $number Message number
     *
     * @return Mirasvit_Ddeboer_Imap_Message
     */
    public function getMessage($number)
    {
        $this->init();

        return new Mirasvit_Ddeboer_Imap_Message($this->connection->getResource(), $number);
    }

    /**
     * Get messages in this mailbox.
     *
     * @return MessageIterator
     */
    public function getIterator()
    {
        $this->init();

        return $this->getMessages();
    }

    /**
     * Delete this mailbox.
     */
    public function delete()
    {
        $this->connection->deleteMailbox($this);
    }

    /**
     * Delete all messages marked for deletion.
     *
     * @return Mirasvit_Ddeboer_Imap_Mailbox
     */
    public function expunge()
    {
        $this->init();

        imap_expunge($this->connection->getResource());

        return $this;
    }

    /**
     * Add a message to the mailbox.
     *
     * @param string $message
     *
     * @return bool
     */
    public function addMessage($message)
    {
        return imap_append($this->connection->getResource(), $this->mailbox, $message);
    }

    /**
     * If connection is not currently in this mailbox, switch it to this mailbox.
     */
    protected function init()
    {
        $check = imap_check($this->connection->getResource());
        if ($check->Mailbox != $this->mailbox) {
            imap_reopen($this->connection->getResource(), $this->mailbox);
        }
    }
}
