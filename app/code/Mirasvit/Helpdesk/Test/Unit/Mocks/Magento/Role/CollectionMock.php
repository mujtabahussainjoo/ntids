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



namespace Mirasvit\Helpdesk\Test\Unit\Mocks\Magento\Role;

use Mirasvit\Helpdesk\Test\Unit\Mocks\Magento\RoleMock;

class CollectionMock extends \Magento\Authorization\Model\ResourceModel\Role\Collection
{
    use \Mirasvit\Helpdesk\Test\Unit\Mocks\Lib\CollectionTrait;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $testCase
     * @param bool|false                                                $items
     */
    public function __construct($testCase, $items = false)
    {
        if (!$items) {
            $items = [
                RoleMock::create(
                    $testCase,
                    [
                        'role_id' => 1,
                        'role_type' => 'G',
                        'role_name' => 'Administrators',
                    ]
                ),
                RoleMock::create(
                    $testCase,
                    [
                        'role_id' => 2,
                        'role_type' => 'G',
                        'role_name' => 'Managers',
                    ]
                ),
                RoleMock::create(
                    $testCase,
                    [
                        'role_id' => 3,
                        'role_type' => 'U',
                        'role_name' => 'admin',
                    ]
                ),
            ];
        }
        $this->items = $items;
    }
}
