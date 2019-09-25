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
 * @covers \Mirasvit\Helpdesk\Helper\Draft
 * @SuppressWarnings(PHPMD)
 */
class DraftTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Draft|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $draftHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\DraftFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $draftFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Draft|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $draftMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Draft\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $draftCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Draft\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $draftCollectionMock;

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userCollectionFactoryMock;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userCollectionMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskStringMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->draftFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\DraftFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->draftMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Draft',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->draftFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->draftMock));
        $this->draftCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Draft\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->draftCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Draft\Collection',
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
        $this->draftCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->draftCollectionMock));
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
        $this->dateMock = $this->getMock(
            '\Magento\Framework\Stdlib\DateTime\DateTime',
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
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->draftHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Draft',
            [
                'draftFactory' => $this->draftFactoryMock,
                'draftCollectionFactory' => $this->draftCollectionFactoryMock,
                'userCollectionFactory' => $this->userCollectionFactoryMock,
                'date' => $this->dateMock,
                'helpdeskString' => $this->helpdeskStringMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->draftHelper, $this->draftHelper);
    }
}
