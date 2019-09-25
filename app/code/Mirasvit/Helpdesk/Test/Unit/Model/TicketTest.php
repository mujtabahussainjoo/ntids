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
use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @SuppressWarnings(PHPMD)
 * @covers \Mirasvit\Helpdesk\Model\Ticket
 */
class TicketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\DepartmentFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Department|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\PriorityFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priorityFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Priority|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priorityMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\StatusFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusMock;

    /**
     * @var \Magento\User\Model\UserFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userFactoryMock;

    /**
     * @var \Magento\User\Model\User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userMock;

    /**
     * @var \Magento\Store\Model\StoreFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeFactoryMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\MessageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Message|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageMock;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\EmailFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Email|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailMock;

    /**
     * @var \Magento\Sales\Model\OrderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderFactoryMock;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Message\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Tag\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tagCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Tag\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tagCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Notification|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskNotificationMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\History|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskHistoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskStringMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Ruleevent|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskRuleeventMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Email|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskEmailMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskDataMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Storeview|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskStoreviewMock;

    /**
     * @var \Magento\Framework\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlManagerMock;

    /**
     * @var \Magento\Backend\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendUrlManagerMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

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
        $this->departmentFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\DepartmentFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->departmentMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Department',
            ['load', 'save', 'delete'],
            [],
            '',
            false
        );
        $this->departmentFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->departmentMock));
        $this->priorityFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\PriorityFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->priorityMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Priority',
            ['load', 'save', 'delete'],
            [],
            '',
            false
        );
        $this->priorityFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->priorityMock));
        $this->statusFactoryMock = $this->getMock('\Mirasvit\Helpdesk\Model\StatusFactory', ['create'], [], '', false);
        $this->statusMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Status',
            ['load', 'save', 'delete', 'loadByCode'],
            [],
            '',
            false
        );
        $this->statusFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->statusMock));
        $this->userFactoryMock = $this->getMock('\Magento\User\Model\UserFactory', ['create'], [], '', false);
        $this->userMock = $this->getMock('\Magento\User\Model\User', ['load', 'save', 'delete'], [], '', false);
        $this->userFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->userMock));
        $this->storeFactoryMock = $this->getMock('\Magento\Store\Model\StoreFactory', ['create'], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', ['load', 'save', 'delete'], [], '', false);
        $this->storeFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->storeMock));
        $this->messageFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\MessageFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->messageMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Message',
            ['load', 'save', 'delete'],
            [],
            '',
            false
        );
        $this->messageFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->messageMock));
        $this->customerFactoryMock = $this->getMock(
            '\Magento\Customer\Model\CustomerFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->customerMock = $this->getMock(
            '\Magento\Customer\Model\Customer',
            ['load', 'save', 'delete', 'getName', 'getShippingAddress'],
            [],
            '',
            false
        );
        $this->customerFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->customerMock));
        $this->emailFactoryMock = $this->getMock('\Mirasvit\Helpdesk\Model\EmailFactory', ['create'], [], '', false);
        $this->emailMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Email',
            ['load', 'save', 'delete'],
            [],
            '',
            false
        );
        $this->emailFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->emailMock));
        $this->orderFactoryMock = $this->getMock('\Magento\Sales\Model\OrderFactory', ['create'], [], '', false);
        $this->orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['load', 'save', 'delete', 'getShippingAddress'],
            [],
            '',
            false
        );
        $this->orderFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->orderMock));
        $this->departmentCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->departmentCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->departmentCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->departmentCollectionMock));
        $this->messageCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->messageCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Message\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->messageCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->messageCollectionMock));
        $this->tagCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Tag\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->tagCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Tag\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->tagCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->tagCollectionMock));
        $this->configMock = $this->getMock('\Mirasvit\Helpdesk\Model\Config', [], [], '', false);
        $this->helpdeskNotificationMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Notification', [], [], '', false);
        $this->helpdeskHistoryMock = $this->getMock('\Mirasvit\Helpdesk\Helper\History', [], [], '', false);
        $this->helpdeskStringMock = $this->getMock('\Mirasvit\Helpdesk\Helper\StringUtil', [], [], '', false);
        $this->helpdeskRuleeventMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Ruleevent', [], [], '', false);
        $this->helpdeskEmailMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Email', [], [], '', false);
        $this->helpdeskDataMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Attachment', [], [], '', false);
        $this->helpdeskStoreviewMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Storeview', ['fixUrl'], [], '', false);
        $this->urlManagerMock = $this->getMock('\Magento\Framework\Url', [], [], '', false);
        $this->backendUrlManagerMock = $this->getMock('\Magento\Backend\Model\Url', [], [], '', false);
        $this->localeDateMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
            [],
            '',
            false,
            true,
            true,
            ['date', 'format', 'formatDateTime', 'formatDate']
        );
        $this->storeManager = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface',
            [],
            '',
            false,
            true,
            true,
            ['getStore']
        );
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock('\Mirasvit\Helpdesk\Model\ResourceModel\Ticket', [], [], '', false);
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection',
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
        $this->ticketModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Ticket',
            [
                'departmentFactory' => $this->departmentFactoryMock,
                'priorityFactory' => $this->priorityFactoryMock,
                'statusFactory' => $this->statusFactoryMock,
                'userFactory' => $this->userFactoryMock,
                'storeFactory' => $this->storeFactoryMock,
                'messageFactory' => $this->messageFactoryMock,
                'customerFactory' => $this->customerFactoryMock,
                'emailFactory' => $this->emailFactoryMock,
                'orderFactory' => $this->orderFactoryMock,
                'departmentCollectionFactory' => $this->departmentCollectionFactoryMock,
                'messageCollectionFactory' => $this->messageCollectionFactoryMock,
                'tagCollectionFactory' => $this->tagCollectionFactoryMock,
                'config' => $this->configMock,
                'helpdeskNotification' => $this->helpdeskNotificationMock,
                'helpdeskHistory' => $this->helpdeskHistoryMock,
                'helpdeskString' => $this->helpdeskStringMock,
                'helpdeskRuleevent' => $this->helpdeskRuleeventMock,
                'helpdeskEmail' => $this->helpdeskEmailMock,
                'helpdeskAttachment' => $this->helpdeskDataMock,
                'storeviewHelper' => $this->helpdeskStoreviewMock,
                'urlManager' => $this->urlManagerMock,
                'backendUrlManager' => $this->backendUrlManagerMock,
                'localeDate' => $this->localeDateMock,
                'storeManager' => $this->storeManager,
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
        $this->assertEquals($this->ticketModel, $this->ticketModel);
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::initOwner
     */
    public function testInitOwner()
    {
        $this->ticketModel->initOwner('1_2');
        $this->assertEquals(2, $this->ticketModel->getUserId());
        $this->assertEquals(1, $this->ticketModel->getDepartmentId());
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::markAsSpam
     */
    public function testMarkAsSpam()
    {
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->ticketModel->markAsSpam();
        $this->assertEquals($this->ticketModel->getFolder(), Config::FOLDER_SPAM);
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::getCustomer
     */
    public function testGetCustomerWithoutCustomer()
    {
        $this->assertEquals($this->ticketModel->getCustomer(), false);
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::getCustomer
     */
    public function testGetCustomerAsGuest()
    {
        $this->ticketModel
            ->setCustomerEmail('john@example.com')
            ->setCustomerName('John Doe');
        $expected = new \Magento\Framework\DataObject(['name' => 'John Doe', 'email' => 'john@example.com']);

        $this->assertEquals($expected, $this->ticketModel->getCustomer());
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::getCustomer
     */
    public function testGetCustomerAsRegistered()
    {
        $this->customerFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->customerMock);

        $this->customerMock->expects($this->any())
            ->method('load')
            ->with(1)
            ->willReturnSelf();

        $this->ticketModel->setCustomerId(1);

        $this->assertEquals($this->customerMock, $this->ticketModel->getCustomer());
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::markAsNotSpam
     */
    public function testMarkAsNotSpam()
    {
        //        $emailMock = $this->getMock('Mirasvit\Helpdesk\Model\Email', ['load', 'save'], [], '', false);
        $this->emailMock->expects($this->once())
            ->method('load')
            ->with(1)
            ->willReturnSelf();
        //
        //        $emailMock->expects($this->once())
        //            ->method('setPatternId')
        //            ->with(0)
        //            ->willReturnSelf();

        $this->emailMock->expects($this->once())
            ->method('save');

        //initial data
        $this->ticketModel
            ->setFolder(Config::FOLDER_SPAM)
            ->setEmailId(1)
        ;

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->ticketModel->markAsNotSpam();
        $this->assertEquals($this->ticketModel->getFolder(), Config::FOLDER_INBOX);
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::open
     */
    public function testOpen()
    {
        $statusId = 1;
        $this->statusMock->setId($statusId);
        $this->statusMock->expects($this->once())
            ->method('loadByCode')
            ->with(Config::STATUS_OPEN)
            ->willReturnSelf();

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        $this->ticketModel->open();
        $this->assertEquals($this->ticketModel->getStatusId(), $statusId);
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::close
     */
    public function testClose()
    {
        $statusId = 1;
        $this->statusMock->setId($statusId);
        $this->statusMock->expects($this->once())
            ->method('loadByCode')
            ->with(Config::STATUS_CLOSED)
            ->willReturnSelf();

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        $this->ticketModel->close();
        $this->assertEquals($this->ticketModel->getStatusId(), $statusId);
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::isClosed
     */
    public function testIsClosed()
    {
        $statusId = 1;

        $this->statusMock->setId($statusId);

        $this->statusMock
            ->method('loadByCode')
            ->with(Config::STATUS_CLOSED)
            ->willReturnSelf();

        $this->ticketModel->setStatusId($statusId);
        $this->assertEquals($this->ticketModel->isClosed(), true);
        $this->ticketModel->setStatusId(2);
        $this->assertEquals($this->ticketModel->isClosed(), false);
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::getTags
     */
    public function testGetTags()
    {
        $this->ticketModel->setTagIds([1, 2]);

        $this->tagCollectionFactoryMock->expects($this->any())->method('create')->will(
            $this->returnValue($this->tagCollectionMock)
        );
        $this->tagCollectionMock
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with('tag_id', [0, 1, 2])
            ->will($this->returnSelf());

        $this->assertEquals($this->tagCollectionMock, $this->ticketModel->getTags());
    }

    /**
     * @param int    $orderCustomerId
     * @param string $orderCustomerEmail
     * @covers \Mirasvit\Helpdesk\Model\Ticket::initFromOrder
     * @dataProvider initFromOrderDataProvider
     */
    public function testInitFromOrder($orderCustomerId, $orderCustomerEmail)
    {
        $orderId = 1;
        $storeId = 2;
        $customerId = 3;
        $customerEmail = 'jack@x.com';
        $shippingAddressEmail = 'jim@x.com';
        $shippingAddressId = 5;

        $this->customerMock->setEmail($customerEmail);

        $this->customerFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->customerMock);

        $this->customerMock->expects($this->any())
            ->method('load')
            ->with($customerId)
            ->willReturnSelf();

        $this->orderFactoryMock->expects($this->any())->method('create')->will(
            $this->returnValue($this->orderMock)
        );
        $this->orderMock
            ->setStoreId($storeId);

        $this->orderMock
            ->expects($this->any())
            ->method('load')
            ->with($orderId)
            ->willReturnSelf();

        $shippingAddressMock = $this->getMockBuilder('\Magento\Sales\Model\Order\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $shippingAddressMock
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($shippingAddressId));

        $shippingAddressMock
            ->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue($shippingAddressEmail));

        $this->orderMock
            ->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($shippingAddressMock));

        $this->orderMock
            ->setCustomerId($orderCustomerId)
            ->setCustomerEmail($orderCustomerEmail);

        $this->ticketModel->initFromOrder($orderId);

        $this->assertEquals($orderId, $this->ticketModel->getOrderId());
        $this->assertEquals($storeId, $this->ticketModel->getStoreId());
        $this->assertEquals($orderCustomerId, $this->ticketModel->getCustomerId());
        if ($orderCustomerId) {
            $this->assertEquals($customerEmail, $this->ticketModel->getCustomerEmail());
        } elseif ($orderCustomerEmail) {
            $this->assertEquals($orderCustomerEmail, $this->ticketModel->getCustomerEmail());
        } else {
            $this->assertEquals($shippingAddressEmail, $this->ticketModel->getCustomerEmail());
        }
        $this->assertEquals($shippingAddressId, $this->ticketModel->getQuoteAddressId());
    }

    /**
     * @return array
     */
    public function initFromOrderDataProvider()
    {
        return [
            'order with customer' => [
                'orderCustomerId' => 3,
                'orderCustomerEmail' => 'tao@x.com',
            ],
            'order with guest customer' => [
                'orderCustomerId' => null,
                'orderCustomerEmail' => 'max@x.com',
            ],
            'order without any email' => [
                'orderCustomerId' => null,
                'orderCustomerEmail' => null,
            ],
        ];
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::getUrl
     */
    public function testGetUrl()
    {
        $this->markTestSkipped();
        $url = 'x.com/ticket/...';
        $ticketId = 1;
        $this->ticketModel->setId($ticketId);

        $this->urlManagerMock
            ->expects($this->once())
            ->method('getUrl')->with('helpdesk/ticket/view', ['id' => $ticketId, '_nosid' => true])
            ->will($this->returnValue($url));

        $this->assertEquals($url, $this->ticketModel->getUrl());
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::getMessages
     */
    public function testGetMessagesWithoutPrivate()
    {
        $ticketId = 1;
        $this->ticketModel->setId($ticketId);

        $this->messageCollectionFactoryMock->expects($this->any())->method('create')->will(
            $this->returnValue($this->messageCollectionMock)
        );

        $this->messageCollectionMock
            ->expects($this->exactly(2))
            ->method('addFieldToFilter')
            ->withConsecutive(
                ['ticket_id', $ticketId],
                ['type', [
                    ['eq' => ''],
                    ['eq' => Config::MESSAGE_PUBLIC],
                    ['eq' => Config::MESSAGE_PUBLIC_THIRD],
                ]]
            )
            ->willReturnSelf();

        $this->messageCollectionMock
            ->expects($this->once())
            ->method('setOrder')
            ->with('created_at', 'desc')
            ->will($this->returnSelf());
        $this->assertEquals($this->messageCollectionMock, $this->ticketModel->getMessages());
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::getMessages
     */
    public function testGetMessagesWithPrivate()
    {
        $ticketId = 1;
        $this->ticketModel->setId($ticketId);

        $this->messageCollectionFactoryMock->expects($this->any())->method('create')->will(
            $this->returnValue($this->messageCollectionMock)
        );

        $this->messageCollectionMock
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->withConsecutive(
                ['ticket_id', $ticketId]
            )
            ->willReturnSelf();

        $this->messageCollectionMock
            ->expects($this->once())
            ->method('setOrder')
            ->with('created_at', 'desc')
            ->will($this->returnSelf());
        $this->assertEquals($this->messageCollectionMock, $this->ticketModel->getMessages(true));
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::getLastMessage
     */
    public function testGetLastMessage()
    {
        $ticketId = 1;
        $this->ticketModel->setId($ticketId);

        $this->messageCollectionMock
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->withConsecutive(
                ['ticket_id', $ticketId]
            )
            ->willReturnSelf();

        $this->messageCollectionMock
            ->expects($this->once())
            ->method('setOrder')
            ->with('message_id', 'asc')
            ->will($this->returnSelf());

        $messageMock = $this->getMockBuilder('\Mirasvit\Helpdesk\Model\Message')
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageCollectionMock
            ->expects($this->once())
            ->method('getLastItem')
            ->will($this->returnValue($messageMock));

        $this->assertEquals($messageMock, $this->ticketModel->getLastMessage());
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::getCreatedAtFormated
     */
    public function testGetCreatedAtFormated()
    {
        $createdAt = '2014-10-13 08:37:20';
        $this->ticketModel->setCreatedAt($createdAt);

        $this->localeDateMock->expects($this->any())
            ->method('formatDateTime')
            ->with(new \DateTime($createdAt), \IntlDateFormatter::SHORT)
            ->willReturn('08:37');

        $this->localeDateMock->expects($this->any())
            ->method('formatDate')
            ->with(new \DateTime($createdAt), \IntlDateFormatter::SHORT)
            ->willReturn('2014-10-13');

        $this->assertEquals('2014-10-13 08:37', $this->ticketModel->getCreatedAtFormated(\IntlDateFormatter::SHORT));
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Ticket::addMessage
     */
    public function testAddMessage()
    {
        $text = 'aaaa';
        $statusId = 1;
        $ticketId = 2;
        $customerId = 3;
        $customerName = 'John Doe';

        $this->ticketModel->setId($ticketId);
        $this->customerMock->setId($customerId);
        $this->statusMock->setId($statusId);
        $this->statusMock
            ->method('loadByCode')
            ->with(Config::STATUS_CLOSED)
            ->willReturnSelf();

        $this->customerMock->expects($this->any())
            ->method('getName')
            ->willReturn($customerName);

        $this->messageMock
            ->expects($this->once())
            ->method('save');

        $this->ticketModel->addMessage($text, $this->customerMock, false, Config::CUSTOMER);

        $this->assertEquals($ticketId, $this->messageMock->getTicketId());
        $this->assertEquals($text, $this->messageMock->getBody());
        $this->assertEquals(Config::MESSAGE_PUBLIC, $this->messageMock->getType());
        $this->assertEquals(Config::MESSAGE_PUBLIC, $this->messageMock->getType());
        $this->assertEquals($customerId, $this->messageMock->getCustomerId());
        $this->assertEquals($customerName, $this->ticketModel->getLastReplyName());
    }
}
