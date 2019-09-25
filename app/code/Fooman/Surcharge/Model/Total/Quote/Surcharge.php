<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model\Total\Quote;

class Surcharge extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    /**
     * @var \Fooman\Totals\Model\QuoteAddressTotalManagement
     */
    private $quoteAddressTotalManagement;

    /**
     * @var \Fooman\Totals\Helper\QuoteAddress
     */
    private $quoteAddressHelper;

    /**
     * @var \Fooman\Surcharge\Model\ResourceModel\Surcharge\Collection
     */
    private $collection;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Fooman\Totals\Model\GroupFactory
     */
    private $groupFactory;

    /**
     * @param \Fooman\Surcharge\Model\ResourceModel\Surcharge\Collection $collection
     * @param \Fooman\Totals\Model\QuoteAddressTotalManagement           $quoteAddressTotalManagement
     * @param \Fooman\Totals\Helper\QuoteAddress                         $quoteAddressHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface          $priceCurrency
     */
    public function __construct(
        \Fooman\Surcharge\Model\ResourceModel\Surcharge\Collection $collection,
        \Fooman\Totals\Model\QuoteAddressTotalManagement $quoteAddressTotalManagement,
        \Fooman\Totals\Helper\QuoteAddress $quoteAddressHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Registry $registry,
        \Fooman\Totals\Model\GroupFactory $groupFactory
    ) {
        $this->collection = $collection;
        $this->quoteAddressTotalManagement = $quoteAddressTotalManagement;
        $this->quoteAddressHelper = $quoteAddressHelper;
        $this->priceCurrency = $priceCurrency;
        $this->registry = $registry;
        $this->groupFactory = $groupFactory;
    }

    /**
     * @return \Magento\Framework\Phrase.
     */
    public function getLabel()
    {
        return __('Surcharge');
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

        if (!$shippingAssignment->getItems()) {
            return $this;
        }

        $surchargeTotalFee = 0;
        $surchargeBaseTotalFee = 0;
        $processedSurcharges = [];
        $foomanTotalGroup = $this->groupFactory->create();

        $surcharges = $this->getSurcharges($quote->getStoreId());

        if ($surcharges->getSize() > 0) {
            foreach ($surcharges as $surcharge) {
                $surchargeTotals = $surcharge->collect($quote);
                if (!empty($surchargeTotals)) {
                    foreach ($surchargeTotals as $surchargeTotal) {
                        $foomanTotalGroup->addItem($surchargeTotal);
                        $surchargeTotalFee += $surchargeTotal->getAmount();
                        $surchargeBaseTotalFee += $surchargeTotal->getBaseAmount();
                        $this->quoteAddressHelper->setExtensionAttributes(
                            $address,
                            $surchargeTotal
                        );
                    }
                    $processedSurcharges[$surcharge->getTypeId()] = true;
                }
            }
        }
        $total->setFoomanSurcharges($foomanTotalGroup);
        $this->removeInactiveSurcharges($quote, $processedSurcharges);

        $total->setTotalAmount(
            \Fooman\Surcharge\Model\Surcharge::CODE,
            $this->priceCurrency->round($surchargeTotalFee)
        );

        $total->setBaseTotalAmount(
            \Fooman\Surcharge\Model\Surcharge::CODE,
            $this->priceCurrency->round($surchargeBaseTotalFee)
        );

        return $this;
    }

    private function removeInactiveSurcharges($quote, $processedSurcharges)
    {
        $dbSurcharges = $this->quoteAddressTotalManagement
            ->getByCodeAndQuoteId(
                \Fooman\Surcharge\Model\Surcharge::CODE,
                $quote->getId()
            );
        if ($dbSurcharges) {
            foreach ($dbSurcharges as $dbSurcharge) {
                if (!isset($processedSurcharges[$dbSurcharge->getTypeId()])) {
                    $this->quoteAddressTotalManagement->deleteByTypeIdAndQuoteId(
                        $dbSurcharge->getTypeId(),
                        $quote->getId()
                    );
                }
            }
        }
        if ($quote->getIsVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        $extAttr = $address->getExtensionAttributes();
        if (!$extAttr) {
            return;
        }

        $foomanTotalGroup = $extAttr->getFoomanTotalGroup();
        if (!$foomanTotalGroup) {
            return;
        }

        if ($foomanTotalGroup->getItems()) {
            foreach ($foomanTotalGroup->getItems() as $total) {
                if (!isset($processedSurcharges[$total->getTypeId()])) {
                    $foomanTotalGroup->removeByTypeId($total->getTypeId());
                }
            }
        }
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
        $totals = [];
        $surchargeTotals = $this->getSurchargeTotals($total);
        if (!empty($surchargeTotals)) {
            foreach ($surchargeTotals as $quoteAddressTotal) {
                $value += $quoteAddressTotal->getAmount();
                $totals[] = $quoteAddressTotal;
                $this->ensureExtensionAttrIsSet($quote, $quoteAddressTotal);
            }
        }

        return [
            'code' => \Fooman\Surcharge\Model\Surcharge::CODE,
            'title' => $this->getLabel(),
            'value' => $value,
            'full_info' => $totals
        ];
    }

    /**
     * @param $storeId
     *
     * @return \Fooman\Surcharge\Model\ResourceModel\Surcharge\Collection
     */
    public function getSurcharges($storeId)
    {
        return $this->collection
            ->addFieldToFilter('store_id', [$storeId, \Magento\Store\Model\Store::DEFAULT_STORE_ID])
            ->addFieldToFilter('is_active', true);
    }

    /**
     * If the totals are retrieved from the DB the surcharge totals are available via the registry
     * @see \Fooman\Totals\Plugin\CartTotalGet::aroundGet()
     *
     * If we have just run a collect on this quote in the same process
     * \Magento\Quote\Model\Quote\Address\Total will have the totals available via
     * getFoomanSurcharges()
     *
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array
     */
    private function getSurchargeTotals(
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $totals = [];
        $foomanTotalGroup = $total->getFoomanSurcharges();
        if ($foomanTotalGroup) {
            $quoteAddressTotals = $foomanTotalGroup->getItems();
            if (!empty($quoteAddressTotals)) {
                foreach ($quoteAddressTotals as $quoteTotal) {
                    if ($quoteTotal->getCode() === \Fooman\Surcharge\Model\Surcharge::CODE) {
                        $totals[] = $quoteTotal;
                    }
                }
            }
        }

        if (!empty($totals)) {
            return $totals;
        }
        $extensionAttributes = $this->registry->registry('fooman_totals_quote_address_extension_attributes');
        if ($extensionAttributes) {
            $foomanTotalGroup = $extensionAttributes->getFoomanTotalGroup();
            if ($foomanTotalGroup) {
                $quoteAddressTotals = $foomanTotalGroup->getItems();
                if (!empty($quoteAddressTotals)) {
                    foreach ($quoteAddressTotals as $quoteTotal) {
                        if ($quoteTotal->getCode() === \Fooman\Surcharge\Model\Surcharge::CODE) {
                            $totals[] = $quoteTotal;
                        }
                    }
                }
            }
        }

        return $totals;
    }

    private function ensureExtensionAttrIsSet($quote, $surchargeTotal)
    {
        if ($quote->getIsVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        $this->quoteAddressHelper->setExtensionAttributes(
            $address,
            $surchargeTotal
        );
    }
}
