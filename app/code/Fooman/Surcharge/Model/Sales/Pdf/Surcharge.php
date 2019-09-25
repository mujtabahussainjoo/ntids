<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Model\Sales\Pdf;

class Surcharge extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{

    /**
     * @var \Fooman\Totals\Model\ResourceModel\OrderTotal\CollectionFactory
     */
    private $orderTotalCollectionFactory;

    /**
     * @var \Fooman\Totals\Model\ResourceModel\InvoiceTotal\CollectionFactory
     */
    private $invoiceTotalCollectionFactory;

    /**
     * @var \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\CollectionFactory
     */
    private $creditmemoTotalCollectionFactory;

    /**
     * @var \Fooman\Surcharge\Helper\Surcharge
     */
    private $surchargeHelper;

    /**
     * @param \Magento\Tax\Helper\Data                                             $taxHelper
     * @param \Magento\Tax\Model\Calculation                                       $taxCalculation
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory   $ordersFactory
     * @param \Fooman\Totals\Model\ResourceModel\OrderTotal\CollectionFactory      $orderTotalCollectionFactory
     * @param \Fooman\Totals\Model\ResourceModel\InvoiceTotal\CollectionFactory    $invoiceTotalCollectionFactory
     * @param \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\CollectionFactory $creditmemoTotalCollectionFactory
     * @param \Fooman\Surcharge\Helper\Surcharge                                   $surchargeHelper
     * @param array                                                                $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory,
        \Fooman\Totals\Model\ResourceModel\OrderTotal\CollectionFactory $orderTotalCollectionFactory,
        \Fooman\Totals\Model\ResourceModel\InvoiceTotal\CollectionFactory $invoiceTotalCollectionFactory,
        \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\CollectionFactory $creditmemoTotalCollectionFactory,
        \Fooman\Surcharge\Helper\Surcharge $surchargeHelper,
        array $data = []
    ) {
        $this->orderTotalCollectionFactory = $orderTotalCollectionFactory;
        $this->invoiceTotalCollectionFactory = $invoiceTotalCollectionFactory;
        $this->creditmemoTotalCollectionFactory = $creditmemoTotalCollectionFactory;
        $this->surchargeHelper = $surchargeHelper;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $totalsItems = $this->getTotalsItems();

        if (empty($totalsItems)) {
            return [];
        }

        $totals = [];

        $storeId = $this->getOrder()->getStoreId();
        $displayZero = $this->surchargeHelper->isSalesZeroDisplayed($storeId);
        $displayBoth = $this->surchargeHelper->isSalesDisplayedBoth($storeId);
        $taxExcl = $this->surchargeHelper->isSalesDisplayedTaxExclusive($storeId);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        foreach ($totalsItems as $total) {
            if (!$displayZero && !($total->getAmount() <> 0)) {
                continue;
            }

            if ($displayBoth) {
                $totals[] = [
                    'amount' => $this->getOrder()->formatPriceTxt($total->getAmount()),
                    'label' => __($total->getLabel()) . ' ' . __('Excl. Tax'). ':',
                    'font_size' => $fontSize
                ];
                $totals[] = [
                    'amount' => $this->getOrder()->formatPriceTxt($total->getAmount() + $total->getTaxAmount()),
                    'label' => __($total->getLabel()) . ' ' . __('Incl. Tax'). ':',
                    'font_size' => $fontSize
                ];
            } elseif ($taxExcl) {
                $totals[] = [
                    'amount' => $this->getOrder()->formatPriceTxt($total->getAmount()),
                    'label' => __($total->getLabel()) . ':',
                    'font_size' => $fontSize
                ];
            } else {
                $totals[] = [
                    'amount' => $this->getOrder()->formatPriceTxt($total->getAmount() + $total->getTaxAmount()),
                    'label' => __($total->getLabel()) . ':',
                    'font_size' => $fontSize
                ];
            }
        }

        return $totals;
    }

    public function canDisplay()
    {
        $totalsItems = $this->getTotalsItems();
        return (count($totalsItems) > 0);
    }

    private function getTotalsItems()
    {
        $entityType = $this->getSource()->getEntityType();
        return $this->getTotalCollectionByEntityType($entityType)
            ->addFieldToFilter(
                $entityType . '_id',
                $this->getSource()->getId()
            )
            ->getItems();
    }

    private function getTotalCollectionByEntityType($entityType)
    {
        if ($entityType === 'invoice') {
            return $this->invoiceTotalCollectionFactory->create();
        }

        if ($entityType === 'creditmemo') {
            return $this->creditmemoTotalCollectionFactory->create();
        }

        if ($entityType === 'order') {
            return $this->orderTotalCollectionFactory->create();
        }

        throw new \Magento\Framework\Exception\LocalizedException(
            __('Unknown source entity type')
        );
    }
}
