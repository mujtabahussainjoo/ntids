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
// namespace Mirasvit_Ddeboer\Imap\Message;

class Mirasvit_Ddeboer_Imap_Message_EmailAddress
{
    protected $mailbox;
    protected $hostname;
    protected $name;
    protected $address;

    public function __construct($mailbox, $hostname, $name = null)
    {
        $this->mailbox = $mailbox;
        $this->hostname = $hostname;
        $this->name = $name;
        $this->address = $mailbox.'@'.$hostname;
    }

    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Returns address with person name.
     *
     * @return string
     */
    public function getFullAddress()
    {
        if ($this->name) {
            $address = sprintf('%s <%s@%s>', $this->name, $this->mailbox, $this->hostname);
        } else {
            $address = sprintf('%s@%s', $this->mailbox, $this->hostname);
        }

        return $address;
    }

    public function getMailbox()
    {
        return $this->mailbox;
    }

    public function getHostname()
    {
        return $this->hostname;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->getAddress();
    }

    public static function fromString($address)
    {
        $parts = explode('@', $address);

        return new self($parts[0], $parts[1]);
    }
}
