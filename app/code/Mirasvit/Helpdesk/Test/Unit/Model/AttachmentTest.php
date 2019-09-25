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
use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @covers \Mirasvit\Helpdesk\Model\Attachment
 * @SuppressWarnings(PHPMD)
 */
class AttachmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Attachment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentModel;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskStringMock;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlManagerMock;

    /**
     * @var \Magento\Backend\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendUrlManagerMock;

    /**
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

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
     * setup tests
     */
    public function setUp()
    {
        $this->configMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->helpdeskStringMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\StringUtil',
            [],
            [],
            '',
            false
        );
        $this->urlManagerMock = $this->getMockForAbstractClass(
            '\Magento\Framework\UrlInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->backendUrlManagerMock = $this->getMock(
            '\Magento\Backend\Model\Url',
            [],
            [],
            '',
            false
        );
        $this->filesystemMock = $this->getMock(
            '\Magento\Framework\Filesystem',
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
            '\Mirasvit\Helpdesk\Model\ResourceModel\Attachment',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Attachment\Collection',
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
        $this->attachmentModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Attachment',
            [
                'config' => $this->configMock,
                'helpdeskString' => $this->helpdeskStringMock,
                'urlManager' => $this->urlManagerMock,
                'backendUrlManager' => $this->backendUrlManagerMock,
                'filesystem' => $this->filesystemMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    /**
     * dummy test
     */
    public function testDummy()
    {
        $this->assertEquals($this->attachmentModel, $this->attachmentModel);
    }
}
