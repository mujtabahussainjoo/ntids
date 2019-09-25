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

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Model\Quote\Address\ToOrder as QuoteAddressToOrder;
use Magento\Quote\Model\Quote\Address as QuoteAddress;

class ToOrderConverter
{
    /**
     * @var QuoteAddress
     */
    private $quoteAddress;

    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var \Fooman\Totals\Model\OrderTotalFactory
     */
    private $orderTotalFactory;

    /**
     * @var \Fooman\Totals\Model\GroupFactory
     */
    private $orderTotalGroupFactory;

    /**
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
     * @param \Fooman\Totals\Model\OrderTotalFactory        $orderTotalFactory
     * @param \Fooman\Totals\Model\GroupFactory             $orderTotalGroupFactory
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory,
        \Fooman\Totals\Model\OrderTotalFactory $orderTotalFactory,
        \Fooman\Totals\Model\GroupFactory $orderTotalGroupFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderTotalFactory = $orderTotalFactory;
        $this->orderTotalGroupFactory = $orderTotalGroupFactory;
    }

    /**
     * @param QuoteAddressToOrder $subject
     * @param QuoteAddress        $address
     * @param array               $additional
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeConvert(
        QuoteAddressToOrder $subject,
        QuoteAddress $address,
        $additional = []
    ) {
        $this->quoteAddress = $address;
        return [$address, $additional];
    }

    /**
     * @param QuoteAddressToOrder $subject
     * @param OrderInterface      $order
     *
     * @return OrderInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterConvert(
        QuoteAddressToOrder $subject,
        OrderInterface $order
    ) {
        $orderExtensionAttributes = $order->getExtensionAttributes();

        if ($orderExtensionAttributes === null) {
            $orderExtensionAttributes = $this->orderExtensionFactory->create();
        }

        $quoteAddressExtensionAttributes = $this->quoteAddress->getExtensionAttributes();
        if (!$quoteAddressExtensionAttributes) {
            return $order;
        }

        $quoteAddressTotalGroup = $quoteAddressExtensionAttributes->getFoomanTotalGroup();

        if (!$quoteAddressTotalGroup) {
            return $order;
        }

        $orderTotalGroup = $this->orderTotalGroupFactory->create();

        foreach ($quoteAddressTotalGroup->getItems() as $quoteAddressTotal) {
            $orderTotal = $this->orderTotalFactory->create();
            $orderTotal->setAmount($quoteAddressTotal->getAmount());
            $orderTotal->setBaseAmount($quoteAddressTotal->getBaseAmount());
            $orderTotal->setTaxAmount($quoteAddressTotal->getTaxAmount());
            $orderTotal->setBaseTaxAmount($quoteAddressTotal->getBaseTaxAmount());
            $orderTotal->setLabel($quoteAddressTotal->getLabel());
            $orderTotal->setTypeId($quoteAddressTotal->getTypeId());
            $orderTotal->setCode($quoteAddressTotal->getCode());
            $orderTotalGroup->addItem($orderTotal);
        }

        $orderExtensionAttributes->setFoomanTotalGroup($orderTotalGroup);

        $order->setExtensionAttributes($orderExtensionAttributes);
        return $order;
    }
}
