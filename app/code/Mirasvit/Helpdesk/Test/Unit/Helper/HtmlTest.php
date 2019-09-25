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
class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Mage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskMageMock;

    /**
     * setup
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
     * @covers Mirasvit\Helpdesk\Helper\Html::toAdminUserOptionArray
     */
    public function testToAdminUserOptionArray()
    {
        /** @var \Mirasvit\Helpdesk\Helper\Html|\PHPUnit_Framework_MockObject_MockObject $helper */
        $helper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Html',
            [
                'userCollectionFactory' => Mocks\Magento\User\CollectionFactoryMock::create($this),
            ]
        );

        $result = $helper->toAdminUserOptionArray();

        $expectedResult = [
            [
                'value' => 1,
                'label' => 'John Doe',
            ],
            [
                'value' => 2,
                'label' => 'Bill Gates',
            ],
            [
                'value' => 3,
                'label' => 'Jim White',
            ],
        ];
        $this->assertEquals($expectedResult, $result);

        $result = $helper->toAdminUserOptionArray(true);

        $expectedResult = [
            [
                'value' => 0,
                'label' => __('-- Please Select --'),
            ],
            [
                'value' => 1,
                'label' => 'John Doe',
            ],
            [
                'value' => 2,
                'label' => 'Bill Gates',
            ],
            [
                'value' => 3,
                'label' => 'Jim White',
            ],
        ];
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers Mirasvit\Helpdesk\Helper\Html::getAdminUserOptionArray
     */
    public function testGetAdminUserOptionArray()
    {
        /** @var \Mirasvit\Helpdesk\Helper\Html|\PHPUnit_Framework_MockObject_MockObject $helper */
        $helper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Html',
            [
                'userCollectionFactory' => Mocks\Magento\User\CollectionFactoryMock::create($this),
            ]
        );

        $result = $helper->getAdminUserOptionArray();

        $expectedResult = [
                1 => 'John Doe',
                    2 => 'Bill Gates',
        3 => 'Jim White',
        ];
        $this->assertEquals($expectedResult, $result);

        $result = $helper->getAdminUserOptionArray(true);

        $expectedResult = [
            0 => __('-- Please Select --'),
            1 => 'John Doe',
            2 => 'Bill Gates',
            3 => 'Jim White',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers Mirasvit\Helpdesk\Helper\Html::getCoreStoreOptionArray
     */
    public function testGetCoreStoreOptionArray()
    {
        /** @var \Mirasvit\Helpdesk\Helper\Html|\PHPUnit_Framework_MockObject_MockObject $helper */
        $helper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Html',
            [
                'storeCollectionFactory' => Mocks\Magento\Store\CollectionFactoryMock::create($this),
            ]
        );

        $result = $helper->getCoreStoreOptionArray();
        $expectedResult = [
            '2' => 'German Store',
            '1' => 'English Store',
            '3' => 'Frech Store',
        ];
        $this->assertEquals(json_encode($expectedResult), json_encode($result));//we would like to test array order also

        $result = $helper->getCoreStoreOptionArray(true);
        $expectedResult = [
            '2' => 'German Store',
            '1' => 'English Store',
            '3' => 'Frech Store',
            '0' => '-- Please Select --',
        ];
        $this->assertEquals(json_encode($expectedResult), json_encode($result));//we would like to test array order also
    }

    /**
     * @covers Mirasvit\Helpdesk\Helper\Html::toAdminRoleOptionArray
     */
    public function testToAdminRoleOptionArray()
    {
        /** @var \Mirasvit\Helpdesk\Helper\Html|\PHPUnit_Framework_MockObject_MockObject $helper */
        $helper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Html',
            [
                'roleCollectionFactory' => Mocks\Magento\Role\CollectionFactoryMock::create($this),
            ]
        );

        $result = $helper->toAdminRoleOptionArray();

        $expectedResult = [
            [
                'value' => 1,
                'label' => 'Administrators',
            ],
            [
                'value' => 2,
                'label' => 'Managers',
            ],
        ];
        $this->assertEquals($expectedResult, $result);

        $result = $helper->toAdminRoleOptionArray(true);
        $expectedResult = [
            [
                'value' => 0,
                'label' => __('-- Please Select --'),
            ],
            [
                'value' => 1,
                'label' => 'Administrators',
            ],
            [
                'value' => 2,
                'label' => 'Managers',
            ],
        ];
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers Mirasvit\Helpdesk\Helper\Html::getAdminRoleOptionArray
     */
    public function testGetAdminRoleOptionArray()
    {
        /** @var \Mirasvit\Helpdesk\Helper\Html|\PHPUnit_Framework_MockObject_MockObject $helper */
        $helper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Html',
            [
                'roleCollectionFactory' => Mocks\Magento\Role\CollectionFactoryMock::create($this),
            ]
        );

        $result = $helper->getAdminRoleOptionArray();

        $expectedResult = [
            1 => 'Administrators',
            2 => 'Managers',
        ];
        $this->assertEquals($expectedResult, $result);

        $result = $helper->getAdminRoleOptionArray(true);
        $expectedResult = [
            0 => __('-- Please Select --'),
            1 => 'Administrators',
            2 => 'Managers',
        ];
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers Mirasvit\Helpdesk\Helper\Html::getAdminOwnerOptionArray
     */
    public function testGetAdminOwnerOptionArray()
    {
        /** @var \Mirasvit\Helpdesk\Helper\Html|\PHPUnit_Framework_MockObject_MockObject $helper */
        $helper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Html',
            [
                'departmentCollectionFactory' => Mocks\Department\CollectionFactoryMock::create($this),
            ]
        );

        $result = $helper->getAdminOwnerOptionArray();

        $expectedResult = [
            '2_0' => 'Support',
            '2_1' => '- John Doe',
            '2_2' => '- Bill White',
            '1_0' => 'Sales',
            '1_1' => '- John Doe',
            '1_2' => '- Bill White',
        ];
        $this->assertEquals(json_encode($expectedResult), json_encode($result));//we would like to test array order also

        $result = $helper->getAdminOwnerOptionArray(true, 2);
        $expectedResult = [
            '0_0' => __('-- Please Select --'),
            '2_0' => 'Support',
            '2_1' => '- John Doe',
            '2_2' => '- Bill White',
            '1_0' => 'Sales',
            '1_1' => '- John Doe',
            '1_2' => '- Bill White',
        ];
        $this->assertEquals(json_encode($expectedResult), json_encode($result));//we would like to test array order also
    }
}
