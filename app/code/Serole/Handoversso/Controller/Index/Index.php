<?php
/**
 * Created by Serole(Dk) on 16/11/2018.
 * For SSO integration
 */
namespace Serole\Handoversso\Controller\Index;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
	/* logging */
	protected $_logger;
	
	protected $_storeManager;
	
	protected $_customer;
	
    protected $_customerFactory;
	
	protected $_customerSession;
	
	protected $_eventManager;
	
	Protected $_helper;
	
	   /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
		\Magento\Framework\Event\Manager $eventManager,
		\Serole\Handoversso\Helper\Data $Helper,
		\Magento\Customer\Model\Session $customerSession
    ) 
	{
		$this->_storeManager = $storeManager; 
        $this->_customerFactory = $customerFactory;
        $this->_customer = $customers;		
		$this->_customerSession = $customerSession;
		$this->_eventManager = $eventManager;
		$this->_helper = $Helper;
        parent::__construct($context);
    }
	
	/**
     * SSO integration for different store
     */
    public function execute()
    {
		$store_code = $this->_helper->getStoreCode();
        
        $success = false;
		$skipRedirect = false;
		$parms='';
		if ($store_code == 'rac_en' ) {	
		    if(isset($_GET['membernumber']) && isset($_GET['email']))
			{
				$this->validateRac($_GET['membernumber'], $_GET['email']);
			}
		  
		} elseif ($store_code == 'hbf_en' ) { 
			$success = $this->validateHBF();	
			
		}elseif ($store_code == 'racv' ) { 
			$success = $this->validateRACV();			
		} elseif ($store_code == 'acorns' ) {
			$success = $this->validateAcorns();	
		}
    }
	
	Public function validateRac($memberNumber='', $email='') {
		
			$this->createLog('rac_handoversso.log');
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
			
			if($memberNumber !='' && $email !='')
			{
				$memberNumber = $_GET['membernumber'];
				$email = $_GET['email'];
				
				$this->_logger->info("Member Number=".$memberNumber." Email=".$email);
				
				$store = $this->_storeManager->getStore();
				$websiteId = $this->_helper->getWebsiteId();
				$customerCollection = $this->getFilteredCustomerCollection($email, $memberNumber);
				$count = count($customerCollection);
				if($count == 1)
				{
					$this->_logger->info("customer found email=".$email." member No=".$memberNumber);
					foreach($customerCollection as $customer){
						$this->_customerSession->setCustomer($customer);
						$this->_customerSession->setCustomerAsLoggedIn($customer);
						$this->_eventManager->dispatch('customer_login', array('customer'=>$customer));
						$this->messageManager->addSuccess("You have successfully logged in");
						$this->_redirect("/");
					}
				}
				else
				{
					$this->_logger->info("customer not found. Creating new email=".$email." member No=".$memberNumber);
					try{
					    $this->_customer
							 ->setWebsiteId($websiteId)
							 ->setStore($store)
							 ->setFirstname('TBC')
							 ->setLastname('TBC')
							 ->setEmail($email)
							 ->setMemberno($memberNumber)
							 ->setPassword(sha1('TBC'.$memberNumber));
							 
						 $this->_customer->save();
						 
					    $customerCollection = $this->getFilteredCustomerCollection($email, $memberNumber);
						$count = count($customerCollection);
						if($count == 1)
						{
							foreach($customerCollection as $customer){
								$this->_customerSession->setCustomer($customer);
								$this->_customerSession->setCustomerAsLoggedIn($customer);
								$this->_eventManager->dispatch('customer_login', array('customer'=>$customer));
								$this->messageManager->addSuccess("You have successfully logged in");
								$this->_redirect("/");
								return true;
							}
							//print_r($customerCollection->getData());
						}
						else
							echo "Error";
					}
					catch (\Exception $e) {
						$this->_logger->info("exiting customer with different member no. email=".$email." member No=".$memberNumber);
						$this->_redirect("member-number-mismatch"); 	
					}
				}
			}
			else
			{
				$this->_logger->info("Member No. or Email is wrong or not provided");
				 $this->messageManager->addError("Member No. or Email is wrong or not provided");
				 $this->_redirect("/");
			} 
	}
	public function validateHBF() {
		
		$this->createLog('hbf_handoversso.log');   
		
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
		
    	$email 		= urldecode($this->getRequest()->getParam('User'));

    	$email 		= str_replace('%40','@',$email);


    	$member_no 	= urldecode($this->getRequest()->getParam('Member'));
    	$first_name = urldecode($this->getRequest()->getParam('First'));
    	$last_name 	= urldecode($this->getRequest()->getParam('Last'));    	    	    	 
    
    	// Attempt to load the customer and create if it doesn't exist
		try {				
			// Attempt to load the customer using the email supplied.
			$this->_logger->info("Attempting to load customer with email: ".$email);
			$websiteId = $this->_helper->getWebsiteId();
			$store = $this->_storeManager->getStore();
			$storeId = $this->_storeManager->getStore()->getId();
			$customer =  $this->_customer
							  ->setWebsiteId($websiteId)
							  ->loadByEmail($email);
			if ($customer->getId()){
				$this->_logger->info("Customer record identified: ".$customer->getId());
				// Just checking the member number matches 				
				if ($customer->getMemberno() != $member_no) {
					
                    $this->_logger->info("SSO FAILED - HBF Member Email Mismatch. Store has:".$customer->getMemberno().' Value passed:'.$member_no);
					
					$this->_logger->info("REDIRECTING TO: hbf_member_email_mismatch");
		
					$this->_redirect('hbf_member_email_mismatch');
					return false;					
				}
				
			} else {
				$this->_logger->info("Customer not found, creating new account in website "
							.$websiteId
							.' - store '
							.$storeId
							." using: ".$email." ".$member_no." ".$first_name." ".$last_name);
				
				         $customer = $this->_customer
							 ->setWebsiteId($websiteId)
							 ->setStore($store)
							 ->setFirstname($first_name)
							 ->setLastname($last_name)
							 ->setEmail($email)
							 ->setMemberno($member_no)
							 ->setPassword(sha1('TBC'.$member_no));
				
	
				if ($customer->save()){
					$this->_logger->info("Account created");
				} else {
					$this->_logger->info("Account creation FAILED");
				}	
			}
			if ($customer->getId()){	
			
		        $this->_logger->info("creating session and auto-logging in with customer id: ".$customer->getId());		
				
					$this->_customerSession->setCustomer($customer);
					$this->_customerSession->setCustomerAsLoggedIn($customer);
					$this->_eventManager->dispatch('customer_login', array('customer'=>$customer));
					$this->messageManager->addSuccess("You have successfully logged in");
					
				    $path = $this->_storeManager->getStore()->getBaseUrl().$this->getRequest()->getParam('path');
				
					$this->_logger->info("Redirecting to: ".$path);																	
					header("Location:".$path);
					exit();
					return true;
			} else {
				$this->_logger->info("SSO FAILED - Unable to establish customer");	
			}
		}
		catch (\Exception $e) {
			$this->_logger->info("ERROR: ".$e->getMessage());
		}				
		return false;
    }
	 
	public function validateRACV() {
		
        $this->createLog('racv_handoversso.log'); 

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

    	$member_no 	= urldecode($this->getRequest()->getParam('membernumber'));
    	$last_name 	= urldecode($this->getRequest()->getParam('surname')); 
		
		$this->_logger->info("Original Surname passed ".$last_name);    	    		
		
		if (strpos($last_name,"''")!==false){
			$last_name = str_replace("''","'",$last_name);
		}
		if (strpos($last_name,"%27")!==false){
			$last_name = str_replace("%27","'",$last_name);
		}
		if (strpos($last_name,"`")!==false){
			$last_name = str_replace("`","'",$last_name);
		}
		if (strpos($last_name,'"')!==false){
			$last_name = str_replace('"',"'",$last_name);
		}
    	$member_no 	= str_replace('%20',"",$member_no);
    	$last_name 	= str_replace('%20'," ",$last_name);    	
    	$last_name 	= str_replace('%252520'," ",$last_name);		
		if (is_numeric($member_no)){
			$member_no = intval($member_no);
		}
    	
    	try {
			
			$websiteId = $this->_helper->getWebsiteId();
			$store = $this->_storeManager->getStore();
			$storeId = $this->_storeManager->getStore()->getId();
			$this->_logger->info("Looking for: ".$member_no." ".$last_name);
			
			$result = $this->getCustomerForLastName($last_name, $member_no);
						
			$customer = false;			
			foreach($result as $r){ 
				$customer = $r;
			}
	
			
			if ($customer && $customer->getId()){
				$this->_logger->info("Customer record identified: ".$customer->getId());
			} else {
				
				// Check if we already have an account with that member # 				
				$result = $this->_customerFactory->create()->getCollection()
								->addAttributeToSelect("*")
								->addFieldToFilter("website_id", array("eq" => $websiteId))
								->addAttributeToFilter("memberno", array("eq" => $member_no))->load();
				$customer = false;			
				foreach($result as $r){ 
						$customer = $r;
				}
			}	
			
			if (!$customer){				
				$this->_logger->info("Customer not found, creating new account in website "
								.$websiteId." using memberNo: " .$member_no." and lastName: " .$last_name);
	
				$tempEmail = 'tempemail_racv'.str_replace(' ','',$member_no).'@neatideas.com.au';
	
								
				 $customer = $this->_customer
							 ->setWebsiteId($websiteId)
							 ->setStore($store)
							 ->setFirstname($last_name)
							 ->setLastname($last_name)
							 ->setEmail($tempEmail)
						     ->setMemberno($member_no)
							 ->setPassword(sha1('TBC'.$member_no));
								
		
				if ($customer->save()){
					$this->_logger->info("Account created");
				} else {
					$this->_logger->info("Account creation FAILED");
				}
			}	
			
			// Check the customer's surname with what was passed.. if it changed we 
			// need to get them to fix it up. 
			if ($customer->getId() && strtoupper($customer->getLastname()) != strtoupper($last_name)){
				
				$this->_logger->info("ERROR Surname mismatch: ".$customer->getLastname()."!=".$last_name);
				
				$this->_logger->info("SSO FAILED");	
				
				$path = $this->_storeManager->getStore()->getBaseUrl()."surname_mismatch";
				
				$this->_logger->info("Redirecting to: ".$path);																	
				header("Location:".$path);
				exit();
				return true;
			
			// We have located the customer .. log them in!
			} else if ($customer->getId()){
							
				$this->_logger->info("creating session and auto-logging in with customer id: ".$customer->getId());	
				
				$this->_customerSession->setCustomer($customer);
				$this->_customerSession->setCustomerAsLoggedIn($customer);
				$this->_eventManager->dispatch('customer_login', array('customer'=>$customer));
				$this->messageManager->addSuccess("You have successfully logged in");	            						                	            
				$this->_logger->info("SSO SUCCESSFUL");	
				
				$path = $this->_storeManager->getStore()->getBaseUrl().$this->getRequest()->getParam('path');
				
				$this->_logger->info("Redirecting to: ".$path);																	
				header("Location:".$path);
				exit();
				return true;
			} else {
				$this->_logger->info("SSO FAILED - Unable to establish customer");	
			}

		} catch (Exception $e) {
			$this->_logger->info("ERROR: ".$e->getMessage());
		}				
		return false;    
    
    }
	 
	 public function validateAcorns() {
		 
		$this->createLog('acorns_handoversso.log'); 

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

    	$uuid 		= urldecode($this->getRequest()->getParam('uuid'));
		$this->_logger->info("uuid:".$uuid);  
		 
		if (preg_match("/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i", $uuid)) {
			  $this->_logger->info("uuid is valid");  
		}
		else
		{
			$this->_logger->info("uuid is not valid");  
			$this->messageManager->addError("uuid is not valid");
			$this->_redirect("/");
			return true;
		}
		
		$websiteId = $this->_helper->getWebsiteId();
		$store = $this->_storeManager->getStore();
		$storeId = $this->_storeManager->getStore()->getId();
		
    	// Attempt to load the customer and create if it doesn't exist
		try {				
			// Attempt to load the customer using the email supplied.
			   $this->_logger->info("Attempting to load customer with uuid: ".$uuid);

			   $result = $this->_customerFactory->create()->getCollection()
								->addAttributeToSelect("*")
								->addFieldToFilter("website_id", array("eq" => $websiteId))
								->addAttributeToFilter("memberno", array("eq" => $uuid))->load();
				$customer = false;			
				foreach($result as $r){ 
						$customer = $r;
				}		
			if ($customer) {
				$this->_logger->info("Collection was returned, Id:".$customer->getId());
				
			} else {
				$this->_logger->info("Customer not found, creating new account in website:".$websiteId);
				
				 $customer = $this->_customer
							 ->setWebsiteId($websiteId)
							 ->setStore($store)
							 ->setFirstname('TBC')
							->setLastname('TBC')
							->setEmail('tempemail_acorns'.sha1($uuid).'@neatideas.com.au')
					        ->setMemberno($uuid)
							->setPassword(sha1('TBC'.$uuid));
							
	
				if ($customer->save()){
					$this->_logger->info("Account created");
				} else {
					$this->_logger->info("Account creation FAILED");
				}
			}
			if ($customer->getId()){			
				$this->_logger->info("Creating session and auto-logging in with customer id: ".$customer->getId());			
				
				$this->_customerSession->setCustomer($customer);
				$this->_customerSession->setCustomerAsLoggedIn($customer);
				$this->_eventManager->dispatch('customer_login', array('customer'=>$customer));
				$this->messageManager->addSuccess("You have successfully logged in");	            						                	            
				$this->_logger->info("SSO SUCCESSFUL");	
	
				
				
				$path = $this->_storeManager->getStore()->getBaseUrl().$this->getRequest()->getParam('path');
				
				$this->_logger->info("Redirecting to: ".$path);																	
				header("Location:".$path);
				exit();
				return true;
			} else {
				$this->_logger->info("SSO FAILED - Unable to establish customer");	
			}			
		}
		catch (\Exception $e) {
			$this->_logger->info("ERROR: ".$e->getMessage());
		}				
		return false;
	}

	/**
     * Get customer
     *
     * @return  customer object
     */
	
	public function getCustomerForLastName($lastname, $memberNo) {
		$websiteId = $this->_helper->getWebsiteId();
        return $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
				->addFieldToFilter("website_id", array("eq" => $websiteId))
                ->addAttributeToFilter("lastname", array("eq" => $lastname))
				->addAttributeToFilter("memberno", array("eq" => $memberNo))->load();
				
    }
	 
	 /**
     * Get customer
     *
     * @return  customer object
     */
	
	public function getFilteredCustomerCollection($email, $memberNo) {
		$websiteId = $this->_helper->getWebsiteId();
        return $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
				->addFieldToFilter("website_id", array("eq" => $websiteId))
                ->addAttributeToFilter("email", array("eq" => $email))
				->addAttributeToFilter("memberno", array("eq" => $memberNo))->load();
				
    }
	
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}