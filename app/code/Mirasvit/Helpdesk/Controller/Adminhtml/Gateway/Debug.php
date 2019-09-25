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


namespace Mirasvit\Helpdesk\Controller\Adminhtml\Gateway;

class Debug extends \Mirasvit\Helpdesk\Controller\Adminhtml\Gateway
{

    protected function fetch($emailNumber)
    {
        $objectManager = $this->context->getObjectManager();
        /** @var \Mirasvit\Helpdesk\Helper\Fetch $fetchHelper */
        $fetchHelper = $objectManager->get("\Mirasvit\Helpdesk\Helper\Fetch");
        $id = (int)$this->getRequest()->getParam('id');
        $gateway = $this->gatewayFactory->create()->load($id);

        $fetchHelper->connect($gateway);
        $mailbox = $fetchHelper->getMailbox();
        $message = $mailbox->getMessage($emailNumber);
        $fetchHelper->createEmail($message);
        $fetchHelper->close();
        echo "done";
    }

    protected function raw($emailNumber)
    {
        header("Content-Type: text/plain");
        $objectManager = $this->context->getObjectManager();
        /** @var \Mirasvit\Helpdesk\Helper\Fetch $fetchHelper */
        $fetchHelper = $objectManager->get("\Mirasvit\Helpdesk\Helper\Fetch");
        $id = (int)$this->getRequest()->getParam('id');
        $gateway = $this->gatewayFactory->create()->load($id);

        $fetchHelper->connect($gateway);
        $mailbox = $fetchHelper->getMailbox();
        $raw_full_email = imap_fetchbody($mailbox->connection->getResource(), $emailNumber, "", FT_PEEK);
        echo $raw_full_email;
        $fetchHelper->close();
    }

    /**
     *
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('action') == 'fetch') {
            $this->fetch($this->getRequest()->getParam('email_number'));
            die;
        }

        if ($this->getRequest()->getParam('action') == 'raw') {
            $this->raw($this->getRequest()->getParam('email_number'));
            die;
        }

        $objectManager = $this->context->getObjectManager();
        /** @var \Mirasvit\Helpdesk\Helper\Fetch $fetchHelper */
        $fetchHelper = $objectManager->get("\Mirasvit\Helpdesk\Helper\Fetch");
        $id = (int)$this->getRequest()->getParam('id');
        $gateway = $this->gatewayFactory->create()->load($id);

        $fetchHelper->connect($gateway);
        $mailbox = $fetchHelper->getMailbox();
        $emails = $mailbox->getMessages();
        //        $emails = $mailbox->getMessages('SUBJECT "8 Days of Gains"');
        echo "Number of emails:".count($emails)."<br>";
        $limit = 10;
        if (count($emails) < $limit) {
            $limit = count($emails);
        }
        echo "Show last $limit emails<br>";
        for($i = count($emails); $i > count($emails) - $limit ; $i--) {
            /** @var \Mirasvit_Ddeboer_Imap_Message $email */
            $email = $mailbox->getMessage($i);
            /* output the email header information */
            echo ' - ' . $i . ': ';
            if($email->isSeen()) {
                echo "[<font color='green'>read</font>]";
            } else {
                echo "[<font color='red'>unread</font>]";
            }
            echo " ".$email->getSubject()." | ".$email->getFrom()." | ";
            echo "<a href='".$this->getUrl("*/*/*", ["id"=>$id, "action"=>"fetch", "email_number" => $email->getNumber()])."'>fetch again</a> ";
            echo "<a href='".$this->getUrl("*/*/*", ["id"=>$id, "action"=>"raw", "email_number" => $email->getNumber()])."'>raw</a>";
            echo "<br>";
        }
        $fetchHelper->close();

    }


}
