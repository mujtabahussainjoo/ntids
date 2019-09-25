<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Totals\Helper;

class Invoice
{
    /**
     * @var \Magento\Sales\Api\Data\InvoiceExtensionFactory
     */
    private $invoiceExtensionFactory;

    /**
     * @var \Fooman\Totals\Model\InvoiceTotalFactory
     */
    private $invoiceTotalFactory;

    /**
     * @var \Fooman\Totals\Model\GroupFactory
     */
    private $groupFactory;

    /**
     * @param \Magento\Sales\Api\Data\InvoiceExtensionFactory  $invoiceExtensionFactory
     * @param \Fooman\Totals\Model\InvoiceTotalFactory  $invoiceTotalFactory
     * @param \Fooman\Totals\Model\GroupFactory         $groupFactory
     */
    public function __construct(
        \Magento\Sales\Api\Data\InvoiceExtensionFactory $invoiceExtensionFactory,
        \Fooman\Totals\Model\InvoiceTotalFactory $invoiceTotalFactory,
        \Fooman\Totals\Model\GroupFactory $groupFactory
    ) {
        $this->invoiceExtensionFactory = $invoiceExtensionFactory;
        $this->invoiceTotalFactory = $invoiceTotalFactory;
        $this->groupFactory = $groupFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Fooman\Totals\Api\Data\InvoiceTotalInterface $total
     */
    public function setExtensionAttributes(
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Fooman\Totals\Api\Data\InvoiceTotalInterface $total
    ) {
        $extensionAttributes = $invoice->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->invoiceExtensionFactory->create();
        }

        /** @var \Fooman\Totals\Api\Data\TotalGroupInterface $invoiceTotalGroup */
        $invoiceTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$invoiceTotalGroup) {
            $invoiceTotalGroup = $this->groupFactory->create();
        }

        /** @var \Fooman\Totals\Api\Data\InvoiceTotalInterface $invoiceTotal */
        $invoiceTotal = $invoiceTotalGroup->getByTypeId($total->getTypeId());

        if (!$invoiceTotal) {
            $invoiceTotal = $this->invoiceTotalFactory->create();
        }
        
        $invoiceTotal->setAmount($total->getAmount());
        $invoiceTotal->setBaseAmount($total->getBaseAmount());
        $invoiceTotal->setTaxAmount($total->getTaxAmount());
        $invoiceTotal->setBaseTaxAmount($total->getBaseTaxAmount());
        $invoiceTotal->setLabel($total->getLabel());
        $invoiceTotal->setTypeId($total->getTypeId());
        $invoiceTotal->setCode($total->getCode());

        $invoiceTotalGroup->addItem($invoiceTotal);
        $extensionAttributes->setFoomanTotalGroup($invoiceTotalGroup);
        $invoice->setExtensionAttributes($extensionAttributes);
    }
}
