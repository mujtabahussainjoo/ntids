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



namespace Mirasvit\Helpdesk\Test\Unit\Controller\Ticket;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Helpdesk\Controller\Ticket\Postexternal
 * @SuppressWarnings(PHPMD)
 */
class PostexternalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Controller\Ticket\Postexternal|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketController;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\History|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskHistoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Process|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskProcessMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Framework\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\View\Result\PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

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
        $this->attachmentCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Attachment\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->attachmentCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Attachment\Collection',
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
        $this->attachmentCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->attachmentCollectionMock));
        $this->helpdeskHistoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\History',
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
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->customerSessionMock = $this->getMock(
            '\Magento\Customer\Model\Session',
            [],
            [],
            '',
            false
        );
        $this->requestMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\RequestInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->resultFactoryMock = $this->getMock(
            'Magento\Framework\Controller\ResultFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resultPageMock = $this->getMock('Magento\Backend\Model\View\Result\Page', [], [], '', false);
        $this->resultFactoryMock->expects($this->any())
           ->method('create')
           ->willReturn($this->resultPageMock);

        $this->redirectMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\Response\RedirectInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->messageManagerMock = $this->getMockForAbstractClass(
            'Magento\Framework\Message\ManagerInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->getMock('\Magento\Backend\App\Action\Context', [], [], '', false);
        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getObjectManager')->willReturn($this->objectManager);
        $this->contextMock->expects($this->any())->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->any())->method('getRedirect')->willReturn($this->redirectMock);
        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->ticketController = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Controller\Ticket\Postexternal',
            [
                'ticketFactory' => $this->ticketFactoryMock,
                'ticketCollectionFactory' => $this->ticketCollectionFactoryMock,
                'attachmentCollectionFactory' => $this->attachmentCollectionFactoryMock,
                'helpdeskHistory' => $this->helpdeskHistoryMock,
                'helpdeskProcess' => $this->helpdeskProcessMock,
                'registry' => $this->registryMock,
                'customerSession' => $this->customerSessionMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->ticketController, $this->ticketController);
    }
}
