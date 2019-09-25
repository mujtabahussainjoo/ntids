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
use Mirasvit\Helpdesk\Test\Unit\Mocks;

/**
 * @covers \Mirasvit\Helpdesk\Helper\Process
 * @SuppressWarnings(PHPMD)
 */
class ProcessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    ///**
    // * @var \Mirasvit\Helpdesk\Helper\Process|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $processHelper;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $ticketFactoryMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $ticketMock;
    //
    ///**
    // * @var \Magento\Sales\Model\Order\AddressFactory|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $orderAddressFactoryMock;
    //
    ///**
    // * @var \Magento\Sales\Model\Order\Address|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $orderAddressMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\GatewayFactory|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $gatewayFactoryMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\Gateway|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $gatewayMock;
    //
    ///**
    // * @var \Magento\Sales\Model\OrderFactory|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $orderFactoryMock;
    //
    ///**
    // * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $orderMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $ticketCollectionFactoryMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $ticketCollectionMock;
    //
    ///**
    // * @var \Magento\User\Model\ResourceModel\User\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $userCollectionFactoryMock;
    //
    ///**
    // * @var \Magento\User\Model\ResourceModel\User\Collection|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $userCollectionMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory|
    // * \PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $departmentCollectionFactoryMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $departmentCollectionMock;
    //
    ///**
    // * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $orderCollectionFactoryMock;
    //
    ///**
    // * @var \Magento\Sales\Model\ResourceModel\Order\Collection|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $orderCollectionMock;
    //
    ///**
    // * @var \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory|
    // * \PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $orderAddressCollectionFactoryMock;
    //
    ///**
    // * @var \Magento\Sales\Model\ResourceModel\Order\Address\Collection|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $orderAddressCollectionMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\ResourceModel\Pattern\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $patternCollectionFactoryMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\ResourceModel\Pattern\Collection|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $patternCollectionMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $messageCollectionFactoryMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\ResourceModel\Message\Collection|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $messageCollectionMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $configMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Helper\Customer|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $helpdeskCustomerMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Helper\StringUtil|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $helpdeskStringMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Helper\Field|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $helpdeskFieldMock;
    //
    /**
     * @var \Mirasvit\Helpdesk\Helper\History|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskHistoryMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Helper\Tag|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $helpdeskTagMock;
    //
    ///**
    // * @var \Mirasvit\Core\Helper\Date|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $mstcoreDateMock;
    //
    /**
     * @var \Mirasvit\Helpdesk\Helper\Draft|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskDraftMock;
    //
    ///**
    // * @var \Mirasvit\Helpdesk\Helper\Encoding|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $helpdeskEncodingMock;
    //
    ///**
    // * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $storeManagerMock;
    //
    ///**
    // * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $localeDateMock;
    //
    ///**
    // * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $customerSessionMock;
    //
    ///**
    // * @var \Magento\Backend\Model\Auth|\PHPUnit_Framework_MockObject_MockObject
    // */
    //protected $authMock;
    //
    ///**
    // * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
    // */

    /**
     * protected $contextMock;
     */
    public function setUp()
    {
        //$this->ticketFactoryMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\TicketFactory',
        //   ['create'],
        //   [],
        //   '',
        //   false
        //);
        //$this->ticketMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\Ticket',
        //   ['load', 'save', 'delete'],
        //   [],
        //   '',
        //   false
        //);
        //$this->ticketFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->ticketMock));
        //$this->orderAddressFactoryMock = $this->getMock(
        //  '\Magento\Sales\Model\Order\AddressFactory',
        //   ['create'],
        //   [],
        //   '',
        //   false
        //);
        //$this->orderAddressMock = $this->getMock(
        //   '\Magento\Sales\Model\Order\Address',
        //   ['load', 'save', 'delete'],
        //   [],
        //   '',
        //   false
        //);
        //$this->orderAddressFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->orderAddressMock));
        //$this->gatewayFactoryMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\GatewayFactory',
        //   ['create'],
        //   [],
        //   '',
        //   false
        //);
        //$this->gatewayMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\Gateway',
        //   ['load', 'save', 'delete'],
        //   [],
        //   '',
        //   false
        //);
        //$this->gatewayFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->gatewayMock));
        //$this->orderFactoryMock = $this->getMock('\Magento\Sales\Model\OrderFactory', ['create'], [], '', false);
        //$this->orderMock = $this->getMock('\Magento\Sales\Model\Order', ['load', 'save', 'delete'], [], '', false);
        //$this->orderFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->orderMock));
        //$this->ticketCollectionFactoryMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory',
        //   ['create'],
        //   [],
        //   '',
        //   false
        //);
        //$this->ticketCollectionMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection',
        //   ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
        //   [],
        //   '',
        //   false
        //);
        //$this->ticketCollectionFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->ticketCollectionMock));
        //$this->userCollectionFactoryMock = $this->getMock(
        //  '\Magento\User\Model\ResourceModel\User\CollectionFactory',
        //   ['create'],
        //   [],
        //   '',
        //   false
        //);
        //$this->userCollectionMock = $this->getMock(
        //  '\Magento\User\Model\ResourceModel\User\Collection',
        //   ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
        //   [],
        //   '',
        //   false
        //);
        //$this->userCollectionFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->userCollectionMock));
        //$this->departmentCollectionFactoryMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory',
        //   ['create'],
        //   [],
        //   '',
        //   false
        //);
        //$this->departmentCollectionMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection'
        //  , ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
        //   [],
        //   '',
        //   false
        //);
        //$this->departmentCollectionFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->departmentCollectionMock));
        //$this->orderCollectionFactoryMock = $this->getMock(
        //  '\Magento\Sales\Model\ResourceModel\Order\CollectionFactory',
        //   ['create'],
        //   [],
        //   '',
        //   false
        //);
        //$this->orderCollectionMock = $this->getMock(
        //  '\Magento\Sales\Model\ResourceModel\Order\Collection',
        //   ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
        //   [],
        //   '',
        //   false
        //);
        //$this->orderCollectionFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->orderCollectionMock));
        //$this->orderAddressCollectionFactoryMock = $this->getMock(
        //  '\Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory',
        //   ['create'],
        //   [],
        //   '',
        //   false
        //);
        //$this->orderAddressCollectionMock = $this->getMock(
        //  '\Magento\Sales\Model\ResourceModel\Order\Address\Collection',
        //   ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
        //   [],
        //   '',
        //   false
        //);
        //$this->orderAddressCollectionFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->orderAddressCollectionMock));
        //$this->patternCollectionFactoryMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\ResourceModel\Pattern\CollectionFactory',
        //   ['create'],
        //   [],
        //   '',
        //   false
        //);
        //$this->patternCollectionMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\ResourceModel\Pattern\Collection',
        //   ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
        //   [],
        //   '',
        //   false
        //);
        //$this->patternCollectionFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->patternCollectionMock));
        //$this->messageCollectionFactoryMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory',
        //   ['create'],
        //   [],
        //   '',
        //   false
        //);
        //$this->messageCollectionMock = $this->getMock(
        //  '\Mirasvit\Helpdesk\Model\ResourceModel\Message\Collection',
        //   ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
        //   [],
        //   '',
        //   false
        //);
        //$this->messageCollectionFactoryMock->expects($this->any())->method('create')
        //    ->will($this->returnValue($this->messageCollectionMock));
        //$this->configMock = $this->getMock('\Mirasvit\Helpdesk\Model\Config', [], [], '', false);
        //$this->helpdeskCustomerMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Customer', [], [], '', false);
        //$this->helpdeskStringMock = $this->getMock('\Mirasvit\Helpdesk\Helper\StringUtil', [], [], '', false);
        //$this->helpdeskFieldMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Field', [], [], '', false);
        $this->helpdeskHistoryMock = $this->getMock('\Mirasvit\Helpdesk\Helper\History', [], [], '', false);
        //$this->helpdeskTagMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Tag', [], [], '', false);
        //$this->mstcoreDateMock = $this->getMock('\Mirasvit\Core\Helper\Date', [], [], '', false);
        $this->helpdeskDraftMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Draft', [], [], '', false);
        //$this->helpdeskEncodingMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Encoding', [], [], '', false);
        //$this->storeManagerMock = $this->getMockForAbstractClass(
        //  '\Magento\Store\Model\StoreManagerInterface',
        //   [],
        //   '',
        //   false,
        //   true,
        //   true,
        //   []
        //);
        //$this->localeDateMock = $this->getMockForAbstractClass(
        //  '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
        //   [],
        //   '',
        //   false,
        //   true,
        //   true,
        //   []
        //);
        //$this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        //$this->authMock = $this->getMock('\Magento\Backend\Model\Auth', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        //$this->contextMock = $this->objectManager->getObject(
        //'\Magento\Framework\App\Helper\Context',
        //[
        //]
        //);
        //$this->processHelper = $this->objectManager->getObject(
        //'\Mirasvit\Helpdesk\Helper\Process',
        //[
        //    'ticketFactory' => $this->ticketFactoryMock,
        //    'orderAddressFactory' => $this->orderAddressFactoryMock,
        //    'gatewayFactory' => $this->gatewayFactoryMock,
        //    'orderFactory' => $this->orderFactoryMock,
        //    'ticketCollectionFactory' => $this->ticketCollectionFactoryMock,
        //    'userCollectionFactory' => $this->userCollectionFactoryMock,
        //    'departmentCollectionFactory' => $this->departmentCollectionFactoryMock,
        //    'orderCollectionFactory' => $this->orderCollectionFactoryMock,
        //    'orderAddressCollectionFactory' => $this->orderAddressCollectionFactoryMock,
        //    'patternCollectionFactory' => $this->patternCollectionFactoryMock,
        //    'messageCollectionFactory' => $this->messageCollectionFactoryMock,
        //    'config' => $this->configMock,
        //    'helpdeskCustomer' => $this->helpdeskCustomerMock,
        //    'helpdeskString' => $this->helpdeskStringMock,
        //    'helpdeskField' => $this->helpdeskFieldMock,
        //    'helpdeskHistory' => $this->helpdeskHistoryMock,
        //    'helpdeskTag' => $this->helpdeskTagMock,
        //    'mstcoreDate' => $this->mstcoreDateMock,
        //    'helpdeskDraft' => $this->helpdeskDraftMock,
        //    'helpdeskEncoding' => $this->helpdeskEncodingMock,
        //    'storeManager' => $this->storeManagerMock,
        //    'localeDate' => $this->localeDateMock,
        //    'customerSession' => $this->customerSessionMock,
        //    'auth' => $this->authMock,
        //    'context' => $this->contextMock,
        //]
        //);
    }

    /**
     * @covers Mirasvit\Helpdesk\Helper\Process::createOrUpdateFromBackendPost
     */
    public function testCreateOrUpdateFromBackendPostNewTicket()
    {
        $data = [
            'customer_email' => 'john@example.com',
            'tags' => [],
            'fp_period_unit' => '',
            'fp_period_value' => '',
            'reply' => 'some message',
            'reply_type' => 'public',
            'name' => 'Ticket New',
            'status_id' => 3,
            'priority_id' => 4,
            'owner' => '7_8',
        ];
        $user = Mocks\Magento\UserMock::create($this);

        $ticket = Mocks\TicketMock::create($this, [], ['load', 'save', 'delete', 'addMessage']);
        $ticket->expects($this->once())
            ->method('addMessage')
            ->with($data['reply'], false, $user, Config::USER, $data['reply_type'], false, Config::FORMAT_PLAIN)
            ->willReturnSelf();

        $ticket->expects($this->never())
            ->method('load');

        $ticket->expects($this->once())
            ->method('save');

        /* @var \Mirasvit\Helpdesk\Helper\Process|\PHPUnit_Framework_MockObject_MockObject $dataHelper */
        $helper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Process',
            [
                'ticketFactory' => Mocks\TicketFactoryMock::create($this, $ticket),
                'helpdeskHistory' => $this->helpdeskHistoryMock,
                'helpdeskDraft' => $this->helpdeskDraftMock,
            ]
        );

        $result = $helper->createOrUpdateFromBackendPost($data, $user);
        $this->assertEquals([
            'id' => 1,
            'name' => 'Ticket New',
            'status_id' => 3,
            'priority_id' => 4,
            'user_id' => 8,
            'department_id' => 7,
            'customer_email' => 'john@example.com',
            'tags' => [],
            'fp_period_unit' => '',
            'fp_period_value' => '',
            'reply' => 'some message',
            'reply_type' => 'public',
            'owner' => '7_8',
            'customer_name' => 'john@example.com',
            'quote_address_id' => null,
        ], $result->getData());
    }

    /**
     * @covers Mirasvit\Helpdesk\Helper\Process::createOrUpdateFromBackendPost
     */
    public function testCreateOrUpdateFromBackendPostExistingTicket()
    {
        $data = [
            'ticket_id' => 1,
            'customer_email' => 'john@example.com',
            'tags' => [],
            'fp_period_unit' => '',
            'fp_period_value' => '',
            'reply' => 'some message',
            'reply_type' => 'public',
        ];
        $user = Mocks\Magento\UserMock::create($this);

        $ticket = Mocks\TicketMock::create($this, [], ['load', 'save', 'delete', 'addMessage']);
        $ticket->expects($this->once())
            ->method('addMessage')
            ->with($data['reply'], false, $user, Config::USER, $data['reply_type'], false, Config::FORMAT_PLAIN)
            ->willReturnSelf();

        $ticket->expects($this->once())
            ->method('load')
            ->with($data['ticket_id']);

        $ticket->expects($this->once())
            ->method('save');

        /* @var \Mirasvit\Helpdesk\Helper\Process|\PHPUnit_Framework_MockObject_MockObject $dataHelper */
        $helper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Process',
            [
                'ticketFactory' => Mocks\TicketFactoryMock::create($this, $ticket),
                'helpdeskHistory' => $this->helpdeskHistoryMock,
                'helpdeskDraft' => $this->helpdeskDraftMock,
            ]
        );

        $result = $helper->createOrUpdateFromBackendPost($data, $user);
        $this->assertEquals($ticket, $result);
    }

    /**
     * @covers Mirasvit\Helpdesk\Helper\Process::createOrUpdateFromBackendPost
     */
    public function testCreateOrUpdateFromBackendPostExistingTicketWithoutMessage()
    {
        $data = [
            'ticket_id' => 1,
            'status_id' => 5,
            'customer_email' => 'john@example.com',
            'tags' => [],
            'fp_period_unit' => '',
            'fp_period_value' => '',
            'reply' => '',
            'reply_type' => 'public',
        ];
        $user = Mocks\Magento\UserMock::create($this);

        $ticket = Mocks\TicketMock::create($this, [], ['load', 'save', 'delete', 'addMessage']);
        $ticket->expects($this->never())
            ->method('addMessage');

        $ticket->expects($this->once())
            ->method('load')
            ->with($data['ticket_id']);

        $ticket->expects($this->once())
            ->method('save');

        $_FILES['attachment']['name'][0] = '';

        /* @var \Mirasvit\Helpdesk\Helper\Process|\PHPUnit_Framework_MockObject_MockObject $dataHelper */
        $helper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Process',
            [
                'ticketFactory' => Mocks\TicketFactoryMock::create($this, $ticket),
                'helpdeskHistory' => $this->helpdeskHistoryMock,
                'helpdeskDraft' => $this->helpdeskDraftMock,
            ]
        );

        $result = $helper->createOrUpdateFromBackendPost($data, $user);
        $this->assertEquals($ticket, $result);
        $this->assertEquals($data['status_id'], $ticket->getStatusId());
    }
}
