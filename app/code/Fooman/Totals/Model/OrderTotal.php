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

use Fooman\Totals\Model\ResourceModel\OrderTotal as ResourceOrderTotal;

class OrderTotal extends \Magento\Framework\Model\AbstractModel implements \Fooman\Totals\Api\Data\OrderTotalInterface
{

    const KEY_TYPE_ID     = 'type_id';
    const KEY_CODE        = 'code';
    const KEY_AMOUNT      = 'amount';
    const KEY_BASE_AMOUNT = 'base_amount';
    const KEY_LABEL       = 'label';
    const KEY_TAX_AMOUNT      = 'tax_amount';
    const KEY_BASE_TAX_AMOUNT = 'base_tax_amount';
    const KEY_AMOUNT_INVOICED = 'amount_invoiced';
    const KEY_BASE_AMOUNT_INVOICED = 'base_amount_invoiced';
    const KEY_AMOUNT_REFUNDED = 'amount_refunded';
    const KEY_BASE_AMOUNT_REFUNDED = 'base_amount_refunded';

    protected function _construct()
    {
        $this->_init(ResourceOrderTotal::class);
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

    /*
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
     * Get amount invoiced
     *
     * @return float
     */
    public function getAmountInvoiced()
    {
        return $this->getData(self::KEY_AMOUNT_INVOICED);
    }

    /**
     * Set amount invoiced
     *
     * @param float $amountInvoiced
     * @return void
     */
    public function setAmountInvoiced($amountInvoiced)
    {
        $this->setData(self::KEY_AMOUNT_INVOICED, $amountInvoiced);
    }

    /**
     * Get base amount invoiced
     *
     * @return float
     */
    public function getBaseAmountInvoiced()
    {
        return $this->getData(self::KEY_BASE_AMOUNT_INVOICED);
    }

    /**
     * Set base amount invoiced
     *
     * @param float $baseAmountInvoiced
     * @return void
     */
    public function setBaseAmountInvoiced($baseAmountInvoiced)
    {
        $this->setData(self::KEY_BASE_AMOUNT_INVOICED, $baseAmountInvoiced);
    }

    /**
     * Get amount refunded
     *
     * @return float
     */
    public function getAmountRefunded()
    {
        return $this->getData(self::KEY_AMOUNT_REFUNDED);
    }

    /**
     * Set amount refunded
     *
     * @param float $amountRefunded
     * @return void
     */
    public function setAmountRefunded($amountRefunded)
    {
        $this->setData(self::KEY_AMOUNT_REFUNDED, $amountRefunded);
    }
    
    /**
     * Get base amount refunded
     *
     * @return float
     */
    public function getBaseAmountRefunded()
    {
        return $this->getData(self::KEY_BASE_AMOUNT_REFUNDED);
    }

    /**
     * Set base amount refunded
     *
     * @param float $baseAmountRefunded
     * @return void
     */
    public function setBaseAmountRefunded($baseAmountRefunded)
    {
        $this->setData(self::KEY_BASE_AMOUNT_REFUNDED, $baseAmountRefunded);
    }
}
