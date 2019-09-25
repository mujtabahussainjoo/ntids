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
 * @covers \Mirasvit\Helpdesk\Model\Message
 * @SuppressWarnings(PHPMD)
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Message|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketMock;

    /**
     * @var \Magento\User\Model\UserFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userFactoryMock;

    /**
     * @var \Magento\User\Model\User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Email\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Email\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Core\Api\TextHelperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mstcoreStringMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskStringMock;

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
     * setUp
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
        $this->departmentCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->departmentCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection',
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
        $this->departmentCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->departmentCollectionMock));
        $this->emailCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Email\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->emailCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Email\Collection',
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
        $this->emailCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->emailCollectionMock));
        $this->configMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->mstcoreStringMock = $this->getMock(
            '\Mirasvit\Core\Api\TextHelperInterface',
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
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Message',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Message\Collection',
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
        $this->messageModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Message',
            [
                'ticketFactory' => $this->ticketFactoryMock,
                'userFactory' => $this->userFactoryMock,
                'attachmentCollectionFactory' => $this->attachmentCollectionFactoryMock,
                'departmentCollectionFactory' => $this->departmentCollectionFactoryMock,
                'emailCollectionFactory' => $this->emailCollectionFactoryMock,
                'config' => $this->configMock,
                'mstcoreString' => $this->mstcoreStringMock,
                'helpdeskString' => $this->helpdeskStringMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    /**
     * @covers Mirasvit\Helpdesk\Model\Message::beforeSave
     */
    public function testBeforeSave()
    {
        $this->messageModel->beforeSave();
        $this->assertNotEmpty($this->messageModel->getUid());
    }
}
