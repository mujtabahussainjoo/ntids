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

use Fooman\Totals\Model\ResourceModel\InvoiceTotal as ResourceInvoiceTotal;

class InvoiceTotal extends OrderTotal implements \Fooman\Totals\Api\Data\InvoiceTotalInterface
{

    const KEY_INVOICE_ID = 'invoice_id';
    const KEY_ORDER_ID = 'order_id';

    protected function _construct()
    {
        $this->_init(ResourceInvoiceTotal::class);
    }

    /**
     * Get order id
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::KEY_ORDER_ID);
    }

    /**
     * Set order id
     *
     * @param int $orderId
     *
     * @return void
     */
    public function setOrderId($orderId)
    {
        $this->setData(self::KEY_ORDER_ID, $orderId);
    }

    /**
     * Get invoice id
     *
     * @return int|null
     */
    public function getInvoiceId()
    {
        return $this->getData(self::KEY_INVOICE_ID);
    }

    /**
     * Set invoice id
     *
     * @param int $invoiceId
     *
     * @return void
     */
    public function setInvoiceId($invoiceId)
    {
        $this->setData(self::KEY_INVOICE_ID, $invoiceId);
    }
}
