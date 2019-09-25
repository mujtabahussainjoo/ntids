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
 * @covers \Mirasvit\Helpdesk\Model\Rule\Condition\Combine
 * @SuppressWarnings(PHPMD)
 */
class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Rule\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $combineModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\Rule\Condition\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionProductFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Rule\Condition\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionProductMock;

    /**
     * @var \Magento\Salesrule\Model\Rule\Condition\AddressFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionAddressFactoryMock;

    /**
     * @var \Magento\Salesrule\Model\Rule\Condition\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionAddressMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Rule\Condition\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionTicketFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Rule\Condition\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionTicketMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskRuleMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

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
        $this->ruleConditionProductFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Rule\Condition\ProductFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleConditionProductMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Rule\Condition\Product',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->ruleConditionProductFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleConditionProductMock));
        $this->ruleConditionAddressFactoryMock = $this->getMock(
            '\Magento\Salesrule\Model\Rule\Condition\AddressFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleConditionAddressMock = $this->getMock(
            '\Magento\Salesrule\Model\Rule\Condition\Address',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->ruleConditionAddressFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleConditionAddressMock));
        $this->ruleConditionTicketFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Rule\Condition\TicketFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleConditionTicketMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Rule\Condition\Ticket',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->ruleConditionTicketFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleConditionTicketMock));
        $this->helpdeskRuleMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Rule',
            [],
            [],
            '',
            false
        );
        $this->requestMock = $this->getMock(
            '\Magento\Framework\App\Request\Http',
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
            '\Mirasvit\Helpdesk\Model\ResourceModel\Rule\Condition\Combine',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Rule\Condition\Combine\Collection',
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
        $this->combineModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Rule\Condition\Combine',
            [
                'ruleConditionProductFactory' => $this->ruleConditionProductFactoryMock,
                'ruleConditionAddressFactory' => $this->ruleConditionAddressFactoryMock,
                'ruleConditionTicketFactory' => $this->ruleConditionTicketFactoryMock,
                'helpdeskRule' => $this->helpdeskRuleMock,
                'request' => $this->requestMock,
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
        $this->assertEquals($this->combineModel, $this->combineModel);
    }
}
