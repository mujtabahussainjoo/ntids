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

/**
 * @covers \Mirasvit\Helpdesk\Helper\History
 * @SuppressWarnings(PHPMD)
 */
class HistoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\History|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\HistoryFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\History|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\StatusFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\PriorityFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priorityFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Priority|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priorityMock;

    /**
     * @var \Magento\User\Model\UserFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userFactoryMock;

    /**
     * @var \Magento\User\Model\User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\DepartmentFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Department|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->historyFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\HistoryFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->historyMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\History',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->historyFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->historyMock));
        $this->statusFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\StatusFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->statusMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Status',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->statusFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->statusMock));
        $this->priorityFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\PriorityFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->priorityMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Priority',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->priorityFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->priorityMock));
        $this->userFactoryMock = $this->getMock(
            '\Magento\User\Model\UserFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->userMock = $this->getMock(
            '\Magento\User\Model\User',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->userFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->userMock));
        $this->departmentFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\DepartmentFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->departmentMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Department',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->departmentFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->departmentMock));
        $this->ticketFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\TicketFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ticketMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Ticket',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->ticketFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ticketMock));
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->historyHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\History',
            [
                'historyFactory' => $this->historyFactoryMock,
                'statusFactory' => $this->statusFactoryMock,
                'priorityFactory' => $this->priorityFactoryMock,
                'userFactory' => $this->userFactoryMock,
                'departmentFactory' => $this->departmentFactoryMock,
                'ticketFactory' => $this->ticketFactoryMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->historyHelper, $this->historyHelper);
    }
}
