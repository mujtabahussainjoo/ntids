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
// use Mirasvit_Ddeboer\Imap\Exception\Mirasvit_Ddeboer_Imap_Exception_MailboxDoesNotExistException;

/**
 * A connection to an IMAP server that is authenticated for a user.
 */
class Mirasvit_Ddeboer_Imap_Connection
{
    protected $server;
    protected $resource;
    protected $mailboxes;
    protected $mailboxNames;

    /**
     * Constructor.
     *
     * @param \resource $resource
     * @param string    $server
     *
     * @throws InvalidArgumentException
     */
    public function __construct($resource, $server)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('$resource must be a resource');
        }

        $this->resource = $resource;
        $this->server = $server;
    }

    /**
     * Get a list of mailboxes (also known as folders).
     *
     * @return Mailbox[]
     */
    public function getMailboxes()
    {
        if (null === $this->mailboxes) {
            foreach ($this->getMailboxNames() as $mailboxName) {
                $this->mailboxes[] = $this->getMailbox($mailboxName);
            }
        }

        return $this->mailboxes;
    }

    /**
     * Get a mailbox by its name.
     *
     * @param string $name Mailbox name
     *
     * @return  Mirasvit_Ddeboer_Imap_Mailbox
     *
     * @throws Mirasvit_Ddeboer_Imap_Exception_MailboxDoesNotExistException If mailbox does not exist
     */
    public function getMailbox($name)
    {
        if (!in_array($name, $this->getMailboxNames())) {
            throw new Mirasvit_Ddeboer_Imap_Exception_MailboxDoesNotExistException($name);
        }

        return new Mirasvit_Ddeboer_Imap_Mailbox($this->server.imap_utf7_encode($name), $this);
    }

    /**
     * Count number of messages not in any mailbox.
     *
     * @return int
     */
    public function count()
    {
        return imap_num_msg($this->resource);
    }

    /**
     * Create mailbox.
     *
     * @param $name
     *
     * @return Mailbox
     *
     * @throws Exception
     */
    public function createMailbox($name)
    {
        if (imap_createmailbox($this->resource, $this->server.$name)) {
            $this->mailboxNames = $this->mailboxes = null;

            return $this->getMailbox($name);
        }

        throw new Mirasvit_Ddeboer_Imap_Exception_Exception("Can not create '{$name}' mailbox at '{$this->server}'");
    }

    /**
     * Close connection.
     *
     * @param int $flag
     *
     * @return bool
     */
    public function close($flag = 0)
    {
        return imap_close($this->resource, $flag);
    }

    public function deleteMailbox(Mirasvit_Ddeboer_Imap_Mailbox $mailbox)
    {
        if (false === imap_deletemailbox(
            $this->resource,
            $this->server.$mailbox->getName()
        )) {
            throw new Mirasvit_Ddeboer_Imap_Exception_Exception('Mailbox '.$mailbox->getName().' could not be deleted');
        }

        $this->mailboxes = $this->mailboxNames = null;
    }

    /**
     * Get IMAP resource.
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    public function getMailboxNames()
    {
        if (null === $this->mailboxNames) {
            $mailboxes = imap_getmailboxes($this->resource, $this->server, '*');
            foreach ($mailboxes as $mailbox) {
                $this->mailboxNames[] = imap_utf7_decode(str_replace($this->server, '', $mailbox->name));
            }
        }

        return $this->mailboxNames;
    }
}
