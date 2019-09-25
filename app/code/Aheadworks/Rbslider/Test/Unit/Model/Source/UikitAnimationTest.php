<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Test\Unit\Model\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Rbslider\Model\Source\UikitAnimation;
use Aheadworks\Rbslider\Model\Source\AnimationEffect;

/**
 * Test \Aheadworks\Rbslider\Model\Source\UikitAnimation
 */
class UikitAnimationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var UikitAnimation|\PHPUnit_Framework_MockObject_MockObject
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            UikitAnimation::class,
            []
        );
    }

    /**
     * Testing of getAnimationEffectByKey method
     *
     * @param int $key
     * @param string $expected
     * @dataProvider getAnimationEffectByKeyDataProvider
     */
    public function testGetAnimationEffectByKey($key, $expected)
    {
        $this->assertEquals($expected, $this->model->getAnimationEffectByKey($key));
    }

    /**
     * Data provider for testGetAnimationEffectByKey method
     *
     * @return array
     */
    public function getAnimationEffectByKeyDataProvider()
    {
        return [
            [AnimationEffect::SLIDE, 'scroll'],
            [AnimationEffect::FADE_OUT_IN, 'fade'],
            [AnimationEffect::SCALE, 'scale']
        ];
    }
}
