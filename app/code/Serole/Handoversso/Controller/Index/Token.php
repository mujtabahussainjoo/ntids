<?php
/**
 * Created by Serole(Dk) on 16/11/2018.
 * For SSO integration
 */
namespace Serole\Handoversso\Controller\Index;

use Magento\Framework\App\Action\Context;

class Token extends \Magento\Framework\App\Action\Action
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
	
	protected $_date;
	
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
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
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
		$this->_date = $date;
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
		
		$this->createLog('alintaWC_handoversso.log');
		
		$this->_logger->info('WC Token Action Called');	    

		$timeout = $this->_helper->getConfigValue('handoversso_alinta/general_alinta/timeout',$this->_helper->getStoreId());
		if (!$timeout || $timeout==''){
			$timeout = 30;
		}
		$this->_logger->info('Timeout set to '.$timeout);	    
				
	    // Check token is still valid 
		$key = $this->getRequest()->getParam('key') ;
		
		$this->_logger->info('Token Key: '.$key);

        $path = $this->_storeManager->getStore()->getBaseUrl();		
		
		$token = false;
	    if ($key && $key!=''){
			$token = $this->_token->load($key,'token');
		}
		
		$tokenData = $token->getData();
		
		if (!empty($tokenData)) {	
			$this->_logger->info('Token Id: '.$token->getId());	    	    	
			
			// Check it hasn't expired
			$this->_logger->info('Token Created: '.$token->getCreatedAt());	    	    				

			$token_time = $this->_date->timestamp($token->getCreatedAt());
			$now_time = $this->_date->timestamp(time());
			
			$minutes = ($now_time - $token_time)/60;
			
			if ($minutes > $timeout) {
				$this->_logger->info('Token has EXPIRED: '.$token->getId());	    	    					
				$token = false;
			}
			
		}	
	
		if (!empty($tokenData)) {
            $websiteId = $this->_helper->getWebsiteId(); 
		    $store = $this->_helper->getStore();
		    $storeId = $this->_helper->getStoreId();
			
			$email = $token->getEmail();
			if ($email){
				$email = $token->getEmail();				
			} elseif ($token->getSsoid()) {
				$ssoid = $token->getSsoid();
				$this->_logger->info('No email passed - attempting to locate using SSOID passed');
				$customer = $this->_customer
								 ->setWebsiteId($websiteId)
								 ->getCollection()
								 ->addFieldToFilter('memberno', $ssoid);
								 
				if ($customer->getId()) {
					$email = $customer->getEmail();
					$this->_logger->info('Located customer - Email is: '.$email);					
				} else {
					$email = 'tempemail_'.$ssoid.'@alintaenergy.com.au';
				}
			}
						
			if ($email !=''){
				$this->_logger->info('Attempting to load by email: '.$email);	    	    				
				$customer = $this->_customer;
				$customer->setWebsiteId($websiteId);
				$customer->setStore($store);
				
				$customer->loadByEmail($email);
				if ($customer->getId()) {	
					$this->_logger->info('Customer Located');
					
					$customerChanged = false;
					if ($token->getFirstname()!='' 
						&& $token->getFirstname() != $customer->getFirstname()){
						$customer->setFirstname($token->getFirstname());
						$customerChanged = true;
					}
					if ($token->getLastname()!='' 
						&& $token->getLastname() != $customer->getLastname()){
						$customer->setLastname($token->getLastname());
						$customerChanged = true;
					}
					if ($token->getSsoid()!=''
						&& $token->getSsoid() != $customer->getMemberno()){
						$customer->setMemberno($token->getSsoid());
						$customerChanged = true;						
					}
					
					
					if ($customerChanged){
						$customer->save();
					}
					
				} else {
					$this->_logger->info("Customer not found, creating new account in website:$websiteId"." using: ".$email);
					
					if ($token->getFirstname()==''){
						$token->setFirstname('TBC');
					}
					if ($token->getLastname()==''){
						$token->setLastname('TBC');
					}
					
					$customer = $this->_customer;
					$customer->setWebsiteId($websiteId)
								->setStore($store)
								->setFirstname($token->getFirstname())
								->setLastname($token->getLastname())
								->setEmail($email)
						        ->setMemberno($token->getSsoid())
								->setPassword(sha1($key));				
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
	
				$this->_logger->info("Redirecting to: ".$path);																	
				header("Location:".$path);
				exit();
			}
	    } else {	
            $this->messageManager->addError("invalid-token");	    
			$this->_logger->info("invalid-token");
            header("Location:".$path);
				exit();			
	    }

    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}