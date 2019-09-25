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
 * @covers \Mirasvit\Helpdesk\Helper\Ruleevent
 * @SuppressWarnings(PHPMD)
 */
class RuleeventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Ruleevent|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleeventHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Rule\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Rule\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Tag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskTagMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\History|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskHistoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Notification|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskNotificationMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->ruleCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Rule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Rule\Collection',
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
        $this->ruleCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleCollectionMock));
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
        $this->configMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->helpdeskTagMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Tag',
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
        $this->helpdeskNotificationMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Notification',
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
        $this->ruleeventHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Ruleevent',
            [
                'ruleCollectionFactory' => $this->ruleCollectionFactoryMock,
                'ticketCollectionFactory' => $this->ticketCollectionFactoryMock,
                'config' => $this->configMock,
                'helpdeskTag' => $this->helpdeskTagMock,
                'helpdeskHistory' => $this->helpdeskHistoryMock,
                'helpdeskNotification' => $this->helpdeskNotificationMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->ruleeventHelper, $this->ruleeventHelper);
    }
}
