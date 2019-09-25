<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Block\Adminhtml\Slide\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Rbslider\Block\Adminhtml\Slide\Edit\Button\Back;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;

/**
 * Test for \Aheadworks\Rbslider\Block\Adminhtml\Slide\Edit\Button\Back
 */
class BackTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Back
     */
    private $button;
    
    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $backUrl = 'https://ecommerce.aheadworks.com/index.php/admin/aw_rbslider_admin/slide';
        $urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/')
            ->willReturn($backUrl);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $urlBuilderMock
            ]
        );
        $this->button = $objectManager->getObject(
            Back::class,
            [
                'context' => $contextMock
            ]
        );
    }

    /**
     * Testing of return value of getButtonData method
     */
    public function testGetButtonData()
    {
        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}
