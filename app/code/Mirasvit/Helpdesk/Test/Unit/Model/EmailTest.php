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
 * @covers \Mirasvit\Helpdesk\Model\Email
 * @SuppressWarnings(PHPMD)
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Email|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\GatewayFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Gateway|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gatewayMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentCollectionMock;

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
        $this->gatewayFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\GatewayFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->gatewayMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Gateway',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->gatewayFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->gatewayMock));
        $this->attachmentCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Attachment\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->attachmentCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Attachment\Collection',
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
        $this->attachmentCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->attachmentCollectionMock));
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Email',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Email\Collection',
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
        $this->emailModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Email',
            [
                'gatewayFactory' => $this->gatewayFactoryMock,
                'attachmentCollectionFactory' => $this->attachmentCollectionFactoryMock,
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
        $this->assertEquals($this->emailModel, $this->emailModel);
    }
}
