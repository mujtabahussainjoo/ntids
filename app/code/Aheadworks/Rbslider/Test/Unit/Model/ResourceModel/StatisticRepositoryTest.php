<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Model\ResourceModel;

use Aheadworks\Rbslider\Model\ResourceModel\StatisticRepository;
use Aheadworks\Rbslider\Model\StatisticFactory;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Rbslider\Api\Data\StatisticInterface;
use Aheadworks\Rbslider\Api\Data\StatisticInterfaceFactory;
use Aheadworks\Rbslider\Api\Data\StatisticSearchResultsInterface;
use Aheadworks\Rbslider\Api\Data\StatisticSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Rbslider\Model\ResourceModel\Statistic\Collection as StatisticCollection;
use Aheadworks\Rbslider\Model\Statistic;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;

/**
 * Test \Aheadworks\Rbslider\Model\ResourceModel\StatisticRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StatisticRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var StatisticRepository
     */
    private $model;

    /**
     * @var StatisticFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticFactoryMock;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var StatisticInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticDataFactoryMock;

    /**
     * @var StatisticSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
        $this->statisticFactoryMock = $this->getMockBuilder(StatisticFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['load', 'delete', 'save'])
            ->getMock();
        $this->statisticDataFactoryMock = $this->getMockBuilder(StatisticInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(StatisticSearchResultsInterfaceFactory::class)
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
            StatisticRepository::class,
            [
                'statisticDataFactory' => $this->statisticDataFactoryMock,
                'entityManager' => $this->entityManagerMock,
                'statisticFactory' => $this->statisticFactoryMock,
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
        $statisticMock = $this->getMockForAbstractClass(StatisticInterface::class);
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($statisticMock)
            ->willReturn($statisticMock);

        $this->assertSame($statisticMock, $this->model->save($statisticMock));
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $statisticId = 1;

        $statisticMock = $this->getMockForAbstractClass(StatisticInterface::class);
        $statisticMock->expects($this->once())
            ->method('getId')
            ->willReturn($statisticId);
        $this->statisticDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statisticMock);

        $this->assertSame($statisticMock, $this->model->get($statisticId));
    }

    /**
     * Testing of get method, that proper exception is thrown if statistic not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with statisticId = 1
     */
    public function testGetException()
    {
        $statisticId = 1;

        $statisticMock = $this->getMockForAbstractClass(StatisticInterface::class);
        $statisticMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->statisticDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statisticMock);

        $this->model->get($statisticId);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $statisticData = [
            'id' => 1
        ];
        $filterName = 'Id';
        $filterValue = '5';
        $collectionSize = 5;
        $scCurrPage = 1;
        $scPageSize = 3;

        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class, [], '', false);
        $searchResultsMock = $this->getMockForAbstractClass(StatisticSearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->getMockBuilder(StatisticCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $statisticModelMock = $this->getMockBuilder(Statistic::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCollection', 'getData'])
            ->getMock();
        $statisticModelMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);
        $statisticModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($statisticData);
        $this->statisticFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statisticModelMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, StatisticInterface::class);

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
        $filterMock->expects($this->exactly(3))
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
            ->willReturn(new \ArrayIterator([$statisticModelMock]));

        $statisticMock = $this->getMockForAbstractClass(StatisticInterface::class);
        $this->statisticDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statisticMock);
        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$statisticMock])
            ->willReturnSelf();
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($statisticMock, $statisticData, StatisticInterface::class)
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $statisticId = 1;

        $statisticMock = $this->getMockForAbstractClass(StatisticInterface::class);
        $statisticMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($statisticId);
        $this->statisticDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statisticMock);
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($statisticMock);

        $this->assertTrue($this->model->delete($statisticMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $statisticId = 1;

        $statisticMock = $this->getMockForAbstractClass(StatisticInterface::class);
        $statisticMock->expects($this->once())
            ->method('getId')
            ->willReturn($statisticId);
        $this->statisticDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statisticMock);
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($statisticMock);

        $this->assertTrue($this->model->deleteById($statisticMock));
    }
}
