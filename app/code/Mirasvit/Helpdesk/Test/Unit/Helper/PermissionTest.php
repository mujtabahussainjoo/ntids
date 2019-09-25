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
 * @covers \Mirasvit\Helpdesk\Helper\Permission
 * @SuppressWarnings(PHPMD)
 */
class PermissionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Permission|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $permissionHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Permission\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $permissionCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Permission\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $permissionCollectionMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Backend\Model\Auth|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->permissionCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Permission\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->permissionCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Permission\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->permissionCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->permissionCollectionMock));
        $this->authMock = $this->getMock('\Magento\Backend\Model\Auth', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->permissionHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Permission',
            [
                'permissionCollectionFactory' => $this->permissionCollectionFactoryMock,
                'context' => $this->contextMock,
                'auth' => $this->authMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->permissionHelper, $this->permissionHelper);
    }
}
