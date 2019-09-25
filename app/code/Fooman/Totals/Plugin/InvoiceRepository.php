<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Totals\Plugin;

class InvoiceRepository
{
    /**
     * @var \Fooman\Totals\Model\InvoiceTotalManagement
     */
    private $invoiceTotalManagement;

    /**
     * @var \Magento\Sales\Api\Data\InvoiceExtensionFactory
     */
    private $invoiceExtensionFactory;

    /**
     * @var \Fooman\Totals\Model\GroupFactory
     */
    private $invoiceTotalGroupFactory;

    /**
     * @param \Fooman\Totals\Model\InvoiceTotalManagement     $invoiceTotalManagement
     * @param \Magento\Sales\Api\Data\InvoiceExtensionFactory $invoiceExtensionFactory
     * @param \Fooman\Totals\Model\GroupFactory               $invoiceTotalGroupFactory
     */
    public function __construct(
        \Fooman\Totals\Model\InvoiceTotalManagement $invoiceTotalManagement,
        \Magento\Sales\Api\Data\InvoiceExtensionFactory $invoiceExtensionFactory,
        \Fooman\Totals\Model\GroupFactory $invoiceTotalGroupFactory
    ) {
        $this->invoiceTotalManagement = $invoiceTotalManagement;
        $this->invoiceExtensionFactory = $invoiceExtensionFactory;
        $this->invoiceTotalGroupFactory = $invoiceTotalGroupFactory;
    }

    /**
     * @param  \Magento\Sales\Api\InvoiceRepositoryInterface $subject
     * @param  \Magento\Sales\Api\Data\InvoiceInterface      $invoice
     *
     * @return \Magento\Sales\Api\Data\InvoiceInterface      $invoice
     */
    public function afterGet(
        \Magento\Sales\Api\InvoiceRepositoryInterface $subject,
        \Magento\Sales\Api\Data\InvoiceInterface $invoice
    ) {

        $this->applyExtensionAttributes($invoice);
        return $invoice;
    }

    /**
     * @param  \Magento\Sales\Api\InvoiceRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\InvoiceSearchResultInterface $result
     *
     * @return \Magento\Sales\Api\Data\InvoiceSearchResultInterface
     */
    public function afterGetList(
        \Magento\Sales\Api\InvoiceRepositoryInterface $subject,
        \Magento\Sales\Api\Data\InvoiceSearchResultInterface $result
    ) {

        $invoices = $result->getItems();
        if (!empty($invoices)) {
            foreach ($invoices as $invoice) {
                $this->applyExtensionAttributes($invoice);
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\InvoiceInterface $invoice
     *
     * @return void
     */
    private function applyExtensionAttributes(\Magento\Sales\Api\Data\InvoiceInterface $invoice)
    {

        $extensionAttributes = $invoice->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->invoiceExtensionFactory->create();
        }

        $foomanTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$foomanTotalGroup) {
            $foomanTotalGroup = $this->invoiceTotalGroupFactory->create();
        }

        $invoiceTotals = $this->invoiceTotalManagement->getByInvoiceId(
            $invoice->getEntityId()
        );

        if (!empty($invoiceTotals)) {
            foreach ($invoiceTotals as $invoiceTotal) {
                $foomanTotalGroup->addItem($invoiceTotal);
            }
        }

        $extensionAttributes->setFoomanTotalGroup($foomanTotalGroup);
        $invoice->setExtensionAttributes($extensionAttributes);
    }
}
