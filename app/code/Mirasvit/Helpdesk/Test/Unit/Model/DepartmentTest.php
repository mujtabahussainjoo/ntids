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
 * @covers \Mirasvit\Helpdesk\Model\Department
 * @SuppressWarnings(PHPMD)
 */
class DepartmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Department|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $departmentModel;

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userCollectionFactoryMock;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Storeview|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskStoreviewMock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Config\Model\Config\Source\Email\Identity|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailIdentityMock;

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
        $this->userCollectionFactoryMock = $this->getMock(
            '\Magento\User\Model\ResourceModel\User\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->userCollectionMock = $this->getMock(
            '\Magento\User\Model\ResourceModel\User\Collection',
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
        $this->userCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->userCollectionMock));
        $this->helpdeskStoreviewMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Storeview',
            [],
            [],
            '',
            false
        );
        $this->scopeConfigMock = $this->getMockForAbstractClass(
            '\Magento\Framework\App\Config\ScopeConfigInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->emailIdentityMock = $this->getMock(
            '\Magento\Config\Model\Config\Source\Email\Identity',
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
            '\Mirasvit\Helpdesk\Model\ResourceModel\Department',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection',
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
        $this->departmentModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Department',
            [
                'userCollectionFactory' => $this->userCollectionFactoryMock,
                'helpdeskStoreview' => $this->helpdeskStoreviewMock,
                'scopeConfig' => $this->scopeConfigMock,
                'emailIdentity' => $this->emailIdentityMock,
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
        $this->assertEquals($this->departmentModel, $this->departmentModel);
    }
}
