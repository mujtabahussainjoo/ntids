<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Controller\Adminhtml\Statistic;

use Aheadworks\Rbslider\Controller\Adminhtml\Statistic\MassReset;
use Magento\Framework\Message\ManagerInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Aheadworks\Rbslider\Api\Data\StatisticInterface;
use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Api\StatisticRepositoryInterface;
use Aheadworks\Rbslider\Model\ResourceModel\Statistic\CollectionFactory;
use Aheadworks\Rbslider\Model\ResourceModel\Statistic\Collection;
use Aheadworks\Rbslider\Model\Statistic;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Rbslider\Controller\Adminhtml\Statistic\MassReset
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassResetTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MassReset
     */
    private $controller;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterMock;

    /**
     * @var StatisticRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->statisticRepositoryMock = $this->getMockForAbstractClass(StatisticRepositoryInterface::class);
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->filterMock = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCollection'])
            ->getMock();
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock
            ]
        );

        $this->controller = $objectManager->getObject(
            MassReset::class,
            [
                'context' => $contextMock,
                'collectionFactory' => $this->collectionFactoryMock,
                'filter' => $this->filterMock,
                'statisticRepository' => $this->statisticRepositoryMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $statisticId = 1;
        $count = 1;

        $statisticModelMock = $this->getMockBuilder(Statistic::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();
        $statisticModelMock->expects($this->once())
            ->method('getId')
            ->willReturn($statisticId);
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItems'])
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$statisticModelMock]);
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);
        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);

        $statisticMock = $this->getMockForAbstractClass(StatisticInterface::class);
        $statisticMock->expects($this->once())
            ->method('setViewCount')
            ->with(0);
        $statisticMock->expects($this->once())
            ->method('setClickCount')
            ->with(0);
        $this->statisticRepositoryMock->expects($this->once())
            ->method('get')
            ->with($statisticId)
            ->willReturn($statisticMock);
        $this->statisticRepositoryMock->expects($this->once())
            ->method('save')
            ->with($statisticMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('A total of %1 record(s) have been updated', $count))
            ->willReturnSelf();

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->setMethods(['setPath'])
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }
}
