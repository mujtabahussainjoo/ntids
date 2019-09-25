<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Totals\Api\Data;

/**
 * Interface TotalInterface
 * @api
 */
interface TotalInterface
{
    /**
     * Get typeId
     *
     * @return string|null
     */
    public function getTypeId();

    /**
     * Set typeId
     *
     * @param string $typeId
     * @return void
     */
    public function setTypeId($typeId);

    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Set code
     *
     * @param string $code
     * @return void
     */
    public function setCode($code);

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount();

    /**
     * Set amount
     *
     * @param float $amount
     * @return void
     */
    public function setAmount($amount);

    /**
     * Get base amount
     *
     * @return float
     */
    public function getBaseAmount();

    /**
     * Set base amount
     *
     * @param float $amount
     * @return void
     */
    public function setBaseAmount($amount);

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set label for total
     *
     * @param string $label
     * @return void
     */
    public function setLabel($label);

    /**
     * Get tax amount
     *
     * @return float
     */
    public function getTaxAmount();

    /**
     * Set tax amount
     *
     * @param float $taxAmount
     * @return void
     */
    public function setTaxAmount($taxAmount);

    /**
     * Get base tax amount
     *
     * @return float
     */
    public function getBaseTaxAmount();

    /**
     * Set base tax amount
     *
     * @param float $taxAmount
     * @return void
     */
    public function setBaseTaxAmount($taxAmount);
}
