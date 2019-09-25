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
 * @covers \Mirasvit\Helpdesk\Helper\Satisfaction
 * @SuppressWarnings(PHPMD)
 */
class SatisfactionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Satisfaction|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $satisfactionHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\SatisfactionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $satisfactionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Satisfaction|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $satisfactionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Message\Collection|
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\CollectionFactory|
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $satisfactionCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $satisfactionCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Mail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskMailMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
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
        $this->satisfactionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\SatisfactionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->satisfactionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Satisfaction',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->satisfactionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->satisfactionMock));
        $this->messageCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->messageCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Message\Collection',
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
        $this->messageCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->messageCollectionMock));
        $this->satisfactionCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->satisfactionCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\Collection',
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
        $this->satisfactionCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->satisfactionCollectionMock));
        $this->helpdeskMailMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Mail',
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
        $this->satisfactionHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Satisfaction',
            [
                'ticketFactory' => $this->ticketFactoryMock,
                'satisfactionFactory' => $this->satisfactionFactoryMock,
                'messageCollectionFactory' => $this->messageCollectionFactoryMock,
                'satisfactionCollectionFactory' => $this->satisfactionCollectionFactoryMock,
                'helpdeskMail' => $this->helpdeskMailMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->satisfactionHelper, $this->satisfactionHelper);
    }
}
