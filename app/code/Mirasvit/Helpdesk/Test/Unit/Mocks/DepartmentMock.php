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



namespace Mirasvit\Helpdesk\Test\Unit\Mocks;

use Mirasvit\Helpdesk\Test\Unit\Mocks\Magento\UserMock;

class DepartmentMock
{
    /**
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param array                       $data
     * @param bool|false                  $users
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public static function create(\PHPUnit_Framework_TestCase $testCase, $data = [], $users = false)
    {
        $mock = $testCase->getMock('\Mirasvit\Helpdesk\Model\Department', ['getUsers', 'getName'], [], '', false);
        $defaultData = [
                'id' => 1,
                'name' => 'Department 1',
                'is_active' => true,
            ];
        $data = array_merge($defaultData, $data);
        $mock->setData($data);
        if (!$users) {
            $users = [
                UserMock::create(
                    $testCase,
                    [
                        'id' => 1,
                        'firstname' => 'John',
                        'lastname' => 'Doe',
                    ]
                ),
                    UserMock::create(
                        $testCase,
                        [
                        'id' => 2,
                        'firstname' => 'Bill',
                        'lastname' => 'White',
                        ]
                    ),
            ];
        }
        $mock->expects($testCase->any())
            ->method('getUsers')
            ->willReturn($users);

        $mock->expects($testCase->any())
            ->method('getName')
            ->willReturn($data['name']);

        return $mock;
    }
}
