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

class QuoteAddressGroup implements \Fooman\Totals\Api\Data\QuoteAddressTotalGroupInterface
{

    public function __toArray()
    {
        $retArr = [];
        /** @var \Fooman\Totals\Api\Data\TotalInterface $item */
        foreach ($this->items as $item) {
            $retArr['items'][] = [
                'code' => $item->getCode(),
                'type_id' => $item->getTypeId(),
                'label' => $item->getLabel(),
                'amount' => $item->getAmount(),
                'base_amount' => $item->getBaseAmount(),
                'tax_amount' => $item->getTaxAmount(),
                'base_tax_amount' => $item->getBaseTaxAmount(),
            ];
        }
        return $retArr;
    }

    /**
     * @var array
     */
    private $items = [];

    /**
     * @param QuoteAddressTotalFactory $quoteAddressTotalFactory
     * @param array                    $data
     */
    public function __construct(
        QuoteAddressTotalFactory $quoteAddressTotalFactory,
        array $data = []
    ) {
        if (isset($data['items'])) {
            foreach ($data['items'] as $itemData) {
                $item = $quoteAddressTotalFactory->create(['data' => $itemData]);
                $this->addItem($item);
            }
        }
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     *
     * @return void
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param \Fooman\Totals\Api\Data\TotalInterface $item
     *
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
