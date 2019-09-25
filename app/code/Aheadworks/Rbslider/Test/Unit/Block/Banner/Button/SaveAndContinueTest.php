<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Block\Adminhtml\Banner\Edit\Button;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Button\SaveAndContinue;

/**
 * Test for \Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Button\SaveAndContinue
 */
class SaveAndContinueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SaveAndContinue
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
        $this->button = $objectManager->getObject(
            SaveAndContinue::class,
            []
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
