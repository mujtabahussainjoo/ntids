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
use Mirasvit\Helpdesk\Test\Unit\Mocks;

/**
 * @covers \Mirasvit\Helpdesk\Helper\Data
 * @SuppressWarnings(PHPMD)
 */
class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Mage
     */
    protected $helpdeskMageMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected  $localeDateMock;

     /**
      * protected $authMock;
      */
    public function setUp()
    {
        $this->helpdeskMageMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Mage',
            ['getBackendOrderUrl'],
            [],
            '',
            false
        );

        $this->localeDateMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->objectManager = new ObjectManager($this);

    }

    /**
     * @covers Mirasvit\Helpdesk\Helper\Order::getOrderLabel
     */
    public function testGetOrderArray()
    {
        /** @var \Mirasvit\Helpdesk\Helper\Data|\PHPUnit_Framework_MockObject_MockObject $helper */
        $helper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Order',
            [
                'orderCollectionFactory' => Mocks\Magento\Order\CollectionFactoryMock::create($this),
                'localeDate' => $this->localeDateMock,
                'helpdeskMage' => $this->helpdeskMageMock,
            ]
        );
        $this->localeDateMock
            ->method('formatDate')
            ->willReturnArgument(0);

        $this->helpdeskMageMock->method('getBackendOrderUrl')
            ->willReturnCallback(function ($x) {
                return 'http://store.com/admin/order/'.$x;
            });

        $result = $helper->getOrderArray('john2@example.com');
        $expectedResult = [
            [
                'id' => 4,
                'name' => '#000000004 at 2015-09-16 00:00:00 ($34.43) - Complete',
                'label' => '#000000004 at 2015-09-16 00:00:00 ($34.43) - Complete',
                'url' => 'http://store.com/admin/order/4',
            ],
            [
                'id' => 2,
                'name' => '#000000002 at 2015-09-14 00:00:00 ($1340.54) - Complete',
                'label' => '#000000002 at 2015-09-14 00:00:00 ($1340.54) - Complete',
                'url' => 'http://store.com/admin/order/2',
            ],
        ];
        $this->assertEquals($expectedResult, $result);
    }
}
