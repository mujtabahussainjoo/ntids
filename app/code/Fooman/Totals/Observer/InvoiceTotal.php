<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Totals\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class InvoiceTotal implements ObserverInterface
{
    /**
     * @var \Fooman\Totals\Model\InvoiceTotalFactory
     */
    private $invoiceTotalFactory;

    /**
     * @var \Fooman\Totals\Model\InvoiceTotalManagement
     */
    private $invoiceTotalManagement;

    /**
     * @var \Fooman\Totals\Model\OrderTotalManagement
     */
    private $orderTotalManagement;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    private $transactionFactory;

    /**
     * @param \Fooman\Totals\Model\InvoiceTotalFactory    $invoiceTotalFactory
     * @param \Fooman\Totals\Model\InvoiceTotalManagement $invoiceTotalManagement
     * @param \Fooman\Totals\Model\OrderTotalManagement   $orderTotalManagement
     * @param \Magento\Framework\DB\TransactionFactory    $transactionFactory
     */
    public function __construct(
        \Fooman\Totals\Model\InvoiceTotalFactory $invoiceTotalFactory,
        \Fooman\Totals\Model\InvoiceTotalManagement $invoiceTotalManagement,
        \Fooman\Totals\Model\OrderTotalManagement $orderTotalManagement,
        \Magento\Framework\DB\TransactionFactory $transactionFactory
    ) {
        $this->invoiceTotalFactory = $invoiceTotalFactory;
        $this->invoiceTotalManagement = $invoiceTotalManagement;
        $this->orderTotalManagement = $orderTotalManagement;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param EventObserver $observer
     *
     * @return void
     * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        $this->saveInvoiceTotals($observer->getInvoice());
    }

    /**
     * @param  \Magento\Sales\Api\Data\InvoiceInterface $invoice
     *
     * @throws \Exception
     */
    private function saveInvoiceTotals(\Magento\Sales\Api\Data\InvoiceInterface $invoice)
    {
        $extensionAttributes = $invoice->getExtensionAttributes();
        if (!$extensionAttributes) {
            return;
        }
        $foomanInvoiceTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$foomanInvoiceTotalGroup) {
            return;
        }

        $foomanInvoiceTotals = $foomanInvoiceTotalGroup->getItems();
        if (!empty($foomanInvoiceTotals)) {
            $transaction = $this->transactionFactory->create();
            foreach ($foomanInvoiceTotals as $foomanInvoiceTotalItem) {
                $invoiceTotals = false;
                if ($invoice->getEntityId()) {
                    $invoiceTotals = $this->invoiceTotalManagement->getByTypeAndInvoiceId(
                        $foomanInvoiceTotalItem->getTypeId(),
                        $invoice->getEntityId()
                    );
                }

                if (!empty($invoiceTotals)) {
                    $invoiceTotal = array_shift($invoiceTotals);
                } else {
                    $invoiceTotal = $this->invoiceTotalFactory->create();
                }

                $invoiceTotal->setAmount($foomanInvoiceTotalItem->getAmount());
                $invoiceTotal->setBaseAmount($foomanInvoiceTotalItem->getBaseAmount());
                $invoiceTotal->setTaxAmount($foomanInvoiceTotalItem->getTaxAmount());
                $invoiceTotal->setBaseTaxAmount($foomanInvoiceTotalItem->getBaseTaxAmount());
                $invoiceTotal->setLabel($foomanInvoiceTotalItem->getLabel());
                $invoiceTotal->setTypeId($foomanInvoiceTotalItem->getTypeId());
                $invoiceTotal->setCode($foomanInvoiceTotalItem->getCode());
                $invoiceTotal->setOrderId($invoice->getOrderId());
                $invoiceTotal->setInvoiceId($invoice->getEntityId());
                $transaction->addObject($invoiceTotal);

                //Update corresponding order total
                $orderTotal = $this->getCorrespondingOrderTotal($foomanInvoiceTotalItem, $invoice);

                // @TODO check
                if ($orderTotal) {
                    $orderTotal->setAmountInvoiced(
                        $orderTotal->getAmountInvoiced() + $foomanInvoiceTotalItem->getAmount()
                    );
                    $orderTotal->setBaseAmountInvoiced(
                        $orderTotal->getBaseAmountInvoiced() + $foomanInvoiceTotalItem->getBaseAmount()
                    );

                    $transaction->addObject($orderTotal);
                }
            }
            $transaction->save();
        }
    }

    /**
     * @param $invoiceTotal
     * @param $invoice
     *
     * @return \Fooman\Totals\Api\Data\OrderTotalInterface|mixed
     */
    public function getCorrespondingOrderTotal($invoiceTotal, $invoice)
    {
        if ($invoice->getOrderId()) {
            $orderTotals = $this->orderTotalManagement->getByTypeIdAndOrderId(
                $invoiceTotal->getTypeId(),
                $invoice->getOrderId()
            );

            if (!empty($orderTotals)) {
                return array_shift($orderTotals);
            }
        }

        $order = $invoice->getOrder();
        $extensionAttributes = $order->getExtensionAttributes();
        $orderTotalGroup = $extensionAttributes->getFoomanTotalGroup();

        return $orderTotalGroup->getByTypeId($invoiceTotal->getTypeId());
    }
}
