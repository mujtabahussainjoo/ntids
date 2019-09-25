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



namespace Mirasvit\Helpdesk\Test\Unit\Mocks\Department;

use Mirasvit\Helpdesk\Test\Unit\Mocks\DepartmentMock;

class CollectionMock extends \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection
{
    use \Mirasvit\Helpdesk\Test\Unit\Mocks\Lib\CollectionTrait;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $testCase
     * @param bool|false                                                $items
     */
    public function __construct($testCase, $items = false)
    {
        $items = [
            DepartmentMock::create(
                $testCase,
                [
                    'id' => 1,
                    'name' => 'Sales',
                    'is_active' => true,
                    'store_ids' => [1, 2, 3],
                    'sort_order' => 20,
                ]
            ),
            DepartmentMock::create(
                $testCase,
                [
                    'id' => 2,
                    'name' => 'Support',
                    'is_active' => true,
                    'store_ids' => [2, 3],
                    'sort_order' => 10,
                ]
            ),
            DepartmentMock::create(
                $testCase,
                [
                    'id' => 3,
                    'name' => 'Returns',
                    'is_active' => false,
                    'store_ids' => [2, 3],
                    'sort_order' => 30,
                ]
            ),
        ];
        $this->items = $items;
    }
}
