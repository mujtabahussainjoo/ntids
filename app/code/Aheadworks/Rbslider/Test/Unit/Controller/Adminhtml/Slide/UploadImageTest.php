<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Controller\Adminhtml\Slide\UploadImage;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Json as ResultJson;
use Aheadworks\Rbslider\Model\Slide\ImageFileUploader;

/**
 * Test for \Aheadworks\Rbslider\Controller\Adminhtml\Slide\UploadImage
 */
class UploadImageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var UploadImage
     */
    private $controller;

    /**
     * @var ImageFileUploader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageFileUploaderMock;

    /**
     * @var ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->imageFileUploaderMock = $this->getMockBuilder(ImageFileUploader::class)
            ->disableOriginalConstructor()
            ->setMethods(['saveImageToMediaFolder'])
            ->getMock();
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'resultFactory' => $this->resultFactoryMock
            ]
        );

        $this->controller = $objectManager->getObject(
            UploadImage::class,
            [
                'context' => $contextMock,
                'imageFileUploader' => $this->imageFileUploaderMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $result = [
            'size' => 282567,
            'file' => '1.png',
            'url' => 'https://ecommerce.aheadworks.com/pub/media/aw_rbslider/slides/1.png'
        ];

        $this->imageFileUploaderMock->expects($this->once())
            ->method('saveImageToMediaFolder')
            ->with('img_file')
            ->willReturn($result);
        $resultJsonMock = $this->getMockBuilder(ResultJson::class)
            ->disableOriginalConstructor()
            ->setMethods(['setData'])
            ->getMock();
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with($result)
            ->willReturnSelf();
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_JSON)
            ->willReturn($resultJsonMock);

        $this->assertSame($resultJsonMock, $this->controller->execute());
    }
}
