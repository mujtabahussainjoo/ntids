<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Controller\CountClicks;

use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Aheadworks\Rbslider\Api\Data\StatisticInterface;
use Aheadworks\Rbslider\Api\Data\SlideSearchResultsInterface;
use Aheadworks\Rbslider\Api\Data\StatisticSearchResultsInterface;
use Aheadworks\Rbslider\Controller\CountClicks\Redirect;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Rbslider\Api\SlideRepositoryInterface;
use Aheadworks\Rbslider\Api\StatisticRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Rbslider\Model\CustomerStatistic\Manager as CustomerStatisticManager;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;

/**
 * Test for \Aheadworks\Rbslider\Controller\CountClicks\Redirect
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RedirectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Redirect
     */
    private $controller;

    /**
     * @var SlideRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $slideRepositoryMock;

    /**
     * @var StatisticRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var CustomerStatisticManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerStatisticManagerMock;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

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

        $this->slideRepositoryMock = $this->getMockForAbstractClass(SlideRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['create', 'addFilter'])
            ->getMock();
        $this->statisticRepositoryMock = $this->getMockForAbstractClass(StatisticRepositoryInterface::class);
        $this->customerStatisticManagerMock = $this->getMockBuilder(CustomerStatisticManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['isSetSlideAction', 'addSlideAction', 'save'])
            ->getMock();
        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
                'request' => $this->requestMock
            ]
        );

        $this->controller = $objectManager->getObject(
            Redirect::class,
            [
                'context' => $contextMock,
                'slideRepository' => $this->slideRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'statisticRepository' => $this->statisticRepositoryMock,
                'customerStatisticManager' => $this->customerStatisticManagerMock
            ]
        );
    }

    /**
     * Testing of execute method, redirect to the slide URL
     */
    public function testExecuteRedirectedToCorrectUrl()
    {
        $slideId = 1;
        $bannerId = 1;
        $slideBannerId = 1;
        $slideUrl = 'https://ecommerce.aheadworks.com/';

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['slide_id', null, $slideId],
                    ['banner_id', null, $bannerId]
                ]
            );
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $statMock = $this->getMockForAbstractClass(StatisticInterface::class);
        $statMock->expects($this->once())
            ->method('getSlideBannerId')
            ->willReturn($slideBannerId);
        $statSearchResultsMock = $this->getMockForAbstractClass(StatisticSearchResultsInterface::class);
        $statSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$statMock]);
        $this->statisticRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($statSearchResultsMock);

        $this->customerStatisticManagerMock->expects($this->once())
            ->method('isSetSlideAction')
            ->with('slide_click_' . $slideBannerId)
            ->willReturn(false);

        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $slideMock->expects($this->exactly(2))
            ->method('getUrl')
            ->willReturn($slideUrl);
        $this->slideRepositoryMock->expects($this->once())
            ->method('get')
            ->with($slideId)
            ->willReturn($slideMock);

        $resultRedirectMock = $this->getMockBuilder(ResultRedirect::class)
            ->disableOriginalConstructor()
            ->setMethods(['setUrl'])
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setUrl')
            ->with($slideUrl)
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }

    /**
     *  Testing of execute method, redirected to the base url
     */
    public function testExecuteRedirectedToBaseUrl()
    {
        $resultRedirectMock = $this->getMockBuilder(ResultRedirect::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setRefererOrBaseUrl')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }
}
