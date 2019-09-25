<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Api;

interface SurchargeInterface
{

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     *
     * @return \Fooman\Totals\Api\Data\QuoteAddressTotalInterface[]
     */
    public function collect(\Magento\Quote\Api\Data\CartInterface $quote);

    /**
     * @return \Fooman\Surcharge\Api\Data\TypeInterface
     */
    public function getTypeInstance();

    /**
     * @return mixed
     */
    public function getTypeId();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getDescription();
}
