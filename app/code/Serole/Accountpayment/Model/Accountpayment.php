<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Serole\Accountpayment\Model;



/**
 * Pay In Store payment method model
 */
class Accountpayment extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'accountpayment';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
	
	protected $_canCapture = true;



    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null) {
       return true;
    }



  

}
