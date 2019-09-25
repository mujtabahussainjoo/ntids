<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Helper;

class Currency
{

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param  \Magento\Quote\Api\Data\CartInterface $quote
     * @param  float $baseAmount
     *
     * @return float
     */
    public function convertToQuoteCurrency(
        \Magento\Quote\Api\Data\CartInterface $quote,
        $baseAmount
    ) {
        return $this->priceCurrency->convertAndRound(
            $baseAmount,
            $quote->getStore(),
            $quote->getCurrency()
        );
    }

    public function round($amount)
    {
        return $this->priceCurrency->round($amount);
    }
}
