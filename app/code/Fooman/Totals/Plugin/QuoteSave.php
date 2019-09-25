<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Totals\Plugin;

class QuoteSave
{
    /**
     * @var \Fooman\Totals\Model\QuoteAddressTotalFactory
     */
    private $quoteAddressTotalFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    private $transactionFactory;

    /**
     * @var \Fooman\Totals\Model\QuoteAddressTotalManagement
     */
    private $quoteAddressTotalManagement;

    public function __construct(
        \Fooman\Totals\Model\QuoteAddressTotalFactory $quoteAddressTotalFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Fooman\Totals\Model\QuoteAddressTotalManagement $quoteAddressTotalManagement
    ) {
        $this->quoteAddressTotalFactory = $quoteAddressTotalFactory;
        $this->transactionFactory = $transactionFactory;
        $this->quoteAddressTotalManagement = $quoteAddressTotalManagement;
    }

    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @param \Closure                   $proceed
     *
     * @return \Magento\Quote\Model\Quote
     * @throws \Exception
     */
    public function aroundSave(
        \Magento\Quote\Model\Quote $subject,
        \Closure $proceed
    ) {
        $proceed();
        $this->saveQuoteAddressTotals($subject);
        return $subject;
    }

    /**
     * @param \Magento\Quote\Model\Quote $cart
     *
     * @throws \Exception
     */
    public function saveQuoteAddressTotals(\Magento\Quote\Model\Quote $cart)
    {
        $saveTransaction = false;
        $transaction = $this->transactionFactory->create();
        foreach ($cart->getAllAddresses() as $address) {
            $this->quoteAddressTotalManagement->deleteByCodeAndQuoteAddressId(
                $address->getId(),
                $cart->getId()
            );
            $extensionAttributes = $address->getExtensionAttributes();
            if (!$extensionAttributes) {
                continue;
            }
            $quoteAddressTotalGroup = $extensionAttributes->getFoomanTotalGroup();
            if (!$quoteAddressTotalGroup) {
                continue;
            }

            $foomanQuoteTotals = $quoteAddressTotalGroup->getItems();
            if (!empty($foomanQuoteTotals)) {
                $saveTransaction = true;
                foreach ($foomanQuoteTotals as $totalItem) {
                    /** @var \Fooman\Totals\Api\Data\QuoteAddressTotalInterface $totalItem */
                    $quoteAddressTotals = $this->quoteAddressTotalManagement
                        ->getByTypeIdAndAddressId(
                            $totalItem->getTypeId(),
                            $address->getId()
                        );

                    if (!empty($quoteAddressTotals)) {
                        $quoteAddressTotal = array_shift($quoteAddressTotals);
                    } else {
                        $quoteAddressTotal = $this->quoteAddressTotalFactory->create();
                    }

                    $quoteAddressTotal->setAmount($totalItem->getAmount());
                    $quoteAddressTotal->setBaseAmount($totalItem->getBaseAmount());
                    $quoteAddressTotal->setBasePrice($totalItem->getBasePrice());
                    $quoteAddressTotal->setTaxAmount($totalItem->getTaxAmount());
                    $quoteAddressTotal->setBaseTaxAmount($totalItem->getBaseTaxAmount());
                    $quoteAddressTotal->setLabel($totalItem->getLabel());
                    $quoteAddressTotal->setTypeId($totalItem->getTypeId());
                    $quoteAddressTotal->setCode($totalItem->getCode());
                    $quoteAddressTotal->setQuoteId($cart->getId());
                    $quoteAddressTotal->setQuoteAddressId($address->getId());
                    $transaction->addObject($quoteAddressTotal);
                }
            }
        }
        if ($saveTransaction) {
            $transaction->save();
        }
    }
}
