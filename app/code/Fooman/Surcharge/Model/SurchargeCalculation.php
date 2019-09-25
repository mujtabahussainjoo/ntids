<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model;

class SurchargeCalculation
{
    const FIXED = 'fixed';

    const PERCENT = 'percent';

    const FIXED_PLUS_PERCENT = 'fixed_plus_percent';

    const FIXED_MINIMUM = 'fixed_minimum';

    /**
     * @var \Fooman\Surcharge\Helper\Currency
     */
    private $currencyHelper;

    /**
     * @var \Fooman\Surcharge\Helper\SurchargeConfig
     */
    private $surchargeConfigHelper;

    /**
     * @var \Fooman\Totals\Model\QuoteAddressTotalFactory
     */
    private $quoteAddressTotalFactory;

    /**
     * @var \Magento\Quote\Api\Data\CartInterface
     */
    private $quote;

    /**
     * @var \Fooman\Surcharge\Api\SurchargeInterface
     */
    private $surcharge;

    /**
     * @var \Fooman\Surcharge\Model\SurchargeConfig
     */
    private $surchargeConfig;

    /**
     * @var SurchargeRestrictor
     */
    private $surchargeRestrictor;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    private $calculation;

    /**
     * @var \Magento\Tax\Model\Config
     */
    private $taxConfig;

    /**
     * @param \Fooman\Surcharge\Helper\Currency             $currencyHelper
     * @param \Fooman\Surcharge\Helper\SurchargeConfig      $surchargeConfigHelper
     * @param \Fooman\Totals\Model\QuoteAddressTotalFactory $quoteAddressTotalFactory
     * @param \Magento\Quote\Api\Data\CartInterface         $quote
     * @param \Fooman\Surcharge\Api\SurchargeInterface      $surcharge
     * @param SurchargeRestrictor                           $surchargeRestrictor
     * @param \Magento\Tax\Model\Calculation                $calculation
     * @param \Magento\Tax\Model\Config                     $taxConfig
     */
    public function __construct(
        \Fooman\Surcharge\Helper\Currency $currencyHelper,
        \Fooman\Surcharge\Helper\SurchargeConfig $surchargeConfigHelper,
        \Fooman\Totals\Model\QuoteAddressTotalFactory $quoteAddressTotalFactory,
        \Magento\Quote\Api\Data\CartInterface $quote,
        \Fooman\Surcharge\Api\SurchargeInterface $surcharge,
        SurchargeRestrictor $surchargeRestrictor,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Tax\Model\Config $taxConfig
    ) {
        $this->currencyHelper = $currencyHelper;
        $this->surchargeConfigHelper = $surchargeConfigHelper;
        $this->quoteAddressTotalFactory = $quoteAddressTotalFactory;
        $this->quote = $quote;
        $this->surcharge = $surcharge;
        $this->surchargeRestrictor = $surchargeRestrictor;
        $this->calculation = $calculation;
        $this->taxConfig = $taxConfig;
    }

    /**
     * @return float
     */
    public function getCurrentSubTotal()
    {
        $subTotal = 0;
        $basedOn = $this->getSurchargeConfig()->getSurchargeBasis();
        foreach ($this->quote->getAllAddresses() as $address) {
            if ($address->getAllItems()) {
                foreach ($address->getAllItems() as $item) {
                    if (in_array(System\SurchargeBasis::BASED_ON_SUBTOTAL, $basedOn)) {
                        $subTotal += $item->getBaseRowTotal()
                            - ($item->getBaseDiscountAmount() - $item->getDiscountTaxCompensationAmount());
                    }
                }
            }
            if (in_array(System\SurchargeBasis::BASED_ON_SHIPPING, $basedOn)) {
                $subTotal += $address->getBaseShippingAmount();
            }
        }
        return $subTotal;
    }

    private function getSurchargeConfig()
    {
        if (null === $this->surchargeConfig) {
            $this->surchargeConfig = $this->surchargeConfigHelper->getConfig($this->surcharge);
        }
        return $this->surchargeConfig;
    }

    /**
     * @return float
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getBaseSurchargeAmount()
    {
        $amount = 0;
        $currentSubtotal = $this->getCurrentSubTotal();
        $config = $this->surchargeConfigHelper->getConfig($this->surcharge);

        if ($this->surchargeRestrictor->surchargeApplies($this->quote, $config, $currentSubtotal)) {
            switch ($config->getCalculationMode()) {
                case self::FIXED:
                    $amount = $config->getFixed();
                    break;

                case self::PERCENT:
                    $amount = $currentSubtotal * $config->getRate() / 100;
                    break;

                case self::FIXED_PLUS_PERCENT:
                    $amount = ($currentSubtotal * $config->getRate() / 100)
                        + $config->getFixed();
                    break;

                case self::FIXED_MINIMUM:
                    $amount = $currentSubtotal * $config->getRate() / 100;
                    if ($amount < $config->getFixed()) {
                        $amount = $config->getFixed();
                    }
                    break;
                default:
                    $amount = 0;
            }
        }

        return $this->currencyHelper->round($amount);
    }

    private function adjustForTaxIncl($total)
    {
        $amount = $total->getBaseAmount();
        if ($amount && $this->surcharge->getTaxClassId() && $this->surcharge->getTaxInclusive()) {
            $taxRateRequest = $this->calculation->getRateRequest(
                $this->quote->getShippingAddress(),
                $this->quote->getBillingAddress(),
                $this->quote->getCustomerTaxClassId(),
                $this->quote->getStoreId(),
                $this->quote->getCustomerId()
            );
            $taxRateRequest->setProductClassId(
                $this->surcharge->getTaxClassId()
            );
            $rate = $this->calculation->getRate($taxRateRequest);
            $tax = $this->calculation->calcTaxAmount($amount, $rate, true, true);
            if ($this->ratesSimilarOrCrossBorder($rate, $taxRateRequest)) {
                //we only set base price if we can later use for tax inclusive calculations
                $total->setBasePrice($amount);
            }
            $total->setBaseAmount($amount - $tax);
        }
    }

    public function ratesSimilarOrCrossBorder($rate, $taxRateRequest)
    {
        if ($this->taxConfig->crossBorderTradeEnabled($this->quote->getStoreId())) {
            return true;
        }
        $storeRate = $this->calculation->getStoreRate($taxRateRequest, $this->quote->getStoreId());
        return (abs($rate - $storeRate) < 0.00001);
    }

    /**
     * @param bool $reset
     *
     * @return \Fooman\Totals\Model\QuoteAddressTotal
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processTotals($reset = false)
    {
        /** @var \Fooman\Totals\Model\QuoteAddressTotal $total */
        $total = $this->quoteAddressTotalFactory->create();
        $total->setTypeId($this->surcharge->getTypeId());
        $total->setCode(\Fooman\Surcharge\Model\Surcharge::CODE);
        $total->setLabel($this->surcharge->getDescription());

        if ($reset) {
            $total->setBaseAmount(0);
            $total->setAmount(0);
        } else {
            $total->setBaseAmount($this->getBaseSurchargeAmount());
            $this->adjustForTaxIncl($total);
            $total->setAmount($this->currencyHelper
                ->convertToQuoteCurrency($this->quote, $total->getBaseAmount()));
        }

        return $total;
    }
}
