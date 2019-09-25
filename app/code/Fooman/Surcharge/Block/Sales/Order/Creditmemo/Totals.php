<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Block\Sales\Order\Creditmemo;

class Totals extends \Magento\Sales\Block\Order\Creditmemo\Totals
{
    /**
     * @var \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\CollectionFactory
     */
    private $creditmemoTotalCollectionFactory;

    /**
     * @var \Fooman\Surcharge\Helper\Surcharge
     */
    private $surchargeHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\CollectionFactory $creditmemoTotalCollectionFactory,
     * @param \Fooman\Surcharge\Helper\Surcharge $surchargeHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Fooman\Totals\Model\ResourceModel\CreditmemoTotal\CollectionFactory $creditmemoTotalCollectionFactory,
        \Fooman\Surcharge\Helper\Surcharge $surchargeHelper,
        array $data = []
    ) {
        $this->creditmemoTotalCollectionFactory = $creditmemoTotalCollectionFactory;
        $this->surchargeHelper = $surchargeHelper;
        parent::__construct($context, $registry, $data);
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $creditmemoId = $this->getCreditmemo()->getId();
        $creditmemoTotals = $this->creditmemoTotalCollectionFactory->create()
            ->addFieldToFilter('creditmemo_id', $creditmemoId)->getItems();

        if (empty($creditmemoTotals)) {
            return $this;
        }

        $parent = $this->getParentBlock();

        $storeId = $this->getCreditmemo()->getStoreId();
        $displayZero = $this->surchargeHelper->isSalesZeroDisplayed($storeId);
        $displayBoth = $this->surchargeHelper->isSalesDisplayedBoth($storeId);
        $taxExcl = $this->surchargeHelper->isSalesDisplayedTaxExclusive($storeId);

        $addAfterTotal = 'tax';
        if ($this->_scopeConfig->isSetFlag(\Magento\Tax\Model\Config::XML_PATH_DISPLAY_SALES_GRANDTOTAL)) {
            $addAfterTotal = 'shipping';
        }

        foreach ($creditmemoTotals as $creditmemoTotal) {
            if (!$displayZero && !($creditmemoTotal->getAmount() <> 0)) {
                continue;
            }
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => $creditmemoTotal->getTypeId(),
                    'value' => $creditmemoTotal->getAmount(),
                    'base_value' => $creditmemoTotal->getBaseAmount(),
                    'label' => __($creditmemoTotal->getLabel())
                ]
            );

            $totalIncl = new \Magento\Framework\DataObject(
                [
                    'code' => $creditmemoTotal->getTypeId(),
                    'value' => $creditmemoTotal->getAmount() + $creditmemoTotal->getTaxAmount(),
                    'base_value' => $creditmemoTotal->getBaseAmount() + $creditmemoTotal->getBaseTaxAmount(),
                    'label' => __($creditmemoTotal->getLabel())
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
