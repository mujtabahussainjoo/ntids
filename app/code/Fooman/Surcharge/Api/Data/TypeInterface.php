<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Api\Data;

interface TypeInterface
{

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param \Fooman\Surcharge\Api\SurchargeInterface $surcharge
     * @param \Magento\Quote\Api\Data\CartInterface    $quote
     *
     * @return \Fooman\Totals\Model\QuoteAddressTotal[]
     */
    public function calculate(
        \Fooman\Surcharge\Api\SurchargeInterface $surcharge,
        \Magento\Quote\Api\Data\CartInterface $quote
    );
}
