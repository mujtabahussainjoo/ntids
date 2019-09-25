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



namespace Mirasvit\Helpdesk\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @covers \Mirasvit\Helpdesk\Helper\StringUtil
 * @SuppressWarnings(PHPMD)
 */
class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stringHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Email|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskEmailMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->ticketCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ticketCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection',
            ['load',
            'save',
            'delete',
            'addFieldToFilter',
            'setOrder',
            'getFirstItem',
            'getLastItem', ],
            [],
            '',
            false
        );
        $this->ticketCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ticketCollectionMock));
        $this->configMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->helpdeskEmailMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Email',
            [],
            [],
            '',
            false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->stringHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\StringUtil',
            [
                'ticketCollectionFactory' => $this->ticketCollectionFactoryMock,
                'config' => $this->configMock,
                'helpdeskEmail' => $this->helpdeskEmailMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->stringHelper, $this->stringHelper);
    }

    /**
     * @covers Mirasvit\Helpdesk\Helper\StringUtil::generateTicketCode
     */
    public function testGenerateTicketCode()
    {
        $this->ticketCollectionMock
            ->method('addFieldToFilter')
            ->willReturnSelf();

        $result = $this->stringHelper->generateTicketCode();
        $this->assertEquals(13, strlen($result));
    }

    /**
     * @dataProvider convertToHtmlProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testConvertToHtml($input, $expected)
    {
        $result = $this->stringHelper->convertToHtml($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function convertToHtmlProvider()
    {
        //@codingStandardsIgnoreStart
        return [
            [
                ' aaaa@bbbb.com ',
                '&nbsp;<a href="mailto:aaaa@bbbb.com">aaaa@bbbb.com</a>&nbsp;',
            ],
            [

                'https://www.evernote.com/shard/s405/sh/bfc9423b-9051-49ef-a3ea-37293055b1be/17c7264f76e1a8a08db7c964641abe52/deep/0/ftp.officerock.com---dev@officerock.com@ftp.officerock.com---FileZilla.png',

                '<a href="https://www.evernote.com/shard/s405/sh/bfc9423b-9051-49ef-a3ea-37293055b1be/17c7264f76e1a8a08db7c964641abe52/deep/0/ftp.officerock.com---dev@officerock.com@ftp.officerock.com---FileZilla.png">https://www.evernote.com/shard/s405/sh/bfc9423b-9051-49ef-a3ea-37293055b1be/17c7264f76e1a8a08db7c964641abe52/deep/0/ftp.officerock.com---dev@officerock.com@ftp.officerock.com---FileZilla.png</a>',
            ],
            [
                '/var/www/vhosts/espace-camera.com/httpdocs/Observer.php',
                '/var/www/vhosts/espace-camera.com/httpdocs/Observer.php',
            ],
            [
                'http://store.com/?aaaa=1&bbbb=2',
                '<a href="http://store.com/?aaaa=1&bbbb=2">http://store.com/?aaaa=1&bbbb=2</a>',
            ],
            [
                ' www.espace-camera.com/httpdocs/',
                '<a href="http://www.espace-camera.com/httpdocs/">www.espace-camera.com/httpdocs/</a>',
            ],
            [
                'http://espace-camera.com/httpdocs/',
                '<a href="http://espace-camera.com/httpdocs/">http://espace-camera.com/httpdocs/</a>',
            ],
            [
                'https://espace-camera.com/httpdocs/',
                '<a href="https://espace-camera.com/httpdocs/">https://espace-camera.com/httpdocs/</a>',
            ],
        ];
        //@codingStandardsIgnoreEnd
    }

    /**
     * @dataProvider subjectProvider
     * @covers Mirasvit\Helpdesk\Helper\StringUtil::getTicketCodeFromSubject
     *
     * @param string $input
     * @param string $expected
     */
    public function testGetTicketCodeFromSubject($input, $expected)
    {
        $result = $this->stringHelper->getTicketCodeFromSubject($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function subjectProvider()
    {
        return [
            ['[#ION-465-43972] Bug', 'ION-465-43972'],
            ['Re: [#ION-465-43972] Bug', 'ION-465-43972'],
            ['Re:Re:[#ION-465-43972]Bug', 'ION-465-43972'],
            ['Re:Re:[ION-465-43972] Bug', false],
            ['Re:Re:#ION-465-43972 Bug', 'ION-465-43972'],
        ];
    }

    /**
     * @dataProvider subjectProviderAW
     *
     * @param string $input
     * @param string $expected
     */
    public function testGetTicketCodeFromSubjectAW($input, $expected)
    {
        $this->configMock->expects($this->any())
            ->method('getGeneralAcceptForeignTickets')
            ->willReturn(Config::ACCEPT_FOREIGN_TICKETS_AW);

        $result = $this->stringHelper->getTicketCodeFromSubject($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function subjectProviderAW()
    {
        return [
            #aw tickets
            ['Re:Re:[AWR-52708] Bug', false],
            ['Re:Re:#AWR-52708 Bug', 'AWR-52708'],
            ['Re: [#CER-84876] New Ticket created: test', 'CER-84876'],
            [
                'Re: [#HNR-12188] Ticket replied: Re: Your points at The Green Nursery are about to expire',
                'HNR-12188'
            ],
        ];
    }

    /**
     * @dataProvider subjectProviderMW
     *
     * @param string $input
     * @param string $expected
     */
    public function testGetTicketCodeFromSubjectMW($input, $expected)
    {
        $this->configMock->expects($this->any())
            ->method('getGeneralAcceptForeignTickets')
            ->willReturn(Config::ACCEPT_FOREIGN_TICKETS_MW);

        $result = $this->stringHelper->getTicketCodeFromSubject($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function subjectProviderMW()
    {
        return [
            #wm tickets
            #Re: Ticket #1000090 - test
            ['Re:Re:Ticket #1000090 - test', 'Ticket #1000090'],
            ['Re: Ticket #1000090 - test', 'Ticket #1000090'],
            ['Re: Ticket #1000090111 sss- test', 'Ticket #1000090111'],
            ['pipe flashing Pittsburg fold 0812',  false],
        ];
    }

    /**
     * @dataProvider body2parseProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testGetTicketCodeFromBody($input, $expected)
    {
        $result = $this->stringHelper->getTicketCodeFromBody($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function body2parseProvider()
    {
        return [
            ['Message-Id:--#AAA-123-45678--', 'AAA-123-45678'],
            ['Message-Id:--#abcedasdfwerwefasdfasdfsadf--', 'abcedasdfwerwefasdfasdfsadf'],
            ['asdfnsjdf askhudfbia sub Ticket Message-Id:--#AAA-123-45678-- 5%32423sfsd', 'AAA-123-45678'],
            ['#AAA-123-45678', false],
            ['dsfa #AAA-123-45678 asdf', false],
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    protected function getFixt($code)
    {
        return file_get_contents(dirname(__FILE__)."/_files/StringTest/fixtures/$code");
    }

    /**
     * @dataProvider bodyProvider
     *
     * @param string $expected
     * @param string $format
     * @param string $input
     */
    public function testParseBodyTest($expected, $format, $input)
    {
        $result = $this->stringHelper->parseBody($this->getFixt($input), $format);
        // echo $result;die;
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function bodyProvider()
    {
        return [
            [
                'So lösen Sie den Geschenkgutschein ein', Config::FORMAT_HTML, 'email3.html',
            ],
            [
                'H1 HEADER

H2 HEADER

H3 HEADER

p block
http://link.com
http://link.com
www.x.com

div block
italic
bold', Config::FORMAT_HTML, 'email2.html',
            ],
            [
                'line 1
line2

line3

line4', Config::FORMAT_HTML, 'email1.html',
            ],
        ];
    }

    /**
     * @dataProvider timeProvider
     *
     * @param string $timeExample
     */
    public function testRemoveTime($timeExample)
    {
        $input = "aaaaaa\nbbbbbb\n$timeExample";
        $expected = "aaaaaa\nbbbbbb";

        $result = $this->stringHelper->removeTime($input);
        // echo $result;
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function timeProvider()
    {
        return [
            ['2014-03-26 19:47 GMT+02:00 Sales <support2@mirasvit.com.ua>:'],
            ['2014-03-25 0:00 GMT+02:00 COPPERLAB Customer Support <support..m>:'],
            ['On Mon, Dec 28, 2015 at 2:54 PM Main Website Store, Sales'."\n".'<helpdeskmx2+sales@gmail.com> wrote:'],
            ['On Mon, Mar 24, 2014 at 10:58 PM, Sales wrote:'],
            ['2014-03-24 19:22 GMT+02:00 Sales :'],
            ['On Dec 8, 2014, at 9:24 AM, Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com> wrote:'],
            ['2014-12-12 9:52 GMT-03:00 Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com>:'],
            ['2014-12-05 11:31 GMT-03:00 Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com>:'],
            ['El 11-12-2014, a las 12:22 p.m., Mirasvit Support <a8v1oq0kggnvsinmg6dv@mirasvit.com> escribió:'],
            ['Em 16/04/2016 8:43 da manhã, "Zococity - Ventas" <a8v1oq0kggnvsinmg6dv@mirasvit.com>:'],
            ["2014-12-05 11:31 GMT-03:00 Mirasvit Support \n<a8v1oq0kggnvsinmg6dv@mirasvit.com>:"],
        ];
    }
}
