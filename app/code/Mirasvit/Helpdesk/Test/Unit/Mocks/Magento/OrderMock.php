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



namespace Mirasvit\Helpdesk\Test\Unit\Mocks\Magento;

class OrderMock
{
    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param array                       $data
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public static function create(\PHPUnit_Framework_TestCase $testCase, $data = [])
    {
        $mock = $testCase->getMock(
            'Magento\Sales\Model\Order',
            ['load', 'save', 'delete', 'formatPrice'],
            [],
            '',
            false
        );
        $defaultData = [
            'order_id' => 1,
            'customer_email' => 'john@example.com',
        ];
        $data = array_merge($defaultData, $data);
        $mock->setData($data);

        $mock->method('formatPrice')
            ->willReturnCallback(function ($x) use ($testCase) {
                return '$'.$x;
            });

        return $mock;
    }
}
