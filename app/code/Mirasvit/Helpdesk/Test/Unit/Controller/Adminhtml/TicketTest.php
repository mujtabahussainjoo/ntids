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



namespace Mirasvit\Helpdesk\Test\Unit\Controller\Adminhtml;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Helpdesk\Controller\Adminhtml\Ticket
 * @SuppressWarnings(PHPMD)
 */
class TicketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Controller\Adminhtml\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketController;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\StatusFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\AttachmentFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Attachment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\MessageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Message|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Process|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskProcessMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\History|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskHistoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Permission|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskPermissionMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskDataMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\Json\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $jsonEncoderMock;

    /**
     * @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendSessionMock;

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
        $this->customerFactoryMock = $this->getMock(
            '\Magento\Customer\Model\CustomerFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->customerMock = $this->getMock(
            '\Magento\Customer\Model\Customer',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->customerFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->customerMock));
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
        $this->attachmentFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\AttachmentFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->attachmentMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Attachment',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->attachmentFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->attachmentMock));
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
        $this->helpdeskProcessMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Process',
            [],
            [],
            '',
            false
        );
        $this->helpdeskHistoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\History',
            [],
            [],
            '',
            false
        );
        $this->helpdeskPermissionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Permission',
            [],
            [],
            '',
            false
        );
        $this->helpdeskDataMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Data',
            [],
            [],
            '',
            false
        );
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->jsonEncoderMock = $this->getMock(
            '\Magento\Framework\Json\Helper\Data',
            [],
            [],
            '',
            false
        );
        $this->escaperMock = $this->getMock(
            '\Magento\Framework\Escaper',
            [],
            [],
            '',
            false
        );
        $this->backendSessionMock = $this->getMock(
            '\Magento\Backend\Model\Session',
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
        $this->ticketController = $this->getMockForAbstractClass(
            '\Mirasvit\Helpdesk\Controller\Adminhtml\Ticket',
            [
                'customerFactory' => $this->customerFactoryMock,
                'ticketFactory' => $this->ticketFactoryMock,
                'statusFactory' => $this->statusFactoryMock,
                'attachmentFactory' => $this->attachmentFactoryMock,
                'messageFactory' => $this->messageFactoryMock,
                'helpdeskProcess' => $this->helpdeskProcessMock,
                'helpdeskHistory' => $this->helpdeskHistoryMock,
                'helpdeskPermission' => $this->helpdeskPermissionMock,
                'helpdeskData' => $this->helpdeskDataMock,
                'storeManager' => $this->storeManagerMock,
                'registry' => $this->registryMock,
                'jsonEncoder' => $this->jsonEncoderMock,
                'escaper' => $this->escaperMock,
                'context' => $this->contextMock,
            ],
            '',
            false,
            true,
            true,
            []
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
