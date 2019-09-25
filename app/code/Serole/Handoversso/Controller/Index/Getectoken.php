<?php
/**
 * Created by Serole(Dk) on 16/11/2018.
 * For SSO integration
 */
namespace Serole\Handoversso\Controller\Index;

use Magento\Framework\App\Action\Context;

class Getectoken extends \Magento\Framework\App\Action\Action
{
	/* logging */
	protected $_logger;
	
	
	protected $_storeManager;
	
	protected $_customer;
	
    protected $_customerFactory;
	
	protected $_customerSession;
	
	protected $_eventManager;
	
	Protected $_helper;
	
	protected $_productloader;
	
	protected $_categoryRepository;
	
	Protected $_token;
	
	   /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
		\Magento\Framework\Event\Manager $eventManager,
		\Magento\Customer\Model\Session $customerSession,
		\Serole\Handoversso\Helper\Data $Helper,
		\Magento\Catalog\Model\ProductFactory $Productloader,
		\Serole\Handoversso\Model\Token $Token,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository
    ) 
	{
		$this->_storeManager = $storeManager; 
        $this->_customerFactory = $customerFactory;
        $this->_customer = $customers;		
		$this->_customerSession = $customerSession;
		$this->_eventManager = $eventManager;
		$this->_helper = $Helper;
		$this->_productloader = $Productloader;
		$this->_categoryRepository = $categoryRepository;
		$this->_token = $Token;
        parent::__construct($context);
    }
	
	/**
     * SSO integration for different store
     */
    public function execute()
    {
		$isSSOEnable = $this->_helper->getConfigValue(
            "handoversso_global/general_global/hsso_yesno",
            $this->_helper->getStoreId()
        );
		if(!$isSSOEnable)
		{
			$this->messageManager->addError("SSO has not been enabled. Kindly contact site admin.");
			$this->_redirect("/");
			return true;
		}
		
		$this->createLog('alintaEC_handoversso.log');
		
		$this->_logger->info("IP: ".$_SERVER['REMOTE_ADDR']);
    
	    $pass = true;
		
		// VALIDATE IP ADDRESS
		$ipaddressList = $this->_helper->getConfigValue('handoversso_alinta/general_alinta/ip_addresses',$this->_helper->getStoreId());
		
		if ($ipaddressList && $ipaddressList != '') { 
			$hostOK = false;
			$ipaddressArray = explode(',',$ipaddressList);
			foreach ($ipaddressArray as $ip){
				$this->_logger->info('Check Host: '.$ip);				
				if ($_SERVER['REMOTE_ADDR'] == trim($ip)){
					$this->_logger->info('Host found: '.$ip);
					$hostOK = true;
					break;
				}
			}
		} else {
			$hostOK = true;			
		}
		
		if (!$hostOK) {
			$this->_logger->info($_SERVER['REMOTE_ADDR'].' Host is not authorized: ');
			$pass=false;
		}
		
		// EMAIL
    	$email = urldecode($this->getRequest()->getParam('email'));
	    
		if ($email) {
			$this->_logger->info('Email: '.$email);
			$this->_token->setEmail($email);
			
		} elseif ($this->_helper->getConfigValue('handoversso_alinta/general_alinta/email_required',$this->_helper->getStoreId())){
			$this->_logger->info('Failed validation: Email is required');
			$pass = false;
		}

		// FIRSTNAME		
    	$firstname = urldecode($this->getRequest()->getParam('firstname'));
		if ($firstname) {
			$this->_logger->info('Firstname: '.$firstname);									
			$this->_token->setFirstname($firstname);
			
		} elseif ($this->_helper->getConfigValue('handoversso_alinta/general_alinta/firstname_required',$this->_helper->getStoreId())){
			$this->_logger->info('Failed validation: Firstname is required');
			$pass = false;
		}

		// LASTNAME
    	$lastname = urldecode($this->getRequest()->getParam('lastname'));
		if ($lastname) {
			$this->_logger->info('Lastname: '.$lastname);												
			$this->_token->setLastname($lastname);
			
		} elseif ($this->_helper->getConfigValue('handoversso_alinta/general_alinta/lastname_required',$this->_helper->getStoreId())){
			$this->_logger->info('Failed validation: Lastname is required');
			$pass = false;
		}


		// SSOID    	
    	$ssoid = urldecode($this->getRequest()->getParam('ssoid')); 
		
		$this->_logger->info('ssoid: '.$ssoid);
		
		if ($ssoid) {
			$this->_logger->info('SSOID: '.$ssoid);		
			$this->_token->setSsoid($ssoid);
			
		} elseif ($this->_helper->getConfigValue('handoversso_alinta/general_alinta/ssoid_required',$this->_helper->getStoreId())){
			$this->_logger->info('Failed validation: SSOID is required');
			$pass = false;
		}


		// FORMAT
    	$format = urldecode($this->getRequest()->getParam('format'));	    
		if ($format){
			$this->_logger->info('Format: '.$format);	
		}
		
		$path = $this->_storeManager->getStore()->getBaseUrl();

		// Did the request pass validation? 				
		if ($pass){
			$this->_token->generateToken(); 
			$this->_token->setStatus(1);
			$this->_token->save();
			$responseBody = $path.'handoversso/index/ectoken/key/'.$this->_token->getToken();
						
		} else {
			$this->getResponse()->setHttpResponseCode(400);
			$responseBody = 'Incorrect request parameters specified';
		}
		
		
		// Format == text/none
		if (!$format || $format == '' || $format == 'text'){
			$this->getResponse()->setHeader('Content-Type','application/text');
			$this->getResponse()->setBody($responseBody);

		// Format == json
		} elseif ($format == 'json'){
			$this->getResponse()->setHeader('Content-Type','application/json');
			$this->getResponse()->setBody('{"url":"'.$responseBody.'"}');

		// Format == xml			
		} elseif ($format == 'xml') {
			$this->getResponse()->setHeader('Content-Type:','application/xml');
			$this->getResponse()->setBody('<?xml version="1.0"?>'.chr(10).'<url>'.$responseBody.'</url>');			
		}

    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}