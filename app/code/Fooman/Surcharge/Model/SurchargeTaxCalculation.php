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

use \Magento\Tax\Api\Data\TaxClassKeyInterface;
use \Magento\Customer\Model\ResourceModel\GroupRepository as CustomerGroupRepository;
use \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

class SurchargeTaxCalculation
{
    /**
     * @var \Fooman\Surcharge\Helper\Currency
     */
    private $currencyHelper;

    /**
     * @var \Fooman\Surcharge\Helper\Surcharge
     */
    private $surchargeHelper;

    /**
     * @var \Magento\Tax\Api\Data\QuoteDetailsInterfaceFactory
     */
    private $quoteDetailsFactory;

    /**
     * @var \Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory
     */
    private $quoteDetailsItemFactory;

    /**
     * @var \Magento\Tax\Model\TaxClass\KeyFactory
     */
    private $taxClassKeyFactory;

    /**
     * @var CommonTaxCollector
     */
    private $commonTaxCollector;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * @var \Magento\Customer\Api\GroupManagementInterface
     */
    private $customerGroupManagement;

    /**
     * @param \Fooman\Surcharge\Helper\Currency                       $currencyHelper
     * @param \Fooman\Surcharge\Helper\Surcharge                      $surchargeHelper
     * @param \Magento\Tax\Api\Data\QuoteDetailsInterfaceFactory      $quoteDetailsFactory
     * @param \Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory  $quoteDetailsItemFactory
     * @param \Magento\Tax\Model\TaxClass\KeyFactory                  $taxClassKeyFactory
     * @param \Magento\Customer\Api\GroupRepositoryInterface          $customerGroupRepository
     * @param \Magento\Customer\Api\GroupManagementInterface          $customerGroupManagement
     * @param CommonTaxCollector                                      $commonTaxCollector
     */
    public function __construct(
        \Fooman\Surcharge\Helper\Currency $currencyHelper,
        \Fooman\Surcharge\Helper\Surcharge $surchargeHelper,
        \Magento\Tax\Api\Data\QuoteDetailsInterfaceFactory $quoteDetailsFactory,
        \Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory $quoteDetailsItemFactory,
        \Magento\Tax\Model\TaxClass\KeyFactory $taxClassKeyFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository,
        \Magento\Customer\Api\GroupManagementInterface $customerGroupManagement,
        CommonTaxCollector $commonTaxCollector
    ) {
        $this->currencyHelper = $currencyHelper;
        $this->surchargeHelper = $surchargeHelper;
        $this->quoteDetailsFactory = $quoteDetailsFactory;
        $this->quoteDetailsItemFactory = $quoteDetailsItemFactory;
        $this->taxClassKeyFactory = $taxClassKeyFactory;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerGroupManagement = $customerGroupManagement;
        $this->commonTaxCollector = $commonTaxCollector;
    }

    /**
     * @param $surchargeTotal
     * @param $quote
     * @param $address
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function calculateTotalsTaxAmount($surchargeTotal, $quote, $address)
    {
        $surcharge = $this->surchargeHelper->getSurchargeByTypeId($surchargeTotal->getTypeId());
        $surchargeTaxClassId = $surcharge->getTaxClassId();

        if ($surchargeTaxClassId) {
            $quoteDetails = $this->populateQuoteDetails(
                $surchargeTotal,
                $quote,
                $address,
                $surchargeTaxClassId,
                $surcharge->getTaxInclusive()
            );

            $associatedTaxables = $address->getAssociatedTaxables();
            if (!$associatedTaxables) {
                $associatedTaxables = [];
            }

            $associatedTaxables[$surchargeTotal->getTypeId()] = $this->populateAssociatedTax(
                $quoteDetails->getItems()['fooman_surcharge'],
                $quote
            );
            $address->setAssociatedTaxables($associatedTaxables);
        }
    }

    private function populateAssociatedTax($taxDetails, $quote)
    {

        $unitAmount = $this->currencyHelper->convertToQuoteCurrency(
            $quote,
            $taxDetails->getUnitPrice()
        );

        return [
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TYPE               => $taxDetails->getType(),
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_CODE               => $taxDetails->getCode(),
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE         => $unitAmount,
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE    => $taxDetails->getUnitPrice(),
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_QUANTITY           => $taxDetails->getQuantity(),
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID       => $taxDetails->getTaxClassId(),
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_PRICE_INCLUDES_TAX => $taxDetails->getIsTaxIncluded(),
            CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_ASSOCIATION_ITEM_CODE
                => CommonTaxCollector::ASSOCIATION_ITEM_CODE_FOR_QUOTE,
        ];
    }

    /**
     * @param      $surchargeTotal
     * @param      $quote
     * @param      $address
     * @param      $surchargeTaxClassId
     * @param bool $taxIncl
     *
     * @return \Magento\Tax\Api\Data\QuoteDetailsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function populateQuoteDetails($surchargeTotal, $quote, $address, $surchargeTaxClassId, $taxIncl = false)
    {

        /** @var \Magento\Tax\Model\TaxClass\Key $taxClassKey */
        $taxClassKey = $this->taxClassKeyFactory->create();
        $taxClassKey->setType(TaxClassKeyInterface::TYPE_ID);
        $taxClassKey->setValue($surchargeTaxClassId);

        /** @var \Magento\Tax\Api\Data\QuoteDetailsItemInterface $quoteDetailsItem */
        $quoteDetailsItem = $this->quoteDetailsItemFactory->create();
        $quoteDetailsItem->setType('fooman_surcharge');
        $quoteDetailsItem->setCode($surchargeTotal->getTypeId());
        $quoteDetailsItem->setQuantity(1);

        //we can only get accurate tax rate in a tax inclusive mode
        //if we have customer tax rate == store rate
        //or in cross border trade mode
        //@see \Magento\Tax\Model\Calculation\AbstractCalculator::isSameRateAsStore()
        if ($surchargeTotal->getBasePrice() !== null) {
            $quoteDetailsItem->setIsTaxIncluded($taxIncl);
            $quoteDetailsItem->setUnitPrice($surchargeTotal->getBasePrice());   //Keep calculations in base currency
        } else {
            $quoteDetailsItem->setIsTaxIncluded(false);
            $quoteDetailsItem->setUnitPrice($surchargeTotal->getBaseAmount());   //Keep calculations in base currency
        }

        $quoteDetailsItem->setTaxClassId($surchargeTaxClassId);
        $quoteDetailsItem->setTaxClassKey($taxClassKey);

        if (is_numeric($customerGroupId = $quote->getCustomer()->getGroupId())) {
            $customerGroup = $this->customerGroupRepository->getById($customerGroupId);
        } else {
            $customerGroup = $this->customerGroupManagement->getDefaultGroup($quote->getStoreId());
        }

        if ($customerGroup && $customerGroup->getTaxClassId()) {
            $customerTaxClassId = $customerGroup->getTaxClassId();
        } else {
            $customerTaxClassId = CustomerGroupRepository::DEFAULT_TAX_CLASS_ID;
        }

        /** @var \Magento\Tax\Model\TaxClass\Key $customerTaxClassKey */
        $customerTaxClassKey = $this->taxClassKeyFactory->create();
        $customerTaxClassKey->setType(TaxClassKeyInterface::TYPE_ID)
                            ->setValue((string)$customerTaxClassId);

        /** @var \Magento\Tax\Api\Data\QuoteDetailsInterface $quoteDetails */
        $quoteDetails = $this->quoteDetailsFactory->create();
        $quoteDetails->setItems([$quoteDetailsItem->getType() => $quoteDetailsItem]);
        $quoteDetails->setCustomerTaxClassKey($customerTaxClassKey);
        $quoteDetails->setCustomerId($quote->getCustomerId());
        $this->commonTaxCollector->populateAddressData($quoteDetails, $address);

        return $quoteDetails;
    }
}
