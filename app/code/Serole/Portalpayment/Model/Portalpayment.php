<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Serole\Portalpayment\Model;



/**
 * Pay In Store payment method model
 */
class Portalpayment extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'portalpayment';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
	
	protected $_canCapture = true;


  

}
