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
 * Interface QuoteAddressTotalInterface
 * @api
 */
interface QuoteAddressTotalInterface extends TotalInterface
{

    /**
     * @return int|null
     */
    public function getQuoteId();

    /**
     * @param int $quoteId
     * @return void
     */
    public function setQuoteId($quoteId);

    /**
     * @return int|null
     */
    public function getQuoteAddressId();

    /**
     * @param int $addressId
     * @return void
     */
    public function setQuoteAddressId($addressId);

    /**
     * Get base price
     *
     * @return float
     */
    public function getBasePrice();

    /**
     * Set base price
     *
     * @param float $price
     * @return void
     */
    public function setBasePrice($price);
}
