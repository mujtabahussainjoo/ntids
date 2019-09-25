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

/**
 * @covers \Mirasvit\Helpdesk\Helper\Mage
 * @SuppressWarnings(PHPMD)
 */
class MageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Mage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mageHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderGridCollectionFactoryMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Grid\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderGridCollectionMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionFactoryMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Backend\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendUrlManagerMock;

    /**
     * setup
     */
    public function setUp()
    {
        $this->orderCollectionFactoryMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Order\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->orderCollectionMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Order\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->orderCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->orderCollectionMock));
        $this->backendUrlManagerMock = $this->getMock('\Magento\Backend\Model\Url', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->mageHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Mage',
            [
                'orderCollectionFactory' => $this->orderCollectionFactoryMock,
                'context' => $this->contextMock,
                'backendUrlManager' => $this->backendUrlManagerMock,
            ]
        );
    }

    /**
     * dummy
     */
    public function testDummy()
    {
        $this->assertEquals($this->mageHelper, $this->mageHelper);
    }
}
