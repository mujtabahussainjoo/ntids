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

use Fooman\Totals\Helper\QuoteAddress;
use Fooman\Totals\Model\QuoteAddressTotalManagement;
use Magento\Quote\Api\Data\CartInterface;

class LoadHandler
{
    private $totalManagement;

    private $quoteAddressHelper;

    public function __construct(
        QuoteAddressTotalManagement $totalManagement,
        QuoteAddress $quoteAddressHelper
    ) {
        $this->totalManagement = $totalManagement;
        $this->quoteAddressHelper = $quoteAddressHelper;
    }

    public function afterLoad(
        \Magento\Quote\Model\QuoteRepository\LoadHandler $subject,
        CartInterface $quote
    ) {
        /** @var \Magento\Quote\Model\Quote\Address $address */
        if ($quote->getIsVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        $totals = $this->totalManagement->getByQuoteAddressId($address->getId());
        if (!empty($totals)) {
            foreach ($totals as $total) {
                $this->quoteAddressHelper->setExtensionAttributes($address, $total);
            }
        }

        return $quote;
    }
}
