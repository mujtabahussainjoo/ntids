<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Rbslider\Model\ResourceModel\SlideRepository;
use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Aheadworks\Rbslider\Api\Data\SlideInterfaceFactory;
use Aheadworks\Rbslider\Model\SlideRegistry;
use Aheadworks\Rbslider\Api\Data\SlideSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Rbslider\Model\SlideFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Rbslider\Api\Data\SlideSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Rbslider\Model\ResourceModel\Slide\Collection as SlideCollection;
use Aheadworks\Rbslider\Model\Slide;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;

/**
 * Test \Aheadworks\Rbslider\Model\ResourceModel\SlideRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SlideRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SlideRepository
     */
    private $model;

    /**
     * @var SlideFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $slideFactoryMock;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var SlideInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $slideDataFactoryMock;

    /**
     * @var SlideRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $slideRegistryMock;

    /**
     * @var SlideSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->slideFactoryMock = $this->getMockBuilder(SlideFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['load', 'delete', 'save'])
            ->getMock();
        $this->slideDataFactoryMock = $this->getMockBuilder(SlideInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->slideRegistryMock = $this->getMockBuilder(SlideRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(['push', 'retrieve', 'remove'])
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(SlideSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['populateWithArray'])
            ->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildOutputDataArray'])
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockBuilder(JoinProcessorInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->model = $objectManager->getObject(
            SlideRepository::class,
            [
                'slideFactory' => $this->slideFactoryMock,
                'entityManager' => $this->entityManagerMock,
                'slideDataFactory' => $this->slideDataFactoryMock,
                'slideRegistry' => $this->slideRegistryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($slideMock)
            ->willReturn($slideMock);
        $this->slideRegistryMock->expects($this->once())
            ->method('push')
            ->with($slideMock);

        $this->assertSame($slideMock, $this->model->save($slideMock));
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $slideId = 1;
        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $this->slideRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($slideId)
            ->willReturn($slideMock);

        $this->assertSame($slideMock, $this->model->get($slideId));
    }

    /**
     * Testing of getList method
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $slideData = [
            'id' => 1
        ];
        $filterName = 'Name';
        $filterValue = 'Sample Slide';
        $collectionSize = 5;
        $scCurrPage = 1;
        $scPageSize = 3;

        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class, [], '', false);
        $searchResultsMock = $this->getMockForAbstractClass(SlideSearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->getMockBuilder(SlideCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $slideModelMock = $this->getMockBuilder(Slide::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCollection', 'getData'])
            ->getMock();
        $slideModelMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);
        $slideModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($slideData);
        $this->slideFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($slideModelMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, SlideInterface::class);

        $filterGroupMock = $this->getMockBuilder(FilterGroup::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $filterMock = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterMock]);
        $filterMock->expects($this->once())
            ->method('getConditionType')
            ->willReturn(false);
        $filterMock->expects($this->exactly(5))
            ->method('getField')
            ->willReturn($filterName);
        $filterMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn($filterValue);
        $collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with([$filterName], [['eq' => $filterValue]]);
        $collectionMock
            ->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $searchCriteriaMock->expects($this->atLeastOnce())
            ->method('getSortOrders')
            ->willReturn([$sortOrderMock]);
        $sortOrderMock->expects($this->once())
            ->method('getField')
            ->willReturn($filterName);
        $collectionMock->expects($this->once())
            ->method('addOrder')
            ->with($filterName, SortOrder::SORT_ASC);
        $sortOrderMock->expects($this->once())
            ->method('getDirection')
            ->willReturn(SortOrder::SORT_ASC);
        $searchCriteriaMock->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn($scCurrPage);
        $collectionMock->expects($this->once())
            ->method('setCurPage')
            ->with($scCurrPage)
            ->willReturn($collectionMock);
        $searchCriteriaMock->expects($this->once())
            ->method('getPageSize')
            ->willReturn($scPageSize);
        $collectionMock->expects($this->once())
            ->method('setPageSize')
            ->with($scPageSize)
            ->willReturn($collectionMock);
        $collectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$slideModelMock]));

        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $this->slideDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($slideMock);
        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$slideMock])
            ->willReturnSelf();
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($slideMock, $slideData, SlideInterface::class)
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $slideId = 1;

        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $slideMock->expects($this->once())
            ->method('getId')
            ->willReturn($slideId);
        $this->slideRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($slideId)
            ->willReturn($slideMock);
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($slideMock);
        $this->slideRegistryMock->expects($this->once())
            ->method('remove')
            ->with($slideId);

        $this->assertTrue($this->model->delete($slideMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $slideId = 1;

        $slideMock = $this->getMockForAbstractClass(SlideInterface::class);
        $this->slideRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($slideId)
            ->willReturn($slideMock);
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($slideMock);
        $this->slideRegistryMock->expects($this->once())
            ->method('remove')
            ->with($slideId);

        $this->assertTrue($this->model->deleteById($slideId));
    }
}
