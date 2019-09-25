<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Model\CustomerStatistic;

use Aheadworks\Rbslider\Model\CustomerStatistic\Manager;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test \Aheadworks\Rbslider\Model\CustomerStatistic\Manager
 */
class ManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Manager
     */
    private $model;

    /**
     * SessionManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $sessionData = [
            'slide_view_1' => time()+50000,
            'slide_view_2' => time()+50000,
            'slide_view_3' => time(),
            'slide_click_1' => time()+50000,
            'slide_click_3' => time(),
        ];

        $this->sessionManagerMock = $this->getMockBuilder(SessionManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getData', 'setData'])
            ->getMock();
        $this->sessionManagerMock->expects($this->any())
            ->method('getData')
            ->willReturn($sessionData);

        $this->model = $objectManager->getObject(
            Manager::class,
            ['sessionManager' => $this->sessionManagerMock]
        );
    }

    /**
     * Testing of getSlidesAction method
     */
    public function testGetSlidesAction()
    {
        $count = 3;
        $class = new \ReflectionClass($this->model);
        $method = $class->getMethod('getSlidesAction');
        $method->setAccessible(true);

        $this->assertCount($count, $method->invoke($this->model));
    }

    /**
     * Testing of isSetSlideAction method
     *
     * @param string $name
     * @param bool $expected
     * @dataProvider isSetSlideActionDataProvider
     */
    public function testIsSetSlideAction($name, $expected)
    {
        $this->assertEquals($expected, $this->model->isSetSlideAction($name));
    }

    /**
     * Data provider for testIsSetSlideAction method
     *
     * @return array
     */
    public function isSetSlideActionDataProvider()
    {
        return [
            ['slide_view_1', true],
            ['slide_view_2', true],
            ['slide_click_1', true],
            ['slide_view_3', false],
            ['slide_click_2', false],
            ['slide_click_3', false]
        ];
    }

    /**
     * Testing of addSlideAction method
     */
    public function testAddSlideAction()
    {
        $count = 4;
        $name = 'slide_view_5';
        $class = new \ReflectionClass($this->model);
        $method = $class->getMethod('getSlidesAction');
        $method->setAccessible(true);
        $method->invoke($this->model);

        $this->model->addSlideAction($name);
        $this->assertCount($count, $method->invoke($this->model));
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        $this->sessionManagerMock->expects($this->once())
            ->method('setData')
            ->willReturnSelf();
        $this->model->save();
    }
}
