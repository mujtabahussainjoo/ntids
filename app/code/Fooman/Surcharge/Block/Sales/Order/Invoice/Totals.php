<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Block\Sales\Order\Invoice;

class Totals extends \Magento\Sales\Block\Order\Invoice\Totals
{
    /**
     * @var \Fooman\Totals\Model\ResourceModel\InvoiceTotal\CollectionFactory
     */
    private $invoiceTotalCollectionFactory;

    /**
     * @var \Fooman\Surcharge\Helper\Surcharge
     */
    private $surchargeHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Fooman\Totals\Model\ResourceModel\InvoiceTotal\CollectionFactory $invoiceTotalCollectionFactory,
     * @param \Fooman\Surcharge\Helper\Surcharge $surchargeHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Fooman\Totals\Model\ResourceModel\InvoiceTotal\CollectionFactory $invoiceTotalCollectionFactory,
        \Fooman\Surcharge\Helper\Surcharge $surchargeHelper,
        array $data = []
    ) {
        $this->invoiceTotalCollectionFactory = $invoiceTotalCollectionFactory;
        $this->surchargeHelper = $surchargeHelper;
        parent::__construct($context, $registry, $data);
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $invoiceId = $this->getInvoice()->getId();
        $invoiceTotals = $this->invoiceTotalCollectionFactory->create()
            ->addFieldToFilter('invoice_id', $invoiceId)->getItems();

        if (empty($invoiceTotals)) {
            return $this;
        }

        $parent = $this->getParentBlock();

        $storeId = $this->getInvoice()->getStoreId();
        $displayZero = $this->surchargeHelper->isSalesZeroDisplayed($storeId);
        $displayBoth = $this->surchargeHelper->isSalesDisplayedBoth($storeId);
        $taxExcl = $this->surchargeHelper->isSalesDisplayedTaxExclusive($storeId);

        $addAfterTotal = 'tax';
        if ($this->_scopeConfig->isSetFlag(\Magento\Tax\Model\Config::XML_PATH_DISPLAY_SALES_GRANDTOTAL)) {
            $addAfterTotal = 'shipping';
        }

        foreach ($invoiceTotals as $invoiceTotal) {
            if (!$displayZero && !($invoiceTotal->getAmount() <> 0)) {
                continue;
            }
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => $invoiceTotal->getTypeId(),
                    'value' => $invoiceTotal->getAmount(),
                    'base_value' => $invoiceTotal->getBaseAmount(),
                    'label' => __($invoiceTotal->getLabel())
                ]
            );

            $totalIncl = new \Magento\Framework\DataObject(
                [
                    'code' => $invoiceTotal->getTypeId(),
                    'value' => $invoiceTotal->getAmount() + $invoiceTotal->getTaxAmount(),
                    'base_value' => $invoiceTotal->getBaseAmount() + $invoiceTotal->getBaseTaxAmount(),
                    'label' => __($invoiceTotal->getLabel())
                ]
            );

            if ($displayBoth) {
                $total->setLabel($total->getLabel() . ' ' . __('Excl. Tax'));
                $totalIncl->setLabel($totalIncl->getLabel() . ' ' . __('Incl. Tax'));
                $totalIncl->setCode($total->getCode() . '_incl');
            }

            if ($displayBoth) {
                $parent->addTotal($total, $addAfterTotal);
                $parent->addTotal($totalIncl, $total->getCode());
            } elseif ($taxExcl) {
                $parent->addTotal($total, $addAfterTotal);
            } else {
                $parent->addTotal($totalIncl, $addAfterTotal);
            }
        }

        return $this;
    }
}
