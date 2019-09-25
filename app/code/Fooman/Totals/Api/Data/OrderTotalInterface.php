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
 * Interface OrderTotalInterface
 * @api
 */
interface OrderTotalInterface extends TotalInterface
{
    /**
     * Get amount invoiced
     *
     * @return float
     */
    public function getAmountInvoiced();

    /**
     * Set amount invoiced
     *
     * @param float $amountInvoiced
     * @return void
     */
    public function setAmountInvoiced($amountInvoiced);

    /**
     * Get base amount invoiced
     *
     * @return float
     */
    public function getBaseAmountInvoiced();

    /**
     * Set base amount invoiced
     *
     * @param float $baseAmountInvoiced
     * @return void
     */
    public function setBaseAmountInvoiced($baseAmountInvoiced);

    /**
     * Get amount refunded
     *
     * @return float
     */
    public function getAmountRefunded();

    /**
     * Set amount refunded
     *
     * @param float $amountRefunded
     * @return void
     */
    public function setAmountRefunded($amountRefunded);
    
    /**
     * Get base amount refunded
     *
     * @return float
     */
    public function getBaseAmountRefunded();

    /**
     * Set base amount refunded
     *
     * @param float $baseAmountRefunded
     * @return void
     */
    public function setBaseAmountRefunded($baseAmountRefunded);
}
