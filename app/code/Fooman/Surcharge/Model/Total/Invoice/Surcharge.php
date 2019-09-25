<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Model\Total\Invoice;

class Surcharge extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * @var \Fooman\Totals\Model\OrderTotalManagement
     */
    private $orderTotalManagement;

    /**
     * @var \Fooman\Totals\Helper\Invoice
     */
    private $invoiceHelper;

    /**
     * @var \Fooman\Totals\Model\InvoiceTotalFactory
     */
    private $invoiceTotalFactory;

    /**
     * @param \Fooman\Totals\Model\OrderTotalManagement $orderTotalManagement
     * @param \Fooman\Totals\Helper\Invoice             $invoiceHelper
     * @param \Fooman\Totals\Model\InvoiceTotalFactory  $invoiceTotalFactory
     * @param array                                     $data
     */
    public function __construct(
        \Fooman\Totals\Model\OrderTotalManagement $orderTotalManagement,
        \Fooman\Totals\Helper\Invoice $invoiceHelper,
        \Fooman\Totals\Model\InvoiceTotalFactory $invoiceTotalFactory,
        array $data = []
    ) {
        $this->orderTotalManagement = $orderTotalManagement;
        $this->invoiceHelper = $invoiceHelper;
        $this->invoiceTotalFactory = $invoiceTotalFactory;
        parent::__construct($data);
    }

    /**
     * @param  \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();

        $foomanOrderTotals = $this->getFoomanSurchargeOrderTotals($order);
        if (empty($foomanOrderTotals)) {
            return $this;
        }

        $baseInvoiceTotals = 0;
        $invoiceTotals = 0;
        foreach ($foomanOrderTotals as $foomanOrderTotal) {
            $amount = $foomanOrderTotal->getAmount() - $foomanOrderTotal->getAmountInvoiced();
            $baseAmount = $foomanOrderTotal->getBaseAmount() - $foomanOrderTotal->getBaseAmountInvoiced();

            $invoiceTotal = $this->invoiceTotalFactory->create();
            $invoiceTotal->setAmount($amount);
            $invoiceTotal->setBaseAmount($baseAmount);
            $invoiceTotal->setTaxAmount($foomanOrderTotal->getTaxAmount());
            $invoiceTotal->setBaseTaxAmount($foomanOrderTotal->getBaseTaxAmount());
            $invoiceTotal->setLabel($foomanOrderTotal->getLabel());
            $invoiceTotal->setCode($foomanOrderTotal->getCode());
            $invoiceTotal->setTypeId($foomanOrderTotal->getTypeId());
            $this->invoiceHelper->setExtensionAttributes(
                $invoice,
                $invoiceTotal
            );
            $invoiceTotals += $amount;
            $baseInvoiceTotals += $baseAmount;

            $foomanOrderTotal->setAmountInvoiced(
                $foomanOrderTotal->getAmountInvoiced() + $invoiceTotal->getAmount()
            );
            $foomanOrderTotal->setBaseAmountInvoiced(
                $foomanOrderTotal->getBaseAmountInvoiced() + $invoiceTotal->getBaseAmount()
            );
        }

        $invoice->setBaseGrandTotal(
            $invoice->getBaseGrandTotal() +
            $baseInvoiceTotals
        );

        $invoice->setGrandTotal(
            $invoice->getGrandTotal() +
            $invoiceTotals
        );

        return $this;
    }

    private function getFoomanSurchargeOrderTotals(\Magento\Sales\Model\Order $order)
    {
        if ($order->getId()) {
            return $this->orderTotalManagement
                ->getByCodeAndOrderId(\Fooman\Surcharge\Model\Surcharge::CODE, $order->getId());
        }

        $extensionAttributes = $order->getExtensionAttributes();

        if (!is_object($extensionAttributes)) {
            return [];
        }

        /** @var \Fooman\Totals\Api\Data\TotalGroupInterface $orderTotalGroup */
        $orderTotalGroup = $extensionAttributes->getFoomanTotalGroup();

        if (!is_object($orderTotalGroup)) {
            return [];
        }

        $orderTotals = [];

        foreach ($orderTotalGroup->getItems() as $orderTotalItem) {
            if ($orderTotalItem->getCode() == \Fooman\Surcharge\Model\Surcharge::CODE) {
                $orderTotals[] = $orderTotalItem;
            }
        }

        return $orderTotals;
    }
}
