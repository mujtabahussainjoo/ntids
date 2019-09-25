<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Totals\Model;

use Fooman\Totals\Model\ResourceModel\OrderTotal\CollectionFactory;

class OrderTotalManagement
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param  string $typeId
     * @param  int    $orderId
     *
     * @return \Fooman\Totals\Api\Data\OrderTotalInterface[]
     */
    public function getByTypeIdAndOrderId($typeId, $orderId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\OrderTotal\Collection $collection */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('type_id', $typeId)
            ->addFieldToFilter('order_id', (string) $orderId);

        return $collection->getItems();
    }

    /**
     * @param  int $orderId
     *
     * @return \Fooman\Totals\Api\Data\OrderTotalInterface[]
     */
    public function getByOrderId($orderId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\OrderTotal\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('order_id', (string) $orderId);

        return $collection->getItems();
    }
    
    /**
     * @param  string $code
     * @param  int    $orderId
     *
     * @return \Fooman\Totals\Api\Data\OrderTotalInterface[]
     */
    public function getByCodeAndOrderId($code, $orderId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\OrderTotal\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('code', $code);
        $collection->addFieldToFilter('order_id', (string) $orderId);

        return $collection->getItems();
    }
}
