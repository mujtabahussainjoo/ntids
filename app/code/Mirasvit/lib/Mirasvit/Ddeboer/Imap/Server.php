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

// use Mirasvit_Ddeboer\Imap\Exception\AuthenticationFailedException;

/**
 * An IMAP server.
 */
class Mirasvit_Ddeboer_Imap_Server
{
    /**
     * @var string Internet domain name or bracketed IP address of server
     */
    protected $hostname;

    /**
     * @var int TCP port number
     */
    protected $port;

    /**
     * @var string Optional flags
     */
    protected $flags;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Constructor.
     *
     * @param string $hostname Internet domain name or bracketed IP address of server
     * @param int    $port     TCP port number
     * @param string $flags    Optional flags
     */
    public function __construct($hostname, $port = 993, $flags = '/imap/ssl/validate-cert')
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->flags = '/'.ltrim($flags, '/');
    }

    /**
     * Authenticate connection.
     *
     * @param string $username Username
     * @param string $password Password
     *
     * @return \Mirasvit_Ddeboer\Imap\Connection
     *
     * @throws AuthenticationFailedException
     */
    public function authenticate($username, $password)
    {
        $resource = @imap_open($this->getServerString(), $username, $password, null, 1);

        if (false === $resource) {
            throw new Mirasvit_Ddeboer_Imap_Exception_AuthenticationFailedException($username);
        }

        $check = imap_check($resource);
        $mailbox = $check->Mailbox;
        $this->connection = substr($mailbox, 0, strpos($mailbox, '}') + 1);

        // These are necessary to get rid of PHP throwing IMAP errors
        imap_errors();
        imap_alerts();

        return new Mirasvit_Ddeboer_Imap_Connection($resource, $this->connection);
    }

    /**
     * Glues hostname, port and flags and returns result.
     *
     * @return string
     */
    protected function getServerString()
    {
        return "{{$this->hostname}:{$this->port}{$this->flags}}";
    }
}
