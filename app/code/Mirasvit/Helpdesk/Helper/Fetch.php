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



namespace Mirasvit\Helpdesk\Helper;

use Mirasvit\Helpdesk\Model\Config as Config;

require_once dirname(__FILE__) . "/../../lib/Mirasvit/Ddeboer/Imap/load.php";

/**
 * Class Fetch.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Fetch extends \Magento\Framework\DataObject
{
    public function __construct(
        \Mirasvit\Helpdesk\Model\GatewayFactory $gatewayFactory,
        \Mirasvit\Helpdesk\Model\EmailFactory $emailFactory,
        \Mirasvit\Helpdesk\Model\AttachmentFactory $attachmentFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Email\CollectionFactory $emailCollectionFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->gatewayFactory         = $gatewayFactory;
        $this->emailFactory           = $emailFactory;
        $this->attachmentFactory      = $attachmentFactory;
        $this->emailCollectionFactory = $emailCollectionFactory;
        $this->config                 = $config;
        $this->context                = $context;

        parent::__construct();
    }

    /**
     * @var \Mirasvit\Helpdesk\Model\Gateway
     */
    protected $gateway;

    /**
     * @var  \Mirasvit_Ddeboer_Imap_Connection
     */
    protected $connection;

    /**
     * @var  \Mirasvit_Ddeboer_Imap_Mailbox
     */
    protected $mailbox;

    /**
     * @return bool
     */
    public function isDev()
    {
        return $this->config->getDeveloperIsActive();
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Gateway $gateway
     *
     * @return bool
     */
    public function connect($gateway)
    {
        $this->validate($gateway);

        $this->gateway = $gateway;
        $flags = sprintf('/%s', $gateway->getProtocol());
        if ($gateway->getEncryption() == 'ssl') {
            $flags .= '/ssl';
        }
        $flags .= '/novalidate-cert';

        $server = new \Mirasvit_Ddeboer_Imap_Server($gateway->getHost(), $gateway->getPort(), $flags);
        if (function_exists('imap_timeout')) {
            imap_timeout(1, 20);
        }
        if (!$this->connection = $server->authenticate($gateway->getLogin(), $gateway->getPassword())) {
            return false;
        }

        $mailboxes = $this->connection->getMailboxNames();
        if (trim($gateway->getFolder()) && in_array($gateway->getFolder(), $mailboxes)) {
            $mailboxName = $gateway->getFolder();
        } else {
            if (in_array('INBOX', $mailboxes)) {
                $mailboxName = 'INBOX';
            } elseif (in_array('Inbox', $mailboxes)) {
                $mailboxName = 'Inbox';
            } else {
                $mailboxName = $mailboxes[0];
            }
        }

        $this->mailbox = $this->connection->getMailbox($mailboxName);

        return true;
    }

    /**
     * @return \Mirasvit_Ddeboer_Imap_Mailbox
     */
    public function getMailbox()
    {
        return $this->mailbox;
    }

    /**
     * @return void
     */
    public function close()
    {
        $this->connection->close();
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     *
     * @return string|bool
     */
    public function getFromEmail($message)
    {
        // if we have reply to, we will set it as "from", because we will not reply on it
        $fromEmail = false;
        if ($message->getReplyTo() && $message->getReplyTo()->getAddress()) {
            $fromEmail = $message->getReplyTo()->getAddress();
        } elseif ($message->getFrom()) {
            $fromEmail = $message->getFrom()->getAddress();
        }

        return $fromEmail;
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     *
     * @return string|bool
     */
    public function getFromName($message)
    {
        // if we have reply to, we will set it as "from", because we will not reply on it
        $fromName = 'unknown';
        if ($message->getReplyTo() && $message->getReplyTo()->getName()) {
            $fromName = $message->getReplyTo()->getName();
        } elseif ($message->getFrom()) {
            $fromName = $message->getFrom()->getName();
        }

        return $fromName;
    }

    /**
     * @return array
     */
    public function getGatewayEmails()
    {
        $collection = $this->gatewayFactory->create()->getCollection()->addFieldToFilter('is_active', 1);

        $emails = array();
        if ($collection->count()) {
            /** @var \Mirasvit\Helpdesk\Model\Gateway $gateway */
            foreach ($collection as $gateway) {
                $emails[] = $gateway->getEmail();
            }
        }
        return $emails;

    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     *
     * @return bool|\Mirasvit\Helpdesk\Model\Email
     *
     */
    public function createEmail($message)
    {
        if ($this->isMessageFetched($message)) {
            return false;
        }
        $format = $this->getMessageFormat($message);
        $body = $this->getMessageBody($message, $format);
        $to = $this->getTo($message);
        $cc = $this->getCc($message);
        $headers = $this->getHeaders($message);
        $fromEmail = $this->getFromEmail($message);
        $senderName = $this->getFromName($message);

        $mailingDate = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        if ($message->getDate()) {
            $date = new \DateTime($message->getDate());
            $mailingDate = $date->getTimestamp();
        }

        $email = $this->emailFactory->create()
            ->setMessageId($message->getId())
            ->setFromEmail($fromEmail)
            ->setSenderName($senderName)
            ->setToEmail(implode($to, ','))
            ->setCc(implode($cc, ', '))
            ->setSubject($message->getSubject())
            ->setMailingDate($mailingDate)
            ->setBody($body)
            ->setFormat($format)
            ->setHeaders($headers)
            ->setIsProcessed(false);
        if ($this->gateway) { //may be null during tests
            $email->setGatewayId($this->gateway->getId());
        }

        $gateways = $this->getGatewayEmails();
        if ($this->isMessageAutosubmitted($message) || in_array($fromEmail, $gateways)) {
            $email->setIsProcessed(true);
            $email->save();

            return $email;
        }

        $email->save();
        $this->saveAttachments($email, $message);

        return $email;
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     * @param string                         $format
     * @return string
     */
    protected function getMessageBody($message, $format)
    {
        if ($format == Config::FORMAT_PLAIN) {
            $body = $message->getBodyText();
        } else {
            $body = $message->getBodyHtml();
            if (empty($body)) { //html can be even in plain body
                $body = $message->getBodyText();
            }
        }
        $bodySizeLimit = ($format == Config::FORMAT_HTML) ? 1000000 : 10000;

        if (strlen($body) > $bodySizeLimit) {
            $body = substr($body, 0, $bodySizeLimit);
        }

        $body = $this->removeGoogleServiceTags($body);

        return $body;
    }

    /**
     * Google insert tag <wbr> for all long words. Our guest ID is long word
     *
     * @param string $body
     * @return string
     */
    private function removeGoogleServiceTags($body)
    {
        return str_replace('<wbr>', '', $body);
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     * @return string
     */
    protected function getMessageFormat($message)
    {
        $bodyHtml = $message->getBodyHtml();
        $bodyPlain = $message->getBodyText();
        if (empty($bodyHtml)) {
            $format = Config::FORMAT_PLAIN;
            $tags = ['<div', '<br', '<tr'];
            foreach ($tags as $tag) {
                if (stripos($bodyPlain, $tag) !== false) {
                    $format = Config::FORMAT_HTML;
                    break;
                }
            }
        } else {
            $format = Config::FORMAT_HTML;
        }
        return $format;
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     * @return string
     */
    protected function getHeaders($message)
    {
        $headers = $message->getHeaders()->toString();
        if (strlen($headers) > 10000) {
            $headers = substr($headers, 0, 10000); //headers includes attached files. they can have huge size.
        }
        return $headers;
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     * @return array
     */
    protected function getTo($message)
    {
        $to = [];
        foreach ($message->getTo() as $email) {
            $to[] = $email->getAddress();
        }
        return $to;
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     * @return array
     */
    protected function getCc($message)
    {
        $cc = [];
        foreach ($message->getCc() as $copy) {
            $cc[] = $copy->mailbox . '@' . $copy->host;
        }
        return $cc;
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     * @return bool
     */
    protected function isMessageAutosubmitted($message)
    {
        // All Auto-Submitted emails are marked as processed to prevent infinity cycles
        // http://www.iana.org/assignments/auto-submitted-keywords/auto-submitted-keywords.xhtml
        if (stripos($message->getHeaders()->toString(), 'Auto-Submitted: auto-replied') !== false
            || stripos($message->getHeaders()->toString(), 'Auto-Submitted: auto-notified') !== false
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     * @return bool
     */
    protected function isMessageFetched($message)
    {
        $emails = $this->emailCollectionFactory->create()
            ->addFieldToFilter('message_id', $message->getId())
            ->addFieldToFilter('from_email', $this->getFromEmail($message));

        if ($emails->count()) {
            return true;
        }
        return false;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Email $email
     * @param \Mirasvit_Ddeboer_Imap_Message $message
     *
     * @return void
     */
    protected function saveAttachments($email, $message)
    {
        $attachments = $message->getAttachments();

        if ($attachments) {
            foreach ($attachments as $a) {
                $attachment = $this->attachmentFactory->create();
                $attachment->setName($a->getFilename())
                    ->setType($a->getType())
                    ->setSize($a->getSize())
                    ->setEmailId($email->getId())
                    ->setBody($a->getDecodedContent())
                    ->save();
            }
        }
    }

    /**
     * @return void
     * @throws \Mirasvit_Ddeboer_Imap_Exception_MessageCannotBeDeletedException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function fetchEmails()
    {
        $msgs = 0;
        $max = $this->gateway->getFetchMax();

        $messages = $this->mailbox->getMessages('UNSEEN');
        $emailsNumber = $this->mailbox->count();

        if ($limit = $this->gateway->getFetchLimit()) {
            $start = $emailsNumber - $limit + 1;
            if ($start < 1) {
                $start = 1;
            }
            for ($num = $start; $num <= $emailsNumber; ++$num) {
                try { // we can have different errors during fetching of email.
                    // we don't want to stop fetching of all queue.
                    $message = $this->mailbox->getMessage($num);
                    if ($message->getErrors()) { // we do not want log imap errors
                        continue;
                    }
                    if ($this->createEmail($message)) {
                        if ($this->gateway->getIsDeleteEmails()) {
                            $message->delete();
                            $this->mailbox->expunge();
                        }
                        ++$msgs;
                    }
                    if ($max && $msgs >= $max) {
                        break;
                    }
                } catch (\Exception $e) {
                    $this->context->getLogger()->error($e->getMessage());
                }
            }
        } else {
            foreach ($messages as $message) {
                if ($message->getErrors()) { // we do not want log imap errors
                    continue;
                }
                try { //we can have different errors during fetching of email.
                    // we don't want to stop fetching of all queue.
                    if ($this->createEmail($message)) {
                        if ($this->gateway->getIsDeleteEmails()) {
                            $message->delete();
                            $this->mailbox->expunge();
                        }
                        ++$msgs;
                    }
                    if ($max && $msgs >= $max) {
                        break;
                    }
                } catch (\Exception $e) {
                    $this->context->getLogger()->error($e->getMessage());
                }
            }
        }
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Gateway $gateway
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return bool
     */
    public function fetch($gateway)
    {
        $this->validate();

        if (!$this->connect($gateway)) {
            return false;
        }
        $this->fetchEmails();
        $this->close();

        return true;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return bool
     */
    public function validate()
    {
        if (!function_exists('imap_open')) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Can't fetch.
                Please, ask your hosting provider to enable IMAP extension in PHP configuration of your server.")
            );
        }

        return true;
    }
}
