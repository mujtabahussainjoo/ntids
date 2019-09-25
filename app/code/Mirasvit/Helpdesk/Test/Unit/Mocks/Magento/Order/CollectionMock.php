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



namespace Mirasvit\Helpdesk\Test\Unit\Mocks\Magento\Order;

use Mirasvit\Helpdesk\Test\Unit\Mocks\Magento\OrderMock;

class CollectionMock extends \Magento\Sales\Model\ResourceModel\Order\Collection
{
    use \Mirasvit\Helpdesk\Test\Unit\Mocks\Lib\CollectionTrait;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $testCase
     * @param bool|false                                       $items
     */
    public function __construct($testCase, $items = false)
    {
        if (!$items) {
            $items = [
                OrderMock::create(
                    $testCase,
                    [
                        'id' => 1,
                        'customer_email' => 'john1@example.com',
                        'increment_id' => '000000001',
                        'created_at' => '2015-09-13 00:00:00',
                        'status' => 'processing',
                        'grand_total' => '100.54',

                    ]
                ),
                OrderMock::create(
                    $testCase,
                    [
                        'id' => 2,
                        'customer_email' => 'john2@example.com',
                        'increment_id' => '000000002',
                        'created_at' => '2015-09-14 00:00:00',
                        'status' => 'complete',
                        'grand_total' => '1340.54',

                    ]
                ),
                OrderMock::create(
                    $testCase,
                    [
                        'id' => 3,
                        'customer_email' => 'john3@example.com',
                        'increment_id' => '000000003',
                        'created_at' => '2015-09-15 00:00:00',
                        'status' => 'pending',
                        'grand_total' => '1.22',
                    ]
                ),
                OrderMock::create(
                    $testCase,
                    [
                        'id' => 4,
                        'customer_email' => 'john2@example.com',
                        'increment_id' => '000000004',
                        'created_at' => '2015-09-16 00:00:00',
                        'status' => 'complete',
                        'grand_total' => '34.43',
                    ]
                ),
            ];
        }
        $this->items = $items;
    }
}
