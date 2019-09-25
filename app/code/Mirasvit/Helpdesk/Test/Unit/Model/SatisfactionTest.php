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



namespace Mirasvit\Helpdesk\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Helpdesk\Model\Satisfaction
 * @SuppressWarnings(PHPMD)
 */
class SatisfactionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Satisfaction|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $satisfactionModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\MessageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Message|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceCollectionMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->messageFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\MessageFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->messageMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Message',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->messageFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->messageMock));
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
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\Collection',
            [],
            [],
            '',
            false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->satisfactionModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Satisfaction',
            [
                'messageFactory' => $this->messageFactoryMock,
                'ticketFactory' => $this->ticketFactoryMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->satisfactionModel, $this->satisfactionModel);
    }
}
