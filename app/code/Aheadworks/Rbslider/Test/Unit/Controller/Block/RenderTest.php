<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Controller\Block;

use Aheadworks\Rbslider\Block\Banner as BlockBanner;
use Aheadworks\Rbslider\Block\Widget\Banner as WidgetBanner;
use Aheadworks\Rbslider\Controller\Block\Render;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\View;
use Magento\Framework\View\Layout;

/**
 * Test for \Aheadworks\Rbslider\Controller\Block\Render
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RenderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Render
     */
    private $controller;

    /**
     * @var InlineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $translateInlineMock;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseMock;

    /**
     * @var View|\PHPUnit_Framework_MockObject_MockObject
     */
    private $viewMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->translateInlineMock = $this->getMockForAbstractClass(InlineInterface::class);
        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['isAjax']
        );
        $this->responseMock = $this->getMockForAbstractClass(
            ResponseInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['appendBody']
        );
        $this->viewMock = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->setMethods(['loadLayout', 'getLayout'])
            ->getMock();
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
                'request' => $this->requestMock,
                'response' => $this->responseMock,
                'view' => $this->viewMock
            ]
        );

        $this->controller = $objectManager->getObject(
            Render::class,
            [
                'context' => $contextMock,
                'translateInline' => $this->translateInlineMock
            ]
        );
    }

    /**
     * Testing of execute method, if is not ajax request
     */
    public function testExecuteIsNotAjax()
    {
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(false);
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

    /**
     * Testing of execute method for Banner block, if is ajax request
     */
    public function testExecuteBlockIsAjax()
    {
        $blocks = ['block_1'];
        $expected = ['block_1' => 'html content'];

        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(true);
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['blocks', null, json_encode($blocks)]
                ]
            );

        $blockInstanceMock = $this->getMockBuilder(BlockBanner::class)
            ->disableOriginalConstructor()
            ->setMethods(['toHtml', 'setNameInLayout'])
            ->getMock();
        $blockInstanceMock->expects($this->once())
            ->method('setNameInLayout')
            ->with($blocks[0]);
        $blockInstanceMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($expected['block_1']);
        $layoutMock = $this->getMockBuilder(Layout::class)
            ->disableOriginalConstructor()
            ->setMethods(['createBlock'])
            ->getMock();
        $layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(BlockBanner::class)
            ->willReturn($blockInstanceMock);
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($layoutMock);

        $this->translateInlineMock->expects($this->once())
            ->method('processResponseBody')
            ->with($expected)
            ->willReturnSelf();
        $this->responseMock->expects($this->once())
            ->method('appendBody')
            ->with(json_encode($expected));

        $this->controller->execute();
    }

    /**
     * Testing of execute method for Banner Widget block, if is ajax request
     */
    public function testExecuteWidgetIsAjax()
    {
        $bannerId = 1;
        $blockName = WidgetBanner::WIDGET_NAME_PREFIX . $bannerId;
        $blocks = [$blockName];
        $expected = [$blockName => 'html content'];

        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(true);
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['blocks', null, json_encode($blocks)]
                ]
            );

        $blockInstanceMock = $this->getMockBuilder(WidgetBanner::class)
            ->disableOriginalConstructor()
            ->setMethods(['toHtml'])
            ->getMock();
        $blockInstanceMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($expected[$blockName]);
        $layoutMock = $this->getMockBuilder(Layout::class)
            ->disableOriginalConstructor()
            ->setMethods(['createBlock'])
            ->getMock();
        $layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(
                WidgetBanner::class,
                '',
                ['data' => ['banner_id' => $bannerId]]
            )->willReturn($blockInstanceMock);
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($layoutMock);

        $this->translateInlineMock->expects($this->once())
            ->method('processResponseBody')
            ->with($expected)
            ->willReturnSelf();
        $this->responseMock->expects($this->once())
            ->method('appendBody')
            ->with(json_encode($expected));

        $this->controller->execute();
    }
}
