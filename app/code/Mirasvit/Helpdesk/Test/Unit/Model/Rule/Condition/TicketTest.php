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



namespace Mirasvit\Helpdesk\Test\Unit\Model\Rule\Condition;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Helpdesk\Model\Rule\Condition\Ticket
 * @SuppressWarnings(PHPMD)
 */
class TicketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Rule\Condition\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Status\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Status\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Priority\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priorityCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Priority\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priorityCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Tag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskTagMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Field|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskFieldMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskDataMock;

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
     * setUp()
     */
    public function setUp()
    {
        $this->markTestSkipped();

        $this->fieldCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->fieldCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->fieldCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->fieldCollectionMock));
        $this->statusCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Status\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->statusCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Status\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->statusCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->statusCollectionMock));
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
        $this->priorityCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Priority\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->priorityCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Priority\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->priorityCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->priorityCollectionMock));
        $this->helpdeskTagMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Tag', [], [], '', false);
        $this->helpdeskFieldMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Field', [], [], '', false);
        $this->helpdeskDataMock = $this->getMock('\Mirasvit\Helpdesk\Helper\Data', [], [], '', false);
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Rule\Condition\Ticket',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Rule\Condition\Ticket\Collection',
            [],
            [],
            '',
            false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            'Magento\Rule\Model\Condition\Context',
            [
            ]
        );
        $this->ticketModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Rule\Condition\Ticket',
            [
                'fieldCollectionFactory' => $this->fieldCollectionFactoryMock,
                'statusCollectionFactory' => $this->statusCollectionFactoryMock,
                'departmentCollectionFactory' => $this->departmentCollectionFactoryMock,
                'priorityCollectionFactory' => $this->priorityCollectionFactoryMock,
                'helpdeskTag' => $this->helpdeskTagMock,
                'helpdeskField' => $this->helpdeskFieldMock,
                'helpdeskData' => $this->helpdeskDataMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    /**
     * testDummy
     */
    public function testDummy()
    {
        $this->assertEquals($this->ticketModel, $this->ticketModel);
    }
}
