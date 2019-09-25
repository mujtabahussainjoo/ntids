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

class Group implements \Fooman\Totals\Api\Data\TotalGroupInterface
{

    public function __construct(
        \Fooman\Totals\Model\OrderTotalFactory $orderTotalFactory,
        array $data = []
    ) {
        if (isset($data['items'])) {
            foreach ($data['items'] as $itemData) {
                $item = $orderTotalFactory->create(['data' => $itemData]);
                $this->addItem($item);
            }
        }
    }

    /**
     * @var array
     */
    private $items = [];

    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param \Fooman\Totals\Api\Data\TotalInterface $item
     * @return void
     */
    public function addItem(\Fooman\Totals\Api\Data\TotalInterface $item)
    {
        $this->items[$item->getTypeId()] = $item;
    }

    /**
     * @param string $typeId
     *
     * @return bool|\Fooman\Totals\Api\Data\TotalInterface
     */
    public function getByTypeId($typeId)
    {
        if (isset($this->items[$typeId])) {
            return $this->items[$typeId];
        }
        return false;
    }

    /**
     * @param string $typeId
     *
     * @return void
     */
    public function removeByTypeId($typeId)
    {
        unset($this->items[$typeId]);
    }
}
