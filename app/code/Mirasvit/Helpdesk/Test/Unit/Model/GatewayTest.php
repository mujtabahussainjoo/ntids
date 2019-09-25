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

/**
 * @covers \Mirasvit\Helpdesk\Model\Gateway
 * @SuppressWarnings(PHPMD)
 */
class GatewayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Gateway|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\DepartmentFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Department|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentMock;

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
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->departmentFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->departmentMock));
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Gateway',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Gateway\Collection',
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
        $this->gatewayModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Gateway',
            [
                'departmentFactory' => $this->departmentFactoryMock,
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
        $this->assertEquals($this->gatewayModel, $this->gatewayModel);
    }
}
