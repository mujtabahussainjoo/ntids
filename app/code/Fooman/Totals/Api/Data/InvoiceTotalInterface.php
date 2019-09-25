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
 * Interface InvoiceTotalInterface
 * @api
 */
interface InvoiceTotalInterface extends TotalInterface
{

    /**
     * @return int|null
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return void
     */
    public function setOrderId($orderId);

    /**
     * @return int|null
     */
    public function getInvoiceId();

    /**
     * @param int $invoiceId
     * @return void
     */
    public function setInvoiceId($invoiceId);
}
