<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Test\Unit\Mocks\Magento\Store;

class CollectionFactoryMock
{
    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param bool|false                  $object
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public static function create(\PHPUnit_Framework_TestCase $testCase, $object = false)
    {
        if (!$object) {
            $object = new CollectionMock($testCase);
        }
        $mock = $testCase->getMock(
            '\Magento\Store\Model\ResourceModel\Store\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $mock->expects($testCase->any())->method('create')
            ->will($testCase->returnValue($object));

        return $mock;
    }
}
