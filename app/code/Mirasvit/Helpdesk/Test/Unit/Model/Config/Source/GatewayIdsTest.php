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



namespace Mirasvit\Helpdesk\Test\Unit\Model\Config\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Helpdesk\Model\Config\Source\GatewayIds
 * @SuppressWarnings(PHPMD)
 */
class GatewayIdsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config\Source\GatewayIds|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayIdsModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayCollectionMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->gatewayCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->gatewayCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Gateway\Collection',
            [
                'load',
                'save',
                'delete',
                'addFieldToFilter',
                'setOrder',
                'getFirstItem',
                'getLastItem',
                'getOptionArray'
            ],
            [],
            '',
            false
        );
        $this->gatewayCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->gatewayCollectionMock));
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->gatewayIdsModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Config\Source\GatewayIds',
            [
                'gatewayCollectionFactory' => $this->gatewayCollectionFactoryMock,
            ]
        );
    }

    /**
     * @covers \Mirasvit\Helpdesk\Model\Config\Source\GatewayIds::toArray
     * @covers \Mirasvit\Helpdesk\Model\Config\Source\GatewayIds::toOptionArray
     */
    public function testToOptionArray()
    {
        $this->gatewayCollectionMock
            ->expects($this->exactly(2))
            ->method('getOptionArray')
            ->willReturn([1 => 'Gateway 1', 2 => 'Gateway 2']);

        $this->assertEquals(
            [1 => 'Gateway 1', 2 => 'Gateway 2'],
            $this->gatewayIdsModel->toArray()
        );
        $this->assertEquals(
            [
                ['value' => 1, 'label' => 'Gateway 1'],
                ['value' => 2, 'label' => 'Gateway 2'],
            ],
            $this->gatewayIdsModel->toOptionArray()
        );
    }
}
