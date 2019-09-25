<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model\Total\Creditmemo;

class AfterTaxCalc extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{

    /**
     * If the Creditmemo is not last Magento does not automatically
     * get the correct taxable amounts, correct for it here
     *
     * @param  \Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        if (!$creditmemo->isLast()) {
            $creditmemoTotals = 0;
            $baseCreditmemoTotals = 0;
            $foomanTotals = $this->getTotalsFromExtAttr($creditmemo);

            if (empty($foomanTotals)) {
                return $this;
            }
            foreach ($foomanTotals as $total) {
                $creditmemoTotals += $total->getTaxAmount();
                $baseCreditmemoTotals += $total->getBaseTaxAmount();
            }

            $order = $creditmemo->getOrder();
            $allowedTax = $order->getTaxAmount() - $order->getTaxRefunded();
            if ($creditmemo->getTaxAmount() + $creditmemoTotals <= $allowedTax) {
                $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $creditmemoTotals);
                $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $baseCreditmemoTotals);

                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemoTotals);
                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseCreditmemoTotals);
            }
        }

        return $this;
    }

    private function getTotalsFromExtAttr(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $returnTotals = [];
        $extensionAttributes = $creditmemo->getExtensionAttributes();
        if (!$extensionAttributes) {
            return $returnTotals;
        }
        $foomanTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$foomanTotalGroup) {
            return $returnTotals;
        }
        $orderTotals = $foomanTotalGroup->getItems();
        if (empty($orderTotals)) {
            return $returnTotals;
        }

        foreach ($orderTotals as $orderTotal) {
            if ($orderTotal->getCode() == \Fooman\Surcharge\Model\Surcharge::CODE) {
                $returnTotals[] = $orderTotal;
            }
        }

        return $returnTotals;
    }
}
