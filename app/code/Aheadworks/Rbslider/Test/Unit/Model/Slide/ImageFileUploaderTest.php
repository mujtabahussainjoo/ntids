<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Model\Slide;

use Magento\Framework\UrlInterface;
use Aheadworks\Rbslider\Model\Slide\ImageFileUploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\MediaStorage\Model\File\Uploader as FileUploader;

/**
 * Test for \Aheadworks\Rbslider\Model\Slide\ImageFileUploader
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImageFileUploaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ImageFileUploader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $model;

    /**
     * @var UploaderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $uploaderFactoryMock;

    /**
     * @var Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystemMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->uploaderFactoryMock = $this->getMockBuilder(UploaderFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->filesystemMock = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDirectoryRead'])
            ->getMock();

        $this->model = $objectManager->getObject(
            ImageFileUploader::class,
            [
                'uploaderFactory' => $this->uploaderFactoryMock,
                'storeManager' => $this->storeManagerMock,
                'filesystem' => $this->filesystemMock
            ]
        );
    }

    /**
     * Testing of saveImageToMediaFolder method
     */
    public function testSaveImageToMediaFolder()
    {
        $baseMediaUrl = 'https://ecommerce.aheadworks.com/pub/media/';
        $tmpMediaPath = '/tmp/media';
        $fileName = '1.png';
        $fileSize = '123';
        $fileCode = 'img';

        $directoryReadMock = $this->getMockForAbstractClass(ReadInterface::class);
        $directoryReadMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with(ImageFileUploader::FILE_DIR)
            ->willReturn($tmpMediaPath);
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($directoryReadMock);

        $uploaderMock = $this->getMockBuilder(FileUploader::class)
            ->disableOriginalConstructor()
            ->setMethods(['setAllowRenameFiles', 'setFilesDispersion', 'setAllowedExtensions', 'save'])
            ->getMock();
        $uploaderMock->expects($this->once())
            ->method('setAllowRenameFiles')
            ->with(true);
        $uploaderMock->expects($this->once())
            ->method('setFilesDispersion')
            ->with(false);
        $uploaderMock->expects($this->once())
            ->method('setAllowedExtensions')
            ->with(['jpg', 'jpeg', 'gif', 'png']);
        $uploaderMock->expects($this->any())
            ->method('save')
            ->with($tmpMediaPath)
            ->willReturn(['file' => $fileName, 'size' => $fileSize]);

        $this->uploaderFactoryMock->expects($this->once())
            ->method('create')
            ->with(['fileId' => $fileCode])
            ->willReturn($uploaderMock);

        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBaseUrl'])
            ->getMock();
        $storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn($baseMediaUrl);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->assertEquals(
            [
                'file' => $fileName,
                'size' => $fileSize,
                'url' => $baseMediaUrl . ImageFileUploader::FILE_DIR . '/' . $fileName
            ],
            $this->model->saveImageToMediaFolder($fileCode)
        );
    }

    /**
     * Testing of getOptions method
     */
    public function testGetMediaUrl()
    {
        $baseMediaUrl = 'https://ecommerce.aheadworks.com/pub/media/';
        $fileName = '1.png';

        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBaseUrl'])
            ->getMock();
        $storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn($baseMediaUrl);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $expectedPath = $baseMediaUrl . ImageFileUploader::FILE_DIR . '/' . $fileName;
        $this->assertEquals($expectedPath, $this->model->getMediaUrl($fileName));
    }

    /**
     * Testing of getAllowedExtensions method
     */
    public function testGetAllowedExtensions()
    {
        $this->assertTrue(is_array($this->model->getAllowedExtensions()));
    }
}
