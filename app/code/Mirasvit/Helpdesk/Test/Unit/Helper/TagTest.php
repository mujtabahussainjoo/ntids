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
 * @covers \Mirasvit\Helpdesk\Helper\Tag
 * @SuppressWarnings(PHPMD)
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Tag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tagHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\TagFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tagFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Tag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tagMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Tag\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tagCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Tag\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $tagCollectionMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->tagFactoryMock = $this->getMock('\Mirasvit\Helpdesk\Model\TagFactory', ['create'], [], '', false);
        $this->tagMock = $this->getMock('\Mirasvit\Helpdesk\Model\Tag', ['load', 'save', 'delete'], [], '', false);
        $this->tagFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->tagMock));
        $this->tagCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Tag\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->tagCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Tag\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->tagCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->tagCollectionMock));
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->tagHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Tag',
            [
                'tagFactory' => $this->tagFactoryMock,
                'tagCollectionFactory' => $this->tagCollectionFactoryMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->tagHelper, $this->tagHelper);
    }
}
