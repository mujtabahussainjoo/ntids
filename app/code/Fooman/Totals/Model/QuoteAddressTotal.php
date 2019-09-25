<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Totals\Model;

use Fooman\Totals\Api\Data\QuoteAddressTotalInterface;
use Magento\Framework\Model\AbstractModel;
use Fooman\Totals\Model\ResourceModel\QuoteAddressTotal as ResourceQuoteAddressTotal;

class QuoteAddressTotal extends AbstractModel implements QuoteAddressTotalInterface
{

    const KEY_TYPE_ID     = 'type_id';
    const KEY_CODE        = 'code';
    const KEY_AMOUNT      = 'amount';
    const KEY_BASE_AMOUNT = 'base_amount';
    const KEY_BASE_PRICE = 'base_price';
    const KEY_QUOTE_ID    = 'quote_id';
    const KEY_QUOTE_ADDRESS_ID = 'quote_address_id';
    const KEY_TAX_AMOUNT        = 'tax_amount';
    const KEY_BASE_TAX_AMOUNT   = 'base_tax_amount';
    const KEY_LABEL       = 'label';

    protected function _construct()
    {
        $this->_init(ResourceQuoteAddressTotal::class);
    }

    /**
     * @return string|null
     */
    public function getTypeId()
    {
        return $this->getData(self::KEY_TYPE_ID);
    }

    /**
     * @param string $typeId
     * @return $this
     */
    public function setTypeId($typeId)
    {
        return $this->setData(self::KEY_TYPE_ID, $typeId);
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->getData(self::KEY_CODE);
    }

    /**
     * @param string $code
     * @return void
     */
    public function setCode($code)
    {
        $this->setData(self::KEY_CODE, $code);
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->getData(self::KEY_AMOUNT);
    }

    /**
     * @param float $amount
     * @return void
     */
    public function setAmount($amount)
    {
        $this->setData(self::KEY_AMOUNT, $amount);
    }

    /**
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->getData(self::KEY_BASE_AMOUNT);
    }

    /**
     * @param float $baseAmount
     * @return void
     */
    public function setBaseAmount($baseAmount)
    {
        $this->setData(self::KEY_BASE_AMOUNT, $baseAmount);
    }

    /**
     * @return float
     */
    public function getBasePrice()
    {
        return $this->getData(self::KEY_BASE_PRICE);
    }

    /**
     * @param float $price
     * @return void
     */
    public function setBasePrice($price)
    {
        $this->setData(self::KEY_BASE_PRICE, $price);
    }

    /**
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->getData(self::KEY_TAX_AMOUNT);
    }

    /**
     * @param float $taxAmount
     * @return void
     */
    public function setTaxAmount($taxAmount)
    {
        $this->setData(self::KEY_TAX_AMOUNT, $taxAmount);
    }

    /**
     * @return float
     */
    public function getBaseTaxAmount()
    {
        return $this->getData(self::KEY_BASE_TAX_AMOUNT);
    }

    /**
     * @param float $baseTaxAmount
     * @return void
     */
    public function setBaseTaxAmount($baseTaxAmount)
    {
        $this->setData(self::KEY_BASE_TAX_AMOUNT, $baseTaxAmount);
    }

    /**
     * Get quote id
     *
     * @return int|null
     */
    public function getQuoteId()
    {
        return $this->getData(self::KEY_QUOTE_ID);
    }

    /**
     * Set quote id
     *
     * @param int $quoteId
     *
     * @return void
     */
    public function setQuoteId($quoteId)
    {
        $this->setData(self::KEY_QUOTE_ID, $quoteId);
    }

    /**
     * Get quote address id
     *
     * @return int|null
     */
    public function getQuoteAddressId()
    {
        return $this->getData(self::KEY_QUOTE_ADDRESS_ID);
    }

    /**
     * Set quote address id
     *
     * @param int $quoteAddressId
     *
     * @return void
     */
    public function setQuoteAddressId($quoteAddressId)
    {
        $this->setData(self::KEY_QUOTE_ADDRESS_ID, $quoteAddressId);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getData(self::KEY_LABEL);
    }

    /**
     * @param string $label
     * @return void
     */
    public function setLabel($label)
    {
        $this->setData(self::KEY_LABEL, $label);
    }
}
