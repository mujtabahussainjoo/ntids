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

class FetchTest extends \PHPUnit\Framework\TestCase
{
    protected static $connection;

    /** @var  \Mirasvit\Helpdesk\Helper\Fetch */
    protected $helper;

    /** @var  \Mirasvit_Ddeboer_Imap_Mailbox */
    protected $mailbox;

    /** @var  \Magento\Framework\TestFramework\Unit\Helper\ObjectManager */
    protected $objectManager;

    /**
     * setUp.
     */
    public function setUp()
    {
        //        $this->markTestSkipped();
        if (!extension_loaded('imap')) {
            $this->markTestSkipped(
                'The IMAP extension is not available.'
            );
        }

        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Framework\App\ResourceConnection $resource */
        $resource = $this->objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $resource->getConnection()->delete('mst_helpdesk_email');
        $resource->getConnection()->delete('mst_helpdesk_ticket');
        $resource->getConnection()->delete('mst_helpdesk_attachment');

        $this->helper = $this->objectManager->get(\Mirasvit\Helpdesk\Helper\Fetch::class);
        $this->mailbox = $this->createMailbox('test-message');
        parent::setUp();
    }

    /**
     * tearDown.
     */
    public function tearDown()
    {
        //        $this->markTestSkipped();
        /** @var \Magento\Framework\App\ResourceConnection $resource */
        $resource = $this->objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $resource->getConnection()->delete('mst_helpdesk_email');
        $resource->getConnection()->delete('mst_helpdesk_ticket');
        $resource->getConnection()->delete('mst_helpdesk_attachment');
        $this->deleteMailbox($this->mailbox);
        parent::tearDown();
        $this->setUpBeforeClass();
        $this->mailbox = $this->createMailbox('test-message');
    }

    /**
     * connect to mailbox.
     */
    public static function setUpBeforeClass()
    {
        $server = new \Mirasvit_Ddeboer_Imap_Server('imap.gmail.com');
        static::$connection = $server->authenticate('support2@mirasvit.com.ua', '6Vl5gxZmxpeE');
    }

    /**
     * @return \Mirasvit_Ddeboer_Imap_Connection
     */
    protected static function getConnection()
    {
        return static::$connection;
    }

    /**
     * @param string $name
     *
     * @return object
     */
    protected function createMailbox($name)
    {
        $uniqueName = $name.uniqid();

        return static::getConnection()->createMailbox($uniqueName);
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Mailbox $mailbox
     */
    protected function deleteMailbox($mailbox)
    {
        if (is_object($mailbox)) {
            $mailbox->delete();
        }
    }

    /**
     * @param \Mirasvit_Ddeboer_Imap_Mailbox $mailbox
     * @param string                         $subject
     * @param string                         $contents
     * @param string                         $from
     * @param string                         $to
     */
    protected function createTestMessage(
        $mailbox,
        $subject = 'Don\'t panic!',
        $contents = 'Don\'t forget your towel',
        $from = 'someone@there.com',
        $to = 'me@here.com'
    ) {
        $message = "From: $from\r\n"
            ."To: $to\r\n"
            ."Subject: $subject\r\n"
            ."\r\n"
            ."$contents";

        $mailbox->addMessage($message);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function getFixt($file)
    {
        return file_get_contents(dirname(__FILE__)."/FetchTest/fixtures/$file");
    }


    /**
     * test.
     */
    public function testCreateEmailExchange()
    {
        $message = $this->getFixt('email_exchange.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);
        $this->assertEquals('support@2buy1click.com', $email->getFromEmail());
        $this->assertEquals('Support', $email->getSenderName());
        $this->assertEquals(Config::FORMAT_HTML, $email->getFormat());
        $this->assertNotEquals(0, strlen($email->getBody()));
    }

    /**
     * test.
     */
    public function testCreateEmailHtml5()
    {
        $message = $this->getFixt('email_html5.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);

        $this->assertEquals(Config::FORMAT_HTML, $email->getFormat());
        $this->assertEquals(32446, strlen($email->getBody()));
    }

    /**
     * test.
     */
    public function testCreateEmailZopim()
    {
        $message = $this->getFixt('email_zopim.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);
        $this->assertEquals('test@xxx.com', $email->getFromEmail());
        $this->assertEquals('Gibson', $email->getSenderName());
    }

    /**
     * test.
     */
    public function testCreateEmailInlineImageAttachment3()
    {
        $message = $this->getFixt('email_attachment_inline_image3.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = $attachment;
        }
        $this->assertEquals(1, count($attachments));

        $a = $attachments[0];
        $this->assertEquals('noname', $a->getName());
        $this->assertEquals('image', $a->getType());
    }

    /**
     * @magentoConfigFixture current_store helpdesk/general/attachment_storage db
     */
    public function testCreateEmailText()
    {
        $message = $this->getFixt('email_attachment_txt.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        // echo $message->getFrom()->getName();
        // echo $message->getUnsafeBodyHtml();
        // echo $message->getBodyText();
        // die;
        $email = $this->helper->createEmail($message);
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = $attachment;
        }
        $this->assertEquals(1, count($attachments));

        $a = $attachments[0];
        $this->assertEquals('Today 10-18.txt', $a->getName());
        $this->assertEquals('text', $a->getType());
        $this->assertEquals('asfasdf', $a->getBody());
        $this->assertEquals(Config::FORMAT_HTML, $email->getFormat());
        $this->assertNotEquals(0, strlen($email->getBody()));
    }

    /**
     * test.
     */
    public function testCreateEmailPlain()
    {
        $message = $this->getFixt('email_plain.eml');
        $this->mailbox->addMessage($message);
        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);

        $this->assertEquals(
            '<CAE6S9wiAJ9rELsoZdG7rBp70Tsj5EG+Lg7mSaf_32ANvKt7qGw@mail.gmail.com>',
            $email->getMessageId()
        );
        $this->assertEquals('Test Email', $email->getSubject());

        $this->assertEquals('terry@mirasvit.com.ua', $email->getFromEmail());
        $this->assertEquals('john@mirasvit.com.ua', $email->getToEmail());
        $this->assertEquals('body', trim($email->getBody()));
        $this->assertEquals(Config::FORMAT_PLAIN, $email->getFormat());
        $this->assertEquals('Terry Bib', $email->getSenderName());
        $this->assertNotEmpty($email->getHeaders());
    }

    /**
     * test.
     */
    public function testCreateEmailHtml()
    {
        $message = $this->getFixt('email_html.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);
        $this->assertEquals(Config::FORMAT_HTML, $email->getFormat());
    }

    /**
     * test.
     */
    public function testCreateEmailHtml2()
    {
        $message = $this->getFixt('email_html2.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);
        $this->assertEquals('contact@liq9.com', $email->getFromEmail());
        $this->assertEquals('ฝ่ายบริการลูกค้า | LIQ9.com', $email->getSenderName());
        $this->assertEquals(Config::FORMAT_HTML, $email->getFormat());
        $this->assertNotEquals(0, strlen($email->getBody()));
    }

    /**
     * test.
     */
    public function testCreateEmailHtml3()
    {
        $message = $this->getFixt('email_html3.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);
        $this->assertEquals('detazeta@gmail.com', $email->getFromEmail());//we have to remove from reply if we have it
        $this->assertEquals(
            'Offline Message from Sittidet: ทดสอบส่งผ่าน zopim...',
            $email->getSubject()
        );
        $this->assertEquals(Config::FORMAT_HTML, $email->getFormat());

        $this->assertEquals(false, $this->helper->createEmail($message));
    }

    /**
     * test.
     */
    public function testCreateEmailHtml4()
    {
        $message = $this->getFixt('email_html4.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        // echo $message->getFrom()->getName();
        // echo $message->getUnsafeBodyHtml();
        // echo $message->getBodyText();
        // die;
        $email = $this->helper->createEmail($message);
        // echo $email->getSenderName();
        // echo $email->getBody();
        $this->assertEquals('postmaster@hotmail.com', $email->getFromEmail());
        // $this->assertEquals('ฝ่ายบริการลูกค้า | LIQ9.com', $email->getSenderName());
        $this->assertEquals(Config::FORMAT_PLAIN, $email->getFormat());
        $this->assertNotEquals(0, strlen($email->getBody()));
    }

    /**
     * test.
     */
    public function testCreateEmailRussain()
    {
        $message = $this->getFixt('email_russian.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        // echo $message->getCharset();
        $email = $this->helper->createEmail($message);
        // @codingStandardsIgnoreStart
        $this->assertEquals(
            'Спасибо Александер, с наступающим Новым Годом Вас и команду Mirasvit!',
            trim($email->getBody()));
        // @codingStandardsIgnoreEnd
        $this->assertEquals(Config::FORMAT_PLAIN, $email->getFormat());
    }

    /**
     * test.
     */
    public function testCreateEmailWithAttachment()
    {
        $message = $this->getFixt('email_attachment.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = $attachment;
        }
        $this->assertEquals(3, count($attachments));

        $a = $attachments[0];
        $this->assertEquals('image.jpg', $a->getName());
        $this->assertEquals('image', $a->getType());
        $this->assertEquals(5237, $a->getSize());
        $this->assertEquals(5237, strlen($a->getBody()));
    }

    /**
     * test.
     */
    public function testCreateEmailWithInlineAttachment()
    {
        $message = $this->getFixt('email_attachment_inline_image.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = $attachment;
        }
        $this->assertEquals(2, count($attachments));

        $a = $attachments[0];
        $this->assertEquals('2d2ydh23.duj.png', $a->getName());
        $this->assertEquals('image', $a->getType());
        $this->assertEquals(24969, $a->getSize());
        $this->assertEquals(24969, strlen($a->getBody()));
    }

    /**
     * test.
     */
    public function testCreateEmailWithInlineAttachment2()
    {
        $message = $this->getFixt('email_attachment_inline_image2.eml');
        $this->mailbox->addMessage($message);

        $message = $this->mailbox->getMessage(1);
        $email = $this->helper->createEmail($message);
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $attachments[] = $attachment;
        }

        $this->assertEquals(1, count($attachments));

        $a = $attachments[0];
        $this->assertEquals('Outlook.jpg', $a->getName());
        $this->assertEquals('image', $a->getType());
    }
}
