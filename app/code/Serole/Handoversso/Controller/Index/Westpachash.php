<?php
/**
 * Created by Serole(Dk) on 16/11/2018.
 * For SSO integration
 */
namespace Serole\Handoversso\Controller\Index;

use Magento\Framework\App\Action\Context;

class Westpachash extends \Magento\Framework\App\Action\Action
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
        parent::__construct($context);
    }
	
	/**
     * SSO integration for different store
     */
    public function execute()
    {
		$this->createLog('westpachash_handoversso.log');
		
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
		
		$websiteId = $this->_helper->getWebsiteId(); 
		$store = $this->_helper->getStore();
		$storeId = $this->_helper->getStoreId();
		
		$this->_logger->info("IP: ".$_SERVER['REMOTE_ADDR']);
	    
		$affiliateId = $this->_helper->getConfigValue('handoversso_westpac/general_westpac/westpac_affiliateid',$this->_helper->getStoreId());
		
		$this->_logger->info("affiliateId: ".$affiliateId);
		
		$privateKey = $this->_helper->getConfigValue('handoversso_westpac/general_westpac/westpac_privatekey',$this->_helper->getStoreId());
		
		$this->_logger->info("privateKey: ".$privateKey);
		
		$hashexpiry = $this->_helper->getConfigValue('handoversso_westpac/general_westpac/westpac_hashexpiry',$this->_helper->getStoreId());
		
		$this->_logger->info("hashexpiry: ".$hashexpiry);
		
		$path = $this->_storeManager->getStore()->getBaseUrl();
		
		if(!isset($affiliateId) || $affiliateId == ''  || !isset($privateKey) || $privateKey == '')
		{
			$this->messageManager->addError('There is some error. Kindly contact site admin'); 
			
			$this->_logger->info("Required settings are missingRedirecting to: ".$path);																	
			header("Location:$path");
			exit;
		}
		
		$timeStamp = urldecode($this->getRequest()->getParam('ts'));
		
		$hash = urldecode($this->getRequest()->getParam('hash'));
		
		if(!isset($timeStamp) || $timeStamp == ''  || !isset($hash) || $hash == '')
		{
			$this->messageManager->addError('Required parameters are not available'); 
			
			$this->_logger->info("Required parameters are missing Redirecting to: ".$path);																	
			header("Location:$path");
			exit;
		}
		
		$hashString = $affiliateId.$timeStamp.$privateKey;
		
		$hashData = strtolower(hash('sha256', $hashString));
		
		$this->_logger->info("Generated Hash: ".$hashData);
		
		$hashRecieved = strtolower($hash);
		
		$this->_logger->info("Recieved Hash: ".$hashRecieved);
		
		if($hashData != $hashRecieved)
		{
			$this->messageManager->addError('Wrong Hash data'); 
			
			$this->_logger->info("Hash did not match");																	
			header("Location:$path");
			exit;

		}
		
		if(!isset($hashexpiry) || $hashexpiry =='')
			$hashexpiry = 10;
		
		$checkTimestamp = $this->validate_url_timestamp($timeStamp,$hashexpiry);
		
		if(!$checkTimestamp)
		{
				$this->messageManager->addError('Hash has been expired. Kindly try again.'); 
				
				$this->_logger->info("Has has expired or timestam has wron format: ".$path);																	
				header("Location:$path");
				exit;
		}
		
             $email = md5($hashData)."@neatideas.com.au";
			 
				
				$customer =  $this->_customer
								  ->setWebsiteId($websiteId)
								  ->loadByEmail($email);

				if ($customer->getId()) {
			        $this->_logger->info('Customer Located customer id: '.$customer->getId());
				} else {
					$this->_logger->info("Customer not found, creating new account in website:".$websiteId);

                    $customer = $this->_customer
									 ->setWebsiteId($websiteId)
									 ->setStore($store)
									 ->setFirstname('TBC')
									 ->setLastname('TBC')
									 ->setEmail($email)
									 ->setPassword(sha1("NI@12345"));
									 
					if ($customer->save()){
						$this->_logger->info("Account created");
					} else {
						$this->_logger->info("Account creation FAILED");
					}								
					
				}
				if ($customer->getId()){
					$this->_customerSession->setCustomer($customer);
					$this->_customerSession->setCustomerAsLoggedIn($customer);
					$this->_eventManager->dispatch('customer_login', array('customer'=>$customer));
					$this->messageManager->addSuccess("You have successfully logged in");				
					$this->_logger->info("SSO SUCCESSFUL");						
				}
				
				$this->_logger->info("After Customer creation Redirecting to: ".$path);																	
				header("Location:".$path);
				exit();

    }
	
	public function validate_url_timestamp($date, $limit) {
	    $array_1 = explode("_", $date);
		if (count($array_1) == 2) {
			
			$main_date =  date("Y-m-d H:i:s", \DateTime::createFromFormat('Ymd_His', $date)->getTimestamp());
			
			$city = "Australia/Melbourne";
			$timezone = new \DateTimeZone($city);
			$currentTime = new \DateTime('now', $timezone);
			$currentTime->format('U = Y-m-d H:i:s');
			$currentTime = $currentTime->format('Y-m-d H:i:s');
			$start = strtotime($main_date);
            $end = strtotime($currentTime);
            $diff = ($end-$start)/60; 
			if($diff<=$limit){ 
				return true;
			}		
		}
        return false;
    }
	
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}