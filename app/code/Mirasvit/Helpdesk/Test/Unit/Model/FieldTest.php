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
 * @covers \Mirasvit\Helpdesk\Model\Field
 * @SuppressWarnings(PHPMD)
 */
class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Field|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\FieldFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Field|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldMock;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Storeview|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskStoreviewMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceCollectionMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->fieldFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\FieldFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->fieldMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Field',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->fieldFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->fieldMock));
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Field',
            [],
            [],
            '',
            false
        );
        $this->helpdeskStoreviewMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Storeview',
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
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection',
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
        $this->fieldModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Field',
            [
                'fieldFactory' => $this->fieldFactoryMock,
                'resource' => $this->resourceMock,
                'helpdeskStoreview' => $this->helpdeskStoreviewMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->fieldModel, $this->fieldModel);
    }
}
