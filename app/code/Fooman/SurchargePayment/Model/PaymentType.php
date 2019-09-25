<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_SurchargePayment
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\SurchargePayment\Model;

class PaymentType implements \Fooman\Surcharge\Api\Data\TypeInterface
{
    /**
     * @var string
     */
    private $type = 'payment';

    /**
     * @var \Fooman\Surcharge\Helper\SurchargeConfig
     */
    private $surchargeConfigHelper;

    /**
     * @var \Fooman\Surcharge\Model\SurchargeCalculationFactory
     */
    private $surchargeCalculationFactory;

    /**
     * @param \Fooman\Surcharge\Model\SurchargeCalculationFactory $surchargeCalculationFactory
     * @param \Fooman\Surcharge\Helper\SurchargeConfig            $surchargeConfigHelper
     */
    public function __construct(
        \Fooman\Surcharge\Model\SurchargeCalculationFactory $surchargeCalculationFactory,
        \Fooman\Surcharge\Helper\SurchargeConfig $surchargeConfigHelper
    ) {
        $this->surchargeCalculationFactory = $surchargeCalculationFactory;
        $this->surchargeConfigHelper = $surchargeConfigHelper;
    }

    /**
     * @param  \Fooman\Surcharge\Api\SurchargeInterface $surcharge
     * @param  \Magento\Quote\Api\Data\CartInterface    $quote
     *
     * @return \Fooman\Totals\Model\QuoteAddressTotal[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function calculate(
        \Fooman\Surcharge\Api\SurchargeInterface $surcharge,
        \Magento\Quote\Api\Data\CartInterface $quote
    ) {
        $config = $this->surchargeConfigHelper->getConfig($surcharge);

        $paymentMethods = $config->getPayment();
        if (!$paymentMethods) {
            return [];
        }

        if (is_string($paymentMethods)) {
            $paymentMethods = [$paymentMethods];
        }

        $surchargeCalculation = $this->surchargeCalculationFactory
            ->create(['quote' => $quote, 'surcharge' => $surcharge]);

        if ($this->surchargeApplies($quote, $paymentMethods)) {
            return [$surchargeCalculation->processTotals()];
        }

        return [];
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __('Payment Surcharge');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param                                       $paymentMethods
     *
     * @return bool
     */
    public function surchargeApplies(\Magento\Quote\Api\Data\CartInterface $quote, $paymentMethods)
    {
        $currentPaymentMethod = $quote->getPayment()->getMethod();
        return in_array($currentPaymentMethod, $paymentMethods);
    }
}
