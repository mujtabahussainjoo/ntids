<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Serole\Neatcoins\Model;



/**
 * Pay In Store payment method model
 */
class Neatcoins extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'neatcoins';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        $grandTotal = $cart->getQuote()->getGrandTotal();
		$cash = $objectManager->get('\Serole\Cashback\Block\Customer\Dashboard');
		$totalPoints = $cash->getTotalPoints();
		$usedPoints = $cash->getUsedPoints();

        $cashAvilable = ($totalPoints-$usedPoints)/10;
		
		if($cashAvilable >= $grandTotal)
		{
			$this->setTitle("Neat Coins(Available cash:$$cashAvilable)");
			return true;
		}
		else
			return false;
		 
    }
  

}
