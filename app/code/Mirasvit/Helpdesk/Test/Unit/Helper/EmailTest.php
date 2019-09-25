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
 * @covers \Mirasvit\Helpdesk\Helper\Email
 * @SuppressWarnings(PHPMD)
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Email|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskStringMock;

    /**
     * @var \Mirasvit\Rma\Helper\Process|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaProcessMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Process|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskProcessMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->configMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->helpdeskStringMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\StringUtil',
            [],
            [],
            '',
            false
        );
        $this->rmaProcessMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Process',
            [],
            [],
            '',
            false
        );
        $this->helpdeskProcessMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Process',
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
        $this->emailHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Email',
            [
                'config' => $this->configMock,
                'helpdeskString' => $this->helpdeskStringMock,
                'rmaProcess' => $this->rmaProcessMock,
                'helpdeskProcess' => $this->helpdeskProcessMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->emailHelper, $this->emailHelper);
    }

    /**
     * TestGetEmailSubject
     */
    public function testGetEmailSubject()
    {
        $this->configMock->expects($this->at(0))
            ->method('getNotificationIsShowCode')
            ->willReturn(true);

        $ticket = $this->objectManager->getObject(
            'Mirasvit\Helpdesk\Model\Ticket',
            [
                'data' => [
                    'code' => 'abcdef',
                    'subject' => 'Test Ticket',
                ],
            ]
        );
        $this->assertEquals('[#abcdef] Test Ticket', $this->emailHelper->getEmailSubject($ticket));
    }
}
