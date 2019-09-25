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
 * @covers \Mirasvit\Helpdesk\Helper\Rule
 * @SuppressWarnings(PHPMD)
 */
class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityAttributeFactoryMock;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityAttributeMock;

    /**
     * @var \Mirasvit\Rewards\Model\System\Config\Source\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $systemConfigSourceAttributeMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->productFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->productFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->productMock));
        $this->entityAttributeFactoryMock = $this->getMock(
            '\Magento\Eav\Model\Entity\AttributeFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->entityAttributeMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->entityAttributeFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->entityAttributeMock));
        $this->systemConfigSourceAttributeMock = $this->getMock(
            '\Mirasvit\Rewards\Model\System\Config\Source\Attribute',
            [],
            [],
            '',
            false
        );
        $this->objectManagerMock = $this->getMockForAbstractClass(
            '\Magento\Framework\ObjectManagerInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->ruleHelper = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Helper\Rule',
            [
                'productFactory' => $this->productFactoryMock,
                'entityAttributeFactory' => $this->entityAttributeFactoryMock,
                'systemConfigSourceAttribute' => $this->systemConfigSourceAttributeMock,
                'context' => $this->contextMock,
                'objectManager' => $this->objectManagerMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->ruleHelper, $this->ruleHelper);
    }
}
