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
 * @covers \Mirasvit\Helpdesk\Model\Cron
 * @SuppressWarnings(PHPMD)
 */
class CronTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Cron|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cronModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\GatewayFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Gateway|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayMock;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scheduleCollectionFactoryMock;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scheduleCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayCollectionMock;

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
     * @var \Mirasvit\Helpdesk\Helper\Ruleevent|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskRuleeventMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Followup|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskFollowupMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Fetch|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskFetchMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Email|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskEmailMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateMock;

    /**
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->gatewayFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\GatewayFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->gatewayMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Gateway',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->gatewayFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->gatewayMock));
        $this->scheduleCollectionFactoryMock = $this->getMock(
            '\Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->scheduleCollectionMock = $this->getMock(
            '\Magento\Cron\Model\ResourceModel\Schedule\Collection',
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
        $this->scheduleCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->scheduleCollectionMock));
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
        $this->gatewayCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->gatewayCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Gateway\Collection',
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
        $this->gatewayCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->gatewayCollectionMock));
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
        $this->helpdeskRuleeventMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Ruleevent',
            [],
            [],
            '',
            false
        );
        $this->helpdeskFollowupMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Followup',
            [],
            [],
            '',
            false
        );
        $this->helpdeskFetchMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Fetch',
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
        $this->dateMock = $this->getMock(
            '\Magento\Framework\Stdlib\DateTime\DateTime',
            [],
            [],
            '',
            false
        );
        $this->filesystemMock = $this->getMock(
            '\Magento\Framework\Filesystem',
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
        $this->cronModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Cron',
            [
                'gatewayFactory' => $this->gatewayFactoryMock,
                'scheduleCollectionFactory' => $this->scheduleCollectionFactoryMock,
                'ticketCollectionFactory' => $this->ticketCollectionFactoryMock,
                'gatewayCollectionFactory' => $this->gatewayCollectionFactoryMock,
                'emailCollectionFactory' => $this->emailCollectionFactoryMock,
                'config' => $this->configMock,
                'helpdeskRuleevent' => $this->helpdeskRuleeventMock,
                'helpdeskFollowup' => $this->helpdeskFollowupMock,
                'helpdeskFetch' => $this->helpdeskFetchMock,
                'helpdeskEmail' => $this->helpdeskEmailMock,
                'date' => $this->dateMock,
                'filesystem' => $this->filesystemMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->cronModel, $this->cronModel);
    }
}
