<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Block;

use Aheadworks\Rbslider\Model\Source\PageType;
use Aheadworks\Rbslider\Model\Source\Position;
use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Aheadworks\Rbslider\Block\Banner;
use Aheadworks\Rbslider\Model\Source\AnimationEffect;
use Aheadworks\Rbslider\Model\Source\ImageType;
use Aheadworks\Rbslider\Model\Source\UikitAnimation;
use Aheadworks\Rbslider\Model\Slide\ImageFileUploader;
use Aheadworks\Rbslider\Api\BlockRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Rbslider\Api\Data\BlockInterface;
use Aheadworks\Rbslider\Api\Data\BlockSearchResultsInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\RequestInterface;

/**
 * Test for \Aheadworks\Rbslider\Block\Banner
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var ImageFileUploader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageFileUploaderMock;

    /**
     * @var UikitAnimation|\PHPUnit_Framework_MockObject_MockObject
     */
    private $uikitAnimationMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->blocksRepositoryMock = $this->getMockForAbstractClass(BlockRepositoryInterface::class);
        $this->imageFileUploaderMock = $this->getMockBuilder(ImageFileUploader::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMediaUrl'])
            ->getMock();
        $this->uikitAnimationMock = $this->getMockBuilder(UikitAnimation::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAnimationEffectByKey'])
            ->getMock();
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['isAjax']
        );
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $this->urlBuilderMock,
                'request' => $this->requestMock
            ]
        );

        $this->block = $objectManager->getObject(
            Banner::class,
            [
                'context' => $contextMock,
                'blocksRepository' => $this->blocksRepositoryMock,
                'imageFileUploader' => $this->imageFileUploaderMock,
                'uikitAnimation' => $this->uikitAnimationMock
            ]
        );
    }

    /**
     * Testing of isAjax method
     *
     * @param bool $isAjax
     * @param bool $expected
     * @dataProvider isAjaxDataProvider
     */
    public function testIsAjax($isAjax, $expected)
    {
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn($isAjax);

        $this->assertEquals($expected, $this->block->isAjax());
    }

    /**
     * Data provider for testIsAjax method
     *
     * @return array
     */
    public function isAjaxDataProvider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }

    /**
     * Testing of getBlocks method
     */
    public function testGetBlocks()
    {
        $this->block->setNameInLayout('banner_category_menu_top');
        $blockMock = $this->getMockForAbstractClass(BlockInterface::class);
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
     * Testing of getSlideImgUrl method
     *
     * @param int $imgType
     * @param string $imgFile
     * @param string $imgUrl
     * @param bool $expected
     * @dataProvider getSlideImgUrlDataProvider
     */
    public function testGetSlideImgUrl($imgType, $imgFile, $imgUrl, $expected)
    {
        $impPath = 'https://ecommerce.aheadworks.com/pub/media/aw_rbslider/slides/';
        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $slideMock->expects($this->once())
            ->method('getImgType')
            ->willReturn($imgType);
        $slideMock->expects($this->any())
            ->method('getImgFile')
            ->willReturn($imgFile);
        $slideMock->expects($this->any())
            ->method('getImgUrl')
            ->willReturn($imgUrl);
        $this->imageFileUploaderMock->expects($this->any())
            ->method('getMediaUrl')
            ->with($imgFile)
            ->willReturn($impPath . $imgFile);

        $this->assertEquals($expected, $this->block->getSlideImgUrl($slideMock));
    }

    /**
     * Data provider for testGetSlideImgUrl method
     *
     * @return array
     */
    public function getSlideImgUrlDataProvider()
    {
        return [
            [
                ImageType::TYPE_FILE,
                '1.png',
                '',
                'https://ecommerce.aheadworks.com/pub/media/aw_rbslider/slides/1.png'
            ],
            [
                ImageType::TYPE_URL,
                '',
                'https://ecommerce.aheadworks.com/my_img.png',
                'https://ecommerce.aheadworks.com/my_img.png'
            ]
        ];
    }

    /**
     * Testing of getLinkUrl method
     *
     * @param int $slideId
     * @param int $bannerId
     * @dataProvider getLinkUrlDataProvider
     */
    public function testGetLinkUrl($slideId, $bannerId)
    {
        $expected = 'https://ecommerce.aheadworks.com/aw_rbslider/countClicks/redirect/'.$slideId.'/'.$bannerId;
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                'aw_rbslider/countClicks/redirect',
                [
                    'slide_id' => $slideId,
                    'banner_id' => $bannerId,
                ]
            )->willReturn($expected);

        $this->assertEquals($expected, $this->block->getLinkUrl($slideId, $bannerId));
    }

    /**
     * Data provider for testGetLinkUrl method
     *
     * @return array
     */
    public function getLinkUrlDataProvider()
    {
        return [
            [1, 2],
            [2, 2]
        ];
    }

    /**
     * Testing of getAnimation method
     *
     * @param int $key
     * @param string $expected
     * @dataProvider getAnimationDataProvider
     */
    public function testGetAnimation($key, $expected)
    {
        $this->uikitAnimationMock->expects($this->once())
            ->method('getAnimationEffectByKey')
            ->with($key)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->block->getAnimation($key));
    }

    /**
     * Data provider for testGetAnimation method
     *
     * @return array
     */
    public function getAnimationDataProvider()
    {
        return [
            [AnimationEffect::SLIDE, 'scroll'],
            [AnimationEffect::FADE_OUT_IN, 'fade'],
            [AnimationEffect::SCALE, 'scale']
        ];
    }

    /**
     * Testing of getBlockPosition method
     *
     * @param string $nameInLayout
     * @param int $expected
     * @dataProvider getBlockPositionDataProvider
     */
    public function testGetBlockPosition($nameInLayout, $expected)
    {
        $this->block->setNameInLayout($nameInLayout);

        $class = new \ReflectionClass($this->block);
        $method = $class->getMethod('getBlockPosition');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invoke($this->block));
    }

    /**
     * Data provider for testGetBlockPosition method
     *
     * @return array
     */
    public function getBlockPositionDataProvider()
    {
        return [
            ['banner_home_menu_top', Position::MENU_TOP],
            ['banner_home_menu_bottom', Position::MENU_BOTTOM],
            ['banner_home_content_top', Position::CONTENT_TOP],
            ['banner_home_page_bottom', Position::PAGE_BOTTOM]
        ];
    }

    /**
     * Testing of getBlockType method
     *
     * @param string $nameInLayout
     * @param int $expected
     * @dataProvider getBlockTypeDataProvider
     */
    public function testGetBlockType($nameInLayout, $expected)
    {
        $this->block->setNameInLayout($nameInLayout);

        $class = new \ReflectionClass($this->block);
        $method = $class->getMethod('getBlockType');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invoke($this->block));
    }

    /**
     * Data provider for testGetBlockType method
     *
     * @return array
     */
    public function getBlockTypeDataProvider()
    {
        return [
            ['banner_home_menu_bottom', PageType::HOME_PAGE],
            ['banner_category_content_top', PageType::CATEGORY_PAGE],
            ['banner_product_page_bottom', PageType::PRODUCT_PAGE]
        ];
    }
}
