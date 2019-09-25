<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Block\Widget;

use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Aheadworks\Rbslider\Block\Widget\Banner;
use Aheadworks\Rbslider\Api\BlockRepositoryInterface;
use Aheadworks\Rbslider\Api\Data\BlockInterface;
use Aheadworks\Rbslider\Api\Data\BlockSearchResultsInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Rbslider\Block\Widget\Banner
 */
class BannerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Banner
     */
    private $block;

    /**
     * @var BlockRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blocksRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->blocksRepositoryMock = $this->getMockForAbstractClass(BlockRepositoryInterface::class);
        $this->block = $objectManager->getObject(
            Banner::class,
            [
                'blocksRepository' => $this->blocksRepositoryMock
            ]
        );
    }

    /**
     * Testing of getBlocks method
     */
    public function testGetBlocks()
    {
        $bannerId = 1;

        $this->block->setData('banner_id', $bannerId);
        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $bannerMock->expects($this->once())
            ->method('getId')
            ->willReturn($bannerId);
        $blockMock = $this->getMockForAbstractClass(BlockInterface::class);
        $blockMock->expects($this->once())
            ->method('getBanner')
            ->willReturn($bannerMock);
        $blockSearchResultsMock = $this->getMockForAbstractClass(BlockSearchResultsInterface::class);
        $blockSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$blockMock]);
        $this->blocksRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($blockSearchResultsMock);

        $this->assertSame([$blockMock], $this->block->getBlocks());
    }

    /**
     * Testing of getNameInLayout method
     */
    public function testGetNameInLayout()
    {
        $bannerId = 1;
        $expected = Banner::WIDGET_NAME_PREFIX . $bannerId;

        $this->block->setData('banner_id', $bannerId);
        $this->assertEquals($expected, $this->block->getNameInLayout());
    }
}
