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


namespace Mirasvit\Helpdesk\Test\Unit\Model\Config\Source\Ticket\Grid;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @covers \Mirasvit\Helpdesk\Model\Config\Source\Ticket\Grid\Columns
 * @SuppressWarnings(PHPMD)
 */
class ColumnsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config\Source\Ticket\Grid\Columns|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $columnsModel;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Field|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskFieldMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;


    /**
     * setup tests
     */
    public function setUp()
    {
        $this->helpdeskFieldMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\Field',
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
        $this->columnsModel = $this->objectManager->getObject(
            '\Mirasvit\Helpdesk\Model\Config\Source\Ticket\Grid\Columns',
            [
                'helpdeskField' => $this->helpdeskFieldMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test
     */
    public function testDummy()
    {
        $this->assertEquals($this->columnsModel, $this->columnsModel);
    }
}
