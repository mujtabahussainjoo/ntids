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
use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @covers \Mirasvit\Helpdesk\Helper\Fetch
 * @SuppressWarnings(PHPMD)
 */
class FetchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Fetch|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fetchHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\EmailFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Email|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\AttachmentFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Attachment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Email\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Email\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailCollectionMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->emailFactoryMock = $this->getMock('\Mirasvit\Helpdesk\Model\EmailFactory', ['create'], [], '', false);
        $this->emailMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Email',
            ['load', 'save', 'delete'],
            [],
            '',
            false
        );
        $this->emailFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->emailMock));
        $this->attachmentFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\AttachmentFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->attachmentMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Attachment',
            ['load', 'save', 'delete'],
            [],
            '',
            false
        );
        $this->attachmentFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->attachmentMock));
        $this->emailCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Email\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->emailCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Email\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->emailCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->emailCollectionMock));
        $this->configMock = $this->getMock('\Mirasvit\Helpdesk\Model\Config', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->fetchHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Fetch',
            [
                'emailFactory' => $this->emailFactoryMock,
                'attachmentFactory' => $this->attachmentFactoryMock,
                'emailCollectionFactory' => $this->emailCollectionFactoryMock,
                'config' => $this->configMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->fetchHelper, $this->fetchHelper);
    }
}
