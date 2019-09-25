<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Model\ResourceModel;

use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Aheadworks\Rbslider\Api\Data\BannerSearchResultsInterface;
use Aheadworks\Rbslider\Api\Data\ConditionInterface;
use Aheadworks\Rbslider\Model\Banner;
use Aheadworks\Rbslider\Model\ResourceModel\BannerRepository;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Rbslider\Model\BannerFactory;
use Aheadworks\Rbslider\Api\Data\BannerInterfaceFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Rbslider\Model\BannerRegistry;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Rbslider\Model\Converter\Condition as ConditionConverter;
use Aheadworks\Rbslider\Api\Data\BannerSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Rbslider\Model\ResourceModel\Banner\Collection as BannerCollection;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;

/**
 * Test for \Aheadworks\Rbslider\Model\ResourceModel\BannerRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BannerRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var BannerRepository
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var BannerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bannerFactoryMock;

    /**
     * @var BannerInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bannerDataFactoryMock;

    /**
     * @var BannerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bannerRegistryMock;

    /**
     * @var BannerSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
     * @var ConditionConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionConverterMock;

    /**
     * @var array
     */
    private $bannerData = [
        'id' => 1,
        'product_condition' => 'a:5:{s:4:"type";s:48:"Aheadworks\Rbslider\Model\Rule\Condition\Combine";'
            . 's:10:"conditions";a:1:{i:0;a:5:{s:4:"type";'
            . 's:59:"Aheadworks\Rbslider\Model\Rule\Condition\Product\Attributes";s:8:"operator";s:2:"==";'
            . 's:9:"attribute";s:12:"category_ids";s:5:"value";s:14:"20, 21, 23, 24";s:10:"value_type";N;}}'
            . 's:10:"aggregator";s:3:"all";s:5:"value";s:1:"1";s:10:"value_type";N;}',
    ];

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
            ->setMethods(['load', 'delete', 'save'])
            ->getMock();
        $this->bannerFactoryMock = $this->getMockBuilder(BannerFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->bannerDataFactoryMock = $this->getMockBuilder(BannerInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->bannerRegistryMock = $this->getMockBuilder(BannerRegistry::class)
            ->disableOriginalConstructor()
            ->setMethods(['push', 'retrieve', 'remove'])
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(BannerSearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()
            ->setMethods(['buildOutputDataArray'])
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockBuilder(JoinProcessorInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->conditionConverterMock = $this->getMockBuilder(ConditionConverter::class)
            ->disableOriginalConstructor()
            ->setMethods(['arrayToDataModel'])
            ->getMock();

        $this->model = $objectManager->getObject(
            BannerRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'bannerFactory' => $this->bannerFactoryMock,
                'bannerDataFactory' => $this->bannerDataFactoryMock,
                'bannerRegistry' => $this->bannerRegistryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'conditionConverter' => $this->conditionConverterMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->willReturn($this->bannerData);
        $bannerModelMock = $this->getMockBuilder(Banner::class)
            ->disableOriginalConstructor()
            ->setMethods(['addData', 'beforeSave', 'getProductCondition'])
            ->getMock();
        $this->bannerFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($bannerModelMock);
        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $bannerMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->bannerData['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($bannerModelMock, $this->bannerData['id']);
        $bannerModelMock->expects($this->once())
            ->method('addData')
            ->with($this->bannerData);
        $bannerModelMock->expects($this->exactly(2))
            ->method('getProductCondition')
            ->willReturn($this->bannerData['product_condition']);
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($bannerModelMock);

        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $this->conditionConverterMock->expects($this->once())
            ->method('arrayToDataModel')
            ->willReturn($conditionMock);

        $this->bannerDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($bannerMock);
        $this->bannerRegistryMock->expects($this->once())
            ->method('push')
            ->with($bannerMock);

        $this->assertSame($bannerMock, $this->model->save($bannerMock));
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $bannerMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->bannerData['id']);
        $bannerMock->expects($this->exactly(2))
            ->method('getProductCondition')
            ->willReturn($this->bannerData['product_condition']);

        $this->bannerDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($bannerMock);
        $this->bannerRegistryMock->expects($this->once())
            ->method('push')
            ->with($bannerMock);
        $this->bannerRegistryMock->expects($this->exactly(2))
            ->method('retrieve')
            ->with($this->bannerData['id'])
            ->will($this->onConsecutiveCalls(null, $bannerMock));

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($bannerMock, $this->bannerData['id']);

        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $this->conditionConverterMock->expects($this->once())
            ->method('arrayToDataModel')
            ->willReturn($conditionMock);

        $this->assertSame($bannerMock, $this->model->get($this->bannerData['id']));
    }

    /**
     * Testing of get method, that proper exception is thrown if banner not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with bannerId = 1
     */
    public function testGetOnExeption()
    {
        $bannerId = 1;
        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $bannerMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->bannerDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($bannerMock);

        $this->assertSame($bannerMock, $this->model->get($bannerId));
    }

    /**
     * Testing of getList method
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $filterName = 'Name';
        $filterValue = 'Sample Banner';
        $collectionSize = 5;
        $scCurrPage = 1;
        $scPageSize = 3;

        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class, [], '', false);
        $searchResultsMock = $this->getMockForAbstractClass(BannerSearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->getMockBuilder(BannerCollection::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $bannerModelMock = $this->getMockBuilder(Banner::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCollection', 'getProductCondition'])
            ->getMock();
        $bannerModelMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);
        $bannerModelMock->expects($this->exactly(2))
            ->method('getProductCondition')
            ->willReturn($this->bannerData['product_condition']);

        $this->bannerFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($bannerModelMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, BannerInterface::class);

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
        $filterMock->expects($this->once())
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
            ->willReturn(new \ArrayIterator([$bannerModelMock]));

        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $this->conditionConverterMock->expects($this->once())
            ->method('arrayToDataModel')
            ->willReturn($conditionMock);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$bannerModelMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $bannerMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->bannerData['id']);

        $this->bannerDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($bannerMock);
        $this->bannerRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($this->bannerData['id'])
            ->willReturn(null);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($bannerMock, $this->bannerData['id']);
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($bannerMock);
        $this->bannerRegistryMock->expects($this->once())
            ->method('remove')
            ->with($this->bannerData['id']);

        $this->assertTrue($this->model->delete($bannerMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $bannerMock = $this->getMockForAbstractClass(BannerInterface::class);
        $bannerMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->bannerData['id']);

        $this->bannerDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($bannerMock);
        $this->bannerRegistryMock->expects($this->once())
            ->method('retrieve')
            ->with($this->bannerData['id'])
            ->willReturn(null);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($bannerMock, $this->bannerData['id']);
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($bannerMock);
        $this->bannerRegistryMock->expects($this->once())
            ->method('remove')
            ->with($this->bannerData['id']);

        $this->assertTrue($this->model->deleteById($this->bannerData['id']));
    }
}
