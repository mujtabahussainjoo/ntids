<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Block\Adminhtml\Sales\Order\Invoice;

class Totals extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals
{
    /**
     * @var \Fooman\Totals\Model\InvoiceTotalManagement
     */
    private $invoiceTotalManagement;

    /**
     * @var \Fooman\Surcharge\Helper\Surcharge
     */
    private $surchargeHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Framework\Registry                       $registry
     * @param \Magento\Sales\Helper\Admin                       $adminHelper
     * @param \Fooman\Totals\Model\InvoiceTotalManagement       $invoiceTotalManagement
     * @param \Fooman\Surcharge\Helper\Surcharge                $surchargeHelper
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Fooman\Totals\Model\InvoiceTotalManagement $invoiceTotalManagement,
        \Fooman\Surcharge\Helper\Surcharge $surchargeHelper,
        array $data = []
    ) {
        $this->invoiceTotalManagement = $invoiceTotalManagement;
        $this->surchargeHelper = $surchargeHelper;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $extensionAttributes = $this->getInvoice()->getExtensionAttributes();
        if ($extensionAttributes &&
            is_object($invoiceTotalGroup = $extensionAttributes->getFoomanTotalGroup())
        ) {
            $invoiceTotals = $invoiceTotalGroup->getItems();
        } else {
            $invoiceTotals = $this->invoiceTotalManagement->getByCodeAndInvoiceId(
                \Fooman\Surcharge\Model\Surcharge::CODE,
                $this->getInvoice()->getId()
            );
        }

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
