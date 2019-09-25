<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Model;

use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Aheadworks\Rbslider\Model\SlideRegistry;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Rbslider\Api\Data\SlideInterfaceFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test \Aheadworks\Rbslider\Model\SlideRegistry
 */
class SlideRegistryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SlideRegistry
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var SlideInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $slideDataFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['load'])
            ->getMock();
        $this->slideDataFactoryMock = $this->getMockBuilder(SlideInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->model = $objectManager->getObject(
            SlideRegistry::class,
            [
                'entityManager' => $this->entityManagerMock,
                'slideDataFactory' => $this->slideDataFactoryMock
            ]
        );
    }

    /**
     * Testing of retrieve method
     */
    public function testRetrieve()
    {
        $slideId = 1;
        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $slideMock->expects($this->once())
            ->method('getId')
            ->willReturn($slideId);
        $this->slideDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($slideMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($slideMock, $slideId);

        $this->assertSame($slideMock, $this->model->retrieve($slideId));
    }

    /**
     * Testing of retrieve method, that proper exception is thrown if slide not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with slideId = 1
     */
    public function testRetrieveException()
    {
        $slideId = 1;
        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $slideMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->slideDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($slideMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($slideMock, $slideId);

        $this->model->retrieve($slideId);
    }

    /**
     * Testing of remove method
     */
    public function testRemove()
    {
        $slideId = 1;
        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $slideMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($slideId);
        $this->slideDataFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($slideMock);
        $this->entityManagerMock->expects($this->exactly(2))
            ->method('load')
            ->with($slideMock, $slideId);

        $slideFromReg = $this->model->retrieve($slideId);
        $this->assertEquals($slideMock, $slideFromReg);
        $this->model->remove($slideId);
        $slideFromReg = $this->model->retrieve($slideId);
        $this->assertEquals($slideMock, $slideFromReg);
    }
}
