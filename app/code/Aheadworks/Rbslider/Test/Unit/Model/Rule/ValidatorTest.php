<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Model\Rule\Viewed;

use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Aheadworks\Rbslider\Model\Banner;
use Aheadworks\Rbslider\Model\Rule\Validator;
use Aheadworks\Rbslider\Model\Source\PageType;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Rbslider\Model\Rule\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Rbslider\Model\Rule\Validator
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Validator
     */
    private $model;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->model = $objectManager->getObject(
            Validator::class,
            [
                'request' => $this->requestMock
            ]
        );
    }

    /**
     * Testing of canShow method for category page
     *
     * @param string $categoryIds
     * @param bool $expected
     * @dataProvider canShowOnCategoryPageDataProvider
     */
    public function testCanShowOnCategoryPage($categoryIds, $expected)
    {
        $currentCategoryId = 1;

        $bannerMock = $this->getMockBuilder(BannerInterface::class)->getMock();
        $bannerMock->expects($this->atLeastOnce())
            ->method('getCategoryIds')
            ->willReturn($categoryIds);
        $bannerMock->expects($this->once())
            ->method('getPageType')
            ->willReturn(PageType::CATEGORY_PAGE);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($currentCategoryId);

        $this->assertEquals($expected, $this->model->canShow($bannerMock));
    }

    /**
     * Data provider for testCanShowOnCategoryPage method
     *
     * @return array
     */
    public function canShowOnCategoryPageDataProvider()
    {
        return [
            ['', true],
            ['2,3,4', false],
        ];
    }

    /**
     * Testing of canShow method for product page
     *
     * @param string $categoryIds
     * @param bool $expected
     * @dataProvider canShowOnProductPageDataProvider
     */
    public function testCanShowOnProductPage($matchingProductIds, $expected)
    {
        $currentProductId = 1;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($currentProductId);

        $productRuleMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMatchingProductIds', 'getConditions'])
            ->getMock();
        $productRuleMock->expects($this->once())
            ->method('getConditions')
            ->willReturn([]);
        $productRuleMock->expects($this->once())
            ->method('getMatchingProductIds')
            ->willReturn($matchingProductIds);

        $bannerMock = $this->getMockBuilder(Banner::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProductRule', 'getPageType'])
            ->getMock();
        $bannerMock->expects($this->exactly(2))
            ->method('getProductRule')
            ->willReturn($productRuleMock);
        $bannerMock->expects($this->once())
            ->method('getPageType')
            ->willReturn(PageType::PRODUCT_PAGE);

        $this->assertEquals($expected, $this->model->canShow($bannerMock));
    }

    /**
     * Data provider for testCanShowOnProductPage method
     *
     * @return array
     */
    public function canShowOnProductPageDataProvider()
    {
        return [
            [[1, 2, 3], true],
            [[2, 3, 4], false],
        ];
    }
}
