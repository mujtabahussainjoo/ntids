<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Totals\Helper;

class QuoteAddress
{
    /**
     * @var \Magento\Quote\Api\Data\AddressExtensionFactory
     */
    private $addressExtensionFactory;

    /**
     * @var \Fooman\Totals\Model\QuoteAddressTotalFactory
     */
    private $quoteAddressTotalFactory;

    /**
     * @var \Fooman\Totals\Model\QuoteAddressGroupFactory
     */
    private $groupFactory;

    /**
     * @param \Magento\Quote\Api\Data\AddressExtensionFactory        $addressExtensionFactory
     * @param \Fooman\Totals\Model\QuoteAddressTotalFactory $quoteAddressTotalFactory
     * @param \Fooman\Totals\Model\QuoteAddressGroupFactory         $groupFactory
     */
    public function __construct(
        \Magento\Quote\Api\Data\AddressExtensionFactory $addressExtensionFactory,
        \Fooman\Totals\Model\QuoteAddressTotalFactory $quoteAddressTotalFactory,
        \Fooman\Totals\Model\QuoteAddressGroupFactory $groupFactory
    ) {
        $this->addressExtensionFactory = $addressExtensionFactory;
        $this->quoteAddressTotalFactory = $quoteAddressTotalFactory;
        $this->groupFactory = $groupFactory;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param \Fooman\Totals\Api\Data\QuoteAddressTotalInterface $total
     */
    public function setExtensionAttributes(
        \Magento\Quote\Model\Quote\Address $address,
        \Fooman\Totals\Api\Data\QuoteAddressTotalInterface $total
    ) {
        $extensionAttributes = $address->getExtensionAttributes();

        if (is_array($extensionAttributes)) {
            // rest/default/V1/guest-carts/--ID--/estimate-shipping-methods
            // can deliver extension attributes as an array
            return;
        }

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->addressExtensionFactory->create();
        }

        /** @var \Fooman\Totals\Api\Data\QuoteAddressTotalGroupInterface $quoteAddressTotalGroup */
        $quoteAddressTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$quoteAddressTotalGroup) {
            $quoteAddressTotalGroup = $this->groupFactory->create();
        }

        /** @var \Fooman\Totals\Api\Data\TotalInterface $quoteAddressTotal */
        $quoteAddressTotal = $quoteAddressTotalGroup->getByTypeId($total->getTypeId());

        if (!$quoteAddressTotal) {
            $quoteAddressTotal = $this->quoteAddressTotalFactory->create();
        }
        
        $quoteAddressTotal->setAmount($total->getAmount());
        $quoteAddressTotal->setBaseAmount($total->getBaseAmount());
        $quoteAddressTotal->setBasePrice($total->getBasePrice());
        $quoteAddressTotal->setLabel($total->getLabel());
        $quoteAddressTotal->setTypeId($total->getTypeId());
        $quoteAddressTotal->setCode($total->getCode());
        $quoteAddressTotal->setTaxAmount($total->getTaxAmount());
        $quoteAddressTotal->setBaseTaxAmount($total->getBaseTaxAmount());

        $quoteAddressTotalGroup->addItem($quoteAddressTotal);
        $extensionAttributes->setFoomanTotalGroup($quoteAddressTotalGroup);

        //workaround for https://github.com/magento/magento2/issues/12921
        if (is_callable([$extensionAttributes, 'getCheckoutFields'])
            && null === $extensionAttributes->getCheckoutFields()) {
            $extensionAttributes->setCheckoutFields([]);
        }

        $address->setExtensionAttributes($extensionAttributes);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param  array                             $taxAmountTotals
     * @param  string                            $typeId
     */
    public function setTaxExtensionAttributes(
        \Magento\Quote\Model\Quote\Address $address,
        $taxAmountTotals,
        $typeId
    ) {
        $extensionAttributes = $address->getExtensionAttributes();

        if ($extensionAttributes === null) {
            return;
        }

        /** @var \Fooman\Totals\Api\Data\QuoteAddressTotalGroupInterface $quoteAddressTotalGroup */
        $quoteAddressTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$quoteAddressTotalGroup) {
            return;
        }

        /** @var \Fooman\Totals\Api\Data\QuoteAddressTotalInterface $quoteAddressTotal */
        $quoteAddressTotal = $quoteAddressTotalGroup->getByTypeId($typeId);

        if (!$quoteAddressTotal) {
            return;
        }
        
        $quoteAddressTotal->setTaxAmount($taxAmountTotals['amount']);
        $quoteAddressTotal->setBaseTaxAmount($taxAmountTotals['base_amount']);

        $quoteAddressTotalGroup->addItem($quoteAddressTotal);
        $extensionAttributes->setFoomanTotalGroup($quoteAddressTotalGroup);

        $address->setExtensionAttributes($extensionAttributes);
    }
}
