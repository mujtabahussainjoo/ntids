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

class Surcharge extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{

    /**
     * @var \Fooman\Totals\Model\OrderTotalManagement
     */
    private $orderTotalManagement;

    /**
     * @var \Fooman\Totals\Helper\Creditmemo
     */
    private $creditmemoHelper;

    /**
     * @var \Fooman\Totals\Model\CreditmemoTotalFactory
     */
    private $creditmemoTotalFactory;

    /**
     * @var \Fooman\Surcharge\Helper\Currency
     */
    private $priceCurrency;

    /**
     * @var \Fooman\Surcharge\Helper\Surcharge
     */
    private $surchargeHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @param \Fooman\Totals\Model\OrderTotalManagement   $orderTotalManagement
     * @param \Fooman\Totals\Helper\Creditmemo            $creditmemoHelper
     * @param \Fooman\Totals\Model\CreditmemoTotalFactory $creditmemoTotalFactory
     * @param \Fooman\Surcharge\Helper\Currency           $priceCurrency
     * @param \Fooman\Surcharge\Helper\Surcharge          $surchargeHelper
     * @param \Magento\Framework\App\RequestInterface     $request
     * @param array                                       $data
     */
    public function __construct(
        \Fooman\Totals\Model\OrderTotalManagement $orderTotalManagement,
        \Fooman\Totals\Helper\Creditmemo $creditmemoHelper,
        \Fooman\Totals\Model\CreditmemoTotalFactory $creditmemoTotalFactory,
        \Fooman\Surcharge\Helper\Currency $priceCurrency,
        \Fooman\Surcharge\Helper\Surcharge $surchargeHelper,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        $this->orderTotalManagement = $orderTotalManagement;
        $this->creditmemoHelper = $creditmemoHelper;
        $this->creditmemoTotalFactory = $creditmemoTotalFactory;
        $this->priceCurrency = $priceCurrency;
        $this->surchargeHelper = $surchargeHelper;
        $this->request = $request;
        parent::__construct($data);
    }

    /**
     * @param  \Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();

        $foomanOrderTotals = $this->getFoomanSurchargeOrderTotals($order);

        $baseCreditmemoTotals = 0;
        $creditmemoTotals = 0;

        $taxAdjustments = 0;
        $baseTaxAdjustments = 0;

        $taxExcl = $this->surchargeHelper->isSalesDisplayedTaxExclusive($order->getStoreId());
        $params = $this->request->getParams();

        foreach ($foomanOrderTotals as $foomanOrderTotal) {
            if ($foomanOrderTotal->getAmount() == 0) {
                continue;
            }

            if (isset($params['creditmemo']['fooman_surcharge'])) {
                if (!isset($params['creditmemo']['fooman_surcharge'][$foomanOrderTotal->getTypeId()])) {
                    continue;
                }
                $surchargeInclAmount = (float)$params['creditmemo']['fooman_surcharge'][$foomanOrderTotal->getTypeId()];
                $allowedSurchargeAmount = $foomanOrderTotal->getAmount() - $foomanOrderTotal->getAmountRefunded();
                //get the surcharge amount based on the ratio of surcharge and total surcharge amount
                if ($taxExcl) {
                    $factor = $surchargeInclAmount / $foomanOrderTotal->getAmount();
                } else {
                    $factor = $surchargeInclAmount /
                        ($foomanOrderTotal->getAmount() + $foomanOrderTotal->getTaxAmount());
                }

                $surchargeAmount = $this->priceCurrency->round(
                    $factor * $foomanOrderTotal->getAmount()
                );

                $baseSurchargeAmount = $this->priceCurrency->round(
                    $factor * $foomanOrderTotal->getBaseAmount()
                );

                if ((float)$surchargeAmount > (float)$allowedSurchargeAmount) {
                    if (!$taxExcl) {
                        $allowedSurchargeAmount += $this->priceCurrency->round(
                            $factor * $foomanOrderTotal->getTaxAmount()
                        );
                    }
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __(
                            sprintf(
                                'Only %s "%s" amount can be refunded.',
                                $allowedSurchargeAmount,
                                $foomanOrderTotal->getLabel()
                            )
                        )
                    );
                }
            } else {
                $surchargeAmount = $foomanOrderTotal->getAmount() - $foomanOrderTotal->getAmountRefunded();
                $baseSurchargeAmount = $foomanOrderTotal->getBaseAmount() - $foomanOrderTotal->getBaseAmountRefunded();
                $factor = $surchargeAmount / $foomanOrderTotal->getAmount();
            }

            $taxAmount = $this->priceCurrency->round($factor * $foomanOrderTotal->getTaxAmount());
            $baseTaxAmount = $this->priceCurrency->round($factor * $foomanOrderTotal->getBaseTaxAmount());

            $creditmemoTotal = $this->creditmemoTotalFactory->create();
            $creditmemoTotal->setAmount($surchargeAmount);
            $creditmemoTotal->setBaseAmount($baseSurchargeAmount);
            $creditmemoTotal->setTaxAmount($taxAmount);
            $creditmemoTotal->setBaseTaxAmount($baseTaxAmount);
            $creditmemoTotal->setLabel($foomanOrderTotal->getLabel());
            $creditmemoTotal->setCode($foomanOrderTotal->getCode());
            $creditmemoTotal->setTypeId($foomanOrderTotal->getTypeId());
            $this->creditmemoHelper->setExtensionAttributes(
                $creditmemo,
                $creditmemoTotal
            );
            $creditmemoTotals += $surchargeAmount;
            $baseCreditmemoTotals += $baseSurchargeAmount;
        }
        $creditmemo->setTaxAmount($creditmemo->getTaxAmount() + $taxAdjustments);
        $creditmemo->setBaseTaxAmount($creditmemo->getBaseTaxAmount() + $baseTaxAdjustments);

        $creditmemo->setBaseGrandTotal(
            $creditmemo->getBaseGrandTotal() +
            $baseCreditmemoTotals +
            $baseTaxAdjustments
        );

        $creditmemo->setGrandTotal(
            $creditmemo->getGrandTotal() +
            $creditmemoTotals +
            $taxAdjustments
        );

        return $this;
    }

    private function getFoomanSurchargeOrderTotals(\Magento\Sales\Model\Order $order)
    {
        return $this->orderTotalManagement
            ->getByCodeAndOrderId(\Fooman\Surcharge\Model\Surcharge::CODE, $order->getId());
    }
}
