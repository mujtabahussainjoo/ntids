<?php
/**
 * Created by Serole(Dk) on 16/11/2018.
 * For SSO integration
 */
namespace Serole\Handoversso\Controller\Index;

use Magento\Framework\App\Action\Context;

class Smallideastoken extends \Magento\Framework\App\Action\Action
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
		$this->createLog('smallideas_handoversso.log');
		
		$tz = "Australia/Melbourne";
		$timezone = new \DateTimeZone($tz);
		$currentTime = new \DateTime('now', $timezone);
		$currentDate = $currentTime->format('Ymd');
		
		$privateKey = $this->_helper->getConfigValue(
            "handoversso_smallideas/general_smallideas/smallideas_privatekey",
            $this->_helper->getStoreId()
        );
		
		$path = $this->_storeManager->getStore()->getBaseUrl();
		
		$SsoID = strtolower(urldecode($this->getRequest()->getParam('ssoid')));
		$emailId = trim(urldecode($this->getRequest()->getParam('email')));
		$urlType = urldecode($this->getRequest()->getParam('urlType'));
		$urlId = urldecode($this->getRequest()->getParam('urlId'));
		
		$websiteId = $this->_helper->getWebsiteId(); 
		$store = $this->_helper->getStore();
		$storeId = $this->_helper->getStoreId();
		
		
		if(!isset($privateKey) || $privateKey == ''){
			$this->messageManager->addError('There is some error. Kindly contact site admin'); 
			$this->_logger->info("Required privet key settings are missingRedirecting to: ".$path);
		    header("Location:$path");
			exit;
		}
		
		if($emailId == "" || !isset($emailId)){
			$this->messageManager->addError('Missing Email'); 
			$this->_logger->info("email not availble");
			header("Location:$path");
			exit;
		}

		$hashString = $privateKey.$currentDate.$emailId;
		$hashData = strtolower(hash('sha256', $hashString));
		$this->_logger->info("Generated Hash: ".$hashData);
		if($hashData != $SsoID){
			$this->messageManager->addError('Wrong Hash data'); 
			$this->_logger->info("Hash did not match");
			header("Location:$path");
			exit;
		}

		$email = $emailId;
		$customer =  $this->_customer
						  ->setWebsiteId($websiteId)
						  ->loadByEmail($email);
		
		if ($customer->getId()) {
			$this->_logger->info('Customer Located customer id: '.$customer->getId());
		} else {
			$this->_logger->info("Customer not found, creating new account in website:".$websiteId." using: ".$email);
				
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
			
			if($urlId!='' && $urlType!=''){ 
			  try{
					if($urlType=='product'){ 
							$productUrl = $this->_productloader->create()->load($urlId)->getProductUrl();;
							$path=$productUrl;
							header("Location:".$path);
							exit();
					}else if($urlType=='category'){ 
					    $category = $this->_categoryRepository->get($urlId, $this->_helper->getStoreId());
						$categoryUrl= $category->getUrl();
						$path=$categoryUrl;
						header("Location:".$path);
						exit();
					}
			    }
				 catch(\Exception $e){
					 $this->messageManager->addError($e->getMessage());
					 header("Location:".$path);
					 exit();
				}
			}
            else
			{
				header("Location:".$path);
				exit();
			}				
		}					

    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}