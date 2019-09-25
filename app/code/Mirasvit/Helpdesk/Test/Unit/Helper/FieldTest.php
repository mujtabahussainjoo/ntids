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
 * @covers \Mirasvit\Helpdesk\Helper\Field
 * @SuppressWarnings(PHPMD)
 */
class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Field|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldCollectionFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldCollectionMock;

    /**
     * @var \Mirasvit\Core\Helper\Date|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mstcoreDateMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    /**
     * @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetRepoMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->fieldCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->fieldCollectionMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection',
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
        $this->fieldCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->fieldCollectionMock));
        $this->mstcoreDateMock = $this->getMock(
            '\Mirasvit\Core\Helper\Date',
            [],
            [],
            '',
            false
        );
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface',
            [],
            '',
            false,
            true,
            true,
            []
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
        $this->assetRepoMock = $this->getMock(
            '\Magento\Framework\View\Asset\Repository',
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
        $this->fieldHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Field',
            [
                'fieldCollectionFactory' => $this->fieldCollectionFactoryMock,
                'mstcoreDate' => $this->mstcoreDateMock,
                'context' => $this->contextMock,
                'storeManager' => $this->storeManagerMock,
                'localeDate' => $this->localeDateMock,
                'assetRepo' => $this->assetRepoMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->fieldHelper, $this->fieldHelper);
    }
}
