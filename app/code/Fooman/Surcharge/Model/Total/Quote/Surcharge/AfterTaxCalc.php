<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model\Total\Quote\Surcharge;

use \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

class AfterTaxCalc extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    const CODE = 'fooman_surcharge_tax_after';

    /**
     * @var \Fooman\Totals\Helper\QuoteAddress
     */
    private $quoteAddressHelper;

    /**
     * @param \Fooman\Totals\Helper\QuoteAddress $quoteAddressHelper
     */
    public function __construct(
        \Fooman\Totals\Helper\QuoteAddress $quoteAddressHelper
    ) {
        $this->quoteAddressHelper = $quoteAddressHelper;
    }

    /**
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     *
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();

        if (empty($address->getAllItems())) {
            return $this;
        }
        $extraTaxes = $total->getExtraTaxableDetails();

        if (!isset($extraTaxes['fooman_surcharge'][CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE])) {
            return $this;
        }

        $surchargeTaxes = $extraTaxes['fooman_surcharge'][CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE];
        foreach ($surchargeTaxes as $surchargeTax) {
            $this->quoteAddressHelper->setTaxExtensionAttributes(
                $address,
                ['amount' => $surchargeTax['row_tax'], 'base_amount' => $surchargeTax['base_row_tax']],
                $surchargeTax['code']
            );
        }

        return $this;
    }

    public function getLabel()
    {
        return __('Surcharge Tax');
    }

    /**
     * @param \Magento\Quote\Model\Quote               $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $value = 0;
        $surchargeTotals = $this->getSurchargeTotals($quote);
        if (!empty($surchargeTotals)) {
            foreach ($surchargeTotals as $quoteAddressTotal) {
                $value += $quoteAddressTotal->getTaxAmount();
            }
        }

        $enhancedSurcharge = [
            'code'       => \Fooman\Surcharge\Model\Surcharge::CODE,
            'tax_amount' => $value
        ];

        $taxSurchargeTotal = [
            'code'  => self::CODE,
            'title' => $this->getLabel(),
            'value' => $value
        ];

        return [$taxSurchargeTotal, $enhancedSurcharge];
    }

    private function getSurchargeTotals(
        \Magento\Quote\Model\Quote $quote
    ) {
        $totals = [];
        if ($quote->getIsVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        $extAttr = $address->getExtensionAttributes();
        if (!$extAttr) {
            return $totals;
        }

        $foomanTotalGroup = $extAttr->getFoomanTotalGroup();
        if (!$foomanTotalGroup) {
            return $totals;
        }

        $foomanTotals = $foomanTotalGroup->getItems();
        if (!empty($foomanTotals)) {
            foreach ($foomanTotals as $foomanTotal) {
                if ($foomanTotal->getCode() === \Fooman\Surcharge\Model\Surcharge::CODE) {
                    $totals[] = $foomanTotal;
                }
            }
        }

        return $totals;
    }
}
