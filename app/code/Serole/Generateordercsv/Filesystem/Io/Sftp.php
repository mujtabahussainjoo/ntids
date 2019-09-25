<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Serole\Generateordercsv\Filesystem\Io;


class Sftp extends \Magento\Framework\Filesystem\Io\Sftp
{
    const REMOTE_TIMEOUT = 10;
    const SSH2_PORT = 22;

    /**
     * @var \phpseclib\Net\SFTP $_connection
     */
    protected $_connection = null;

    /**
     * Open a SFTP connection to a remote site.
     *
     * @param array $args Connection arguments
     *        string $args[host] Remote hostname
     *        string $args[username] Remote username
     *        string $args[password] Connection password
     *        int $args[timeout] Connection timeout [=10]
     * @return void
     * @throws \Exception
     */
    public function open(array $args = [])
    {
        if (!isset($args['timeout'])) {
            $args['timeout'] = self::REMOTE_TIMEOUT;
        }
        if (strpos($args['host'], ':') !== false) {
            list($host, $port) = explode(':', $args['host'], 2);
        } else {
            $host = $args['host'];
            $port = self::SSH2_PORT;
        }
        $this->_connection = new \phpseclib\Net\SFTP($host, $port, $args['timeout']);
        $connectionStatus = $this->_connection->login($args['username'],$args['password']);
        if (!$connectionStatus) {
            throw new \Exception(
                sprintf("Unable to open SFTP connection as %s@%s", $args['username'], $args['host'])
            );
        }else{
            return $connectionStatus;
        }
    }

}
