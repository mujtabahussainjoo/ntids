<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Block\Adminhtml\Sales\Order;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Fooman\Totals\Model\ResourceModel\OrderTotal\CollectionFactory
     */
    private $orderTotalCollectionFactory;

    /**
     * @var \Fooman\Surcharge\Helper\Surcharge
     */
    private $surchargeHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                $context
     * @param \Fooman\Totals\Model\ResourceModel\OrderTotal\CollectionFactory $orderTotalCollectionFactory
     * @param \Fooman\Surcharge\Helper\Surcharge                              $surchargeHelper
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Fooman\Totals\Model\ResourceModel\OrderTotal\CollectionFactory $orderTotalCollectionFactory,
        \Fooman\Surcharge\Helper\Surcharge $surchargeHelper,
        array $data = []
    ) {
        $this->orderTotalCollectionFactory = $orderTotalCollectionFactory;
        $this->surchargeHelper = $surchargeHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source = $parent->getSource();
        $orderTotals = $this->orderTotalCollectionFactory->create()
            ->addFieldToFilter('order_id', $source->getId())->getItems();

        if (empty($orderTotals)) {
            return $this;
        }

        $storeId = $source->getStoreId();
        $displayZero = $this->surchargeHelper->isSalesZeroDisplayed($storeId);
        $displayBoth = $this->surchargeHelper->isSalesDisplayedBoth($storeId);
        $taxExcl = $this->surchargeHelper->isSalesDisplayedTaxExclusive($storeId);

        $addAfterTotal = 'tax';
        if ($this->_scopeConfig->isSetFlag(\Magento\Tax\Model\Config::XML_PATH_DISPLAY_SALES_GRANDTOTAL)) {
            $addAfterTotal = 'shipping';
        }

        foreach ($orderTotals as $orderTotal) {
            if (!$displayZero && !($orderTotal->getAmount() <> 0)) {
                continue;
            }
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => $orderTotal->getTypeId(),
                    'value' => $orderTotal->getAmount(),
                    'base_value' => $orderTotal->getBaseAmount(),
                    'label' => __($orderTotal->getLabel())
                ]
            );

            $totalIncl = new \Magento\Framework\DataObject(
                [
                    'code' => $orderTotal->getTypeId(),
                    'value' => $orderTotal->getAmount() + $orderTotal->getTaxAmount(),
                    'base_value' => $orderTotal->getBaseAmount() + $orderTotal->getBaseTaxAmount(),
                    'label' => __($orderTotal->getLabel())
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
