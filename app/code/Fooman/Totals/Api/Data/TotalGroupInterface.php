<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Totals\Api\Data;

/**
 * Interface TotalGroupInterface
 * @api
 */
interface TotalGroupInterface
{
    /**
     * Get items
     *
     * @return \Fooman\Totals\Api\Data\TotalInterface[]
     */
    public function getItems();

    /**
     * @param array $items
     *
     * @return void
     */
    public function setItems(array $items);

    /**
     * @param \Fooman\Totals\Api\Data\TotalInterface $item
     *
     * @return mixed
     */
    public function addItem(\Fooman\Totals\Api\Data\TotalInterface $item);

    /**
     * @param string $typeId
     *
     * @return bool | \Fooman\Totals\Api\Data\TotalInterface
     */
    public function getByTypeId($typeId);

    /**
     * @param string $typeId
     *
     * @return void
     */
    public function removeByTypeId($typeId);
}
