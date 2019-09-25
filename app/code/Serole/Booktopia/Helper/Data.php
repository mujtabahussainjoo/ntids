<?php
/**
 * Created by Serole(Dk) on 04/09/2018.
 * includes the code related to system configuration of Booktopia.
 */
 
namespace Serole\Booktopia\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Data extends AbstractHelper
{
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;
	
	
	protected $_storeManager;
	
	protected $_logger;

    /**
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor
    )
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
		$this->_storeManager = $storeManager;
		$this->createLog('booktopia.log');
    }
	
	/*
     * @return string
     */
    public function getApiUrl($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'booktopia/apidetails/apiurl',
            $scope
        );
    }

   /*
     * @return string
     */
    public function getApiUserName($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'booktopia/apidetails/apiuser',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getApiPassword($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'booktopia/apidetails/apipassword',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getWarningEmail($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'booktopia/apidetails/warningemail',
            $scope
        );
    }
	/*
	 * @calling booktopia API to get codes as per price and quantities 
     * @return string
     */
	public function getBooktopiaCodes($orderid, $sku, $price, $qty)
    {
	
	    $price = (int)$price;
		
        
		$this->_logger->info('Sending value:'.$price.' and Qty:'.$qty.' to Booktopia');

	    $apiURL = $this->getApiUrl();
		$apiUser = $this->getApiUserName();
		$apiPass = $this->getApiPassword();
		
	    $url = $apiURL."voucher/create?value=$price&quantity=$qty";
		
		$this->_logger->info('API User:'.$apiUser);

		$this->_logger->info('API Pass:'.$apiPass);

		$this->_logger->info('API URL:'.$url);


		
		$ch = curl_init( $url );
		
		$postData = json_encode( array( "username"=> $apiUser,  "password"=> $apiPass));
		
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData );
		
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		
		$this->_logger->info('Api Result:'.$result);
		
		$resultData = json_decode($result);
		
		if($resultData->success == 1)
		{
			return $resultData->vouchers;
		}
		else
		{
			$this->_logger->info('There is some issue with Booktopia API');
			$this->sendBooktopiaWarningEmail($orderid, $sku, $qty, $price);
			return false;
		}
    }
	/*
	 * @sending error email
     */
	public function sendBooktopiaWarningEmail($orderid, $sku, $invQty, $itmPrice)
	{
			$email = $this->getWarningEmail();
			
			$emailvars = array(
				'sku'	=> $sku,
				'order'		=> $orderid,
				'price' => $itmPrice,
				'qty' => $invQty
				);
			
			$emailTemplate = Mage::getSingleton('core/email_template')->load(53);
			
			$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name'));
			$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email'));
			$emailTemplate->send(
				$email,
				'Administrator',
				$emailvars);
		
	}
	/*
	 * @Writing log
     */
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}

}