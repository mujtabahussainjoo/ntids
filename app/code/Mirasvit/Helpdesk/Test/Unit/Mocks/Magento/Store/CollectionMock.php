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

use Mirasvit\Helpdesk\Test\Unit\Mocks\Magento\StoreMock;

class CollectionMock extends \Magento\Store\Model\ResourceModel\Store\Collection
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
                StoreMock::create(
                    $testCase,
                    [
                        'store_id' => 1,
                        'code' => 'store_en',
                        'name' => 'English Store',
                        'sort_order' => '20',
                        'is_active' => true,
                    ]
                ),
                StoreMock::create(
                    $testCase,
                    [
                        'store_id' => 2,
                        'code' => 'store_de',
                        'name' => 'German Store',
                        'sort_order' => '10',
                        'is_active' => true,
                    ]
                ),
                StoreMock::create(
                    $testCase,
                    [
                        'store_id' => 3,
                        'code' => 'store_fr',
                        'name' => 'Frech Store',
                        'sort_order' => '30',
                        'is_active' => false,
                    ]
                ),
            ];
        }
        $this->items = $items;
    }
}
