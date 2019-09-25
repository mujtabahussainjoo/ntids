<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Model\Converter;

use Aheadworks\Rbslider\Model\Converter\Condition as ConditionConverter;
use Aheadworks\Rbslider\Api\Data\ConditionInterfaceFactory;
use Aheadworks\Rbslider\Api\Data\ConditionInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Rbslider\Model\Converter\Condition
 */
class ConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConditionConverter
     */
    private $model;

    /**
     * @var ConditionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->conditionFactoryMock = $this->getMockBuilder(ConditionInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->model = $objectManager->getObject(
            ConditionConverter::class,
            ['conditionFactory' => $this->conditionFactoryMock]
        );
    }

    /**
     * Testing of arrayToDataModel method
     *
     * @param array $conditions
     * @dataProvider getConditionDataProvider
     */
    public function testArrayToDataModel($conditions)
    {
        $conditionMock = $this->getMockBuilder(ConditionInterface::class)->getMock();
        $conditionChildMock = $this->getMockBuilder(ConditionInterface::class)->getMock();
        $childCondition = $conditions['conditions'][0];

        $this->conditionFactoryMock
            ->expects($this->at(0))
            ->method('create')
            ->willReturn($conditionMock);
        $this->conditionFactoryMock
            ->expects($this->at(1))
            ->method('create')
            ->willReturn($conditionChildMock);

        $conditionMock->expects($this->once())
            ->method('setType')
            ->with($conditions['type'])
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setAggregator')
            ->with($conditions['aggregator'])
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setAttribute')
            ->with($conditions['attribute'])
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setOperator')
            ->with($conditions['operator'])
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setValueType')
            ->with($conditions['value_type'])
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setConditions')
            ->with([$conditionChildMock])
            ->willReturnSelf();

        $conditionChildMock->expects($this->once())
            ->method('setType')
            ->with($childCondition['type'])
            ->willReturnSelf();
        $conditionChildMock->expects($this->once())
            ->method('setAttribute')
            ->with($childCondition['attribute'])
            ->willReturnSelf();
        $conditionChildMock->expects($this->once())
            ->method('setAggregator')
            ->with($childCondition['aggregator'])
            ->willReturnSelf();
        $conditionChildMock->expects($this->once())
            ->method('setOperator')
            ->with($childCondition['operator'])
            ->willReturnSelf();
        $conditionChildMock->expects($this->once())
            ->method('setValueType')
            ->with($childCondition['value_type'])
            ->willReturnSelf();

        $this->assertEquals($conditionMock, $this->model->arrayToDataModel($conditions));
    }

    /**
     * Testing of dataModelToArray method
     *
     * @param array $conditions
     * @dataProvider getConditionDataProvider
     */
    public function testDataModelToArray($conditions)
    {
        $dataModelMock = $this->getMockBuilder(ConditionInterface::class)->getMock();
        $childConditionMock = $this->getMockBuilder(ConditionInterface::class)->getMock();
        $childCondition = $conditions['conditions'][0];

        $dataModelMock->expects($this->once())
            ->method('getType')
            ->willReturn($conditions['type']);
        $dataModelMock->expects($this->once())
            ->method('getAttribute')
            ->willReturn($conditions['attribute']);
        $dataModelMock->expects($this->once())
            ->method('getOperator')
            ->willReturn($conditions['operator']);
        $dataModelMock->expects($this->once())
            ->method('getValue')
            ->willReturn($conditions['value']);
        $dataModelMock->expects($this->once())
            ->method('getValueType')
            ->willReturn($conditions['value_type']);
        $dataModelMock->expects($this->once())
            ->method('getAggregator')
            ->willReturn($conditions['aggregator']);
        $dataModelMock->expects($this->once())
            ->method('getConditions')
            ->willReturn([$childConditionMock]);

        $childConditionMock->expects($this->once())
            ->method('getType')
            ->willReturn($childCondition['type']);
        $childConditionMock->expects($this->once())
            ->method('getAttribute')
            ->willReturn($childCondition['attribute']);
        $childConditionMock->expects($this->once())
            ->method('getOperator')
            ->willReturn($childCondition['operator']);
        $childConditionMock->expects($this->once())
            ->method('getValue')
            ->willReturn($childCondition['value']);
        $childConditionMock->expects($this->once())
            ->method('getValueType')
            ->willReturn($childCondition['value_type']);
        $childConditionMock->expects($this->once())
            ->method('getAggregator')
            ->willReturn($childCondition['aggregator']);
        $childConditionMock->expects($this->once())
            ->method('getConditions')
            ->willReturn([]);

        $this->assertEquals($conditions, $this->model->dataModelToArray($dataModelMock));
    }

    /**
     * Data provider for tests
     *
     * @return array
     */
    public function getConditionDataProvider()
    {
        return [
            [
                [
                    'type' => 'type',
                    'attribute' => 'attribute',
                    'operator' => 'operator',
                    'value' => 'value',
                    'value_type' => 'value_type',
                    'aggregator' => 'aggregator',
                    'conditions' => [
                        [
                            'type' => 'child_type',
                            'attribute' => 'child_attribute',
                            'operator' => 'child_operator',
                            'value' => 'child_value',
                            'value_type' => null,
                            'aggregator' => 'aggregator'
                        ]
                    ]
                ]
            ]
        ];
    }
}
