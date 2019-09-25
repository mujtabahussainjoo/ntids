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

class CartSave
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

    /**
     * @param \Fooman\Totals\Model\QuoteAddressTotalFactory                          $quoteAddressTotalFactory
     * @param \Magento\Framework\DB\TransactionFactory                               $transactionFactory
     * @param \Fooman\Totals\Model\QuoteAddressTotalManagement                       $quoteAddressTotalManagement
     */
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
     * @param  \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Closure                                    $proceed
     * @param  \Magento\Quote\Api\Data\CartInterface      $cart
     *
     * @return \Magento\Quote\Api\Data\CartInterface       $cart
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Quote\Api\CartRepositoryInterface $subject,
        \Closure $proceed,
        \Magento\Quote\Api\Data\CartInterface $cart
    ) {
        $proceed($cart);
        $this->saveQuoteAddressTotals($cart);
        return $cart;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $cart
     *
     * @throws \Exception
     */
    public function saveQuoteAddressTotals(\Magento\Quote\Api\Data\CartInterface $cart)
    {
        $saveTransaction = false;
        $transaction = $this->transactionFactory->create();
        /** @var \Magento\Quote\Model\Quote\Address $address */
        foreach ($cart->getAllAddresses() as $address) {
            $extensionAttributes = $address->getExtensionAttributes();
            if (!$extensionAttributes) {
                continue;
            }

            /** @var \Fooman\Totals\Api\Data\TotalGroupInterface $quoteAddressTotalGroup */
            $quoteAddressTotalGroup = $extensionAttributes->getFoomanTotalGroup();
            if (!$quoteAddressTotalGroup) {
                continue;
            }

            foreach ($quoteAddressTotalGroup->getItems() as $totalItem) {
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
                $quoteAddressTotal->setTaxAmount($totalItem->getTaxAmount());
                $quoteAddressTotal->setBaseTaxAmount($totalItem->getBaseTaxAmount());
                $quoteAddressTotal->setBasePrice($totalItem->getBasePrice());
                $quoteAddressTotal->setLabel($totalItem->getLabel());
                $quoteAddressTotal->setTypeId($totalItem->getTypeId());
                $quoteAddressTotal->setCode($totalItem->getCode());
                $quoteAddressTotal->setQuoteId($cart->getId());
                $quoteAddressTotal->setQuoteAddressId($address->getId());
                $transaction->addObject($quoteAddressTotal);
                $saveTransaction = true;
            }
        }
        if ($saveTransaction) {
            $transaction->save();
        }
    }
}
