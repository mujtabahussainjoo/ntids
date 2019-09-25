<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_SurchargePayment
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class PaypalCart implements ObserverInterface
{

    /**
     * @param EventObserver $observer
     *
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Paypal\Model\Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $salesModel = $cart->getSalesModel();
        $extensionAttributes = $salesModel->getTaxContainer()->getExtensionAttributes();
        if (!$extensionAttributes) {
            return $this;
        }

        $foomanQuoteAddressTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$foomanQuoteAddressTotalGroup) {
            return $this;
        }

        $foomanOrderTotals = $foomanQuoteAddressTotalGroup->getItems();
        if (!empty($foomanOrderTotals)) {
            foreach ($foomanOrderTotals as $foomanOrderTotalItem) {
                $cart->addCustomItem(
                    $foomanOrderTotalItem->getLabel(),
                    1,
                    $foomanOrderTotalItem->getAmount(),
                    null //$foomanOrderTotalItem->getTypeId()
                    //if we use an identifier it gets array merged to all items
                );
            }
        }

        return $this;
    }
}
