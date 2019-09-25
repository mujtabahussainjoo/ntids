<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Block\Adminhtml\Sales\Order\Creditmemo\Totals;

class View extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals
{
    /**
     * @var \Fooman\Totals\Model\CreditmemoTotalManagement
     */
    private $creditmemoTotalManagement;

    /**
     * @var \Fooman\Surcharge\Helper\Surcharge
     */
    private $surchargeHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Framework\Registry                       $registry
     * @param \Magento\Sales\Helper\Admin                       $adminHelper
     * @param \Fooman\Totals\Model\CreditmemoTotalManagement    $creditmemoTotalManagement
     * @param \Fooman\Surcharge\Helper\Surcharge                $surchargeHelper
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Fooman\Totals\Model\CreditmemoTotalManagement $creditmemoTotalManagement,
        \Fooman\Surcharge\Helper\Surcharge $surchargeHelper,
        array $data = []
    ) {
        $this->creditmemoTotalManagement = $creditmemoTotalManagement;
        $this->surchargeHelper = $surchargeHelper;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $extensionAttributes = $this->getCreditmemo()->getExtensionAttributes();

        if ($extensionAttributes &&
            is_object($creditmemoTotalGroup = $extensionAttributes->getFoomanTotalGroup())
        ) {
            $creditmemoTotals = $creditmemoTotalGroup->getItems();
        } else {
            $creditmemoTotals = $this->creditmemoTotalManagement->getByCodeAndCreditmemoId(
                \Fooman\Surcharge\Model\Surcharge::CODE,
                $this->getCreditmemo()->getId()
            );
        }

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
