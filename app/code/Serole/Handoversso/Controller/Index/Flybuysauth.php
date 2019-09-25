<?php
/**
 * Created by Serole(Dk) on 16/11/2018.
 * For SSO integration
 */
namespace Serole\Handoversso\Controller\Index;

use Magento\Framework\App\Action\Context;

class Flybuysauth extends \Magento\Framework\App\Action\Action
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
		
		$this->createLog('flybuys_handoversso.log');
		
		$tz = "Australia/Melbourne";
		$timezone = new \DateTimeZone($tz);
		$currentTime = new \DateTime('now', $timezone);
		$currentDate = $currentTime->format('Ymd');
		
		$privateKey = $this->_helper->getConfigValue(
            "handoversso_flybuys/general_flybuys/flybuys_privatekey",
            $this->_helper->getStoreId()
        );
		
		$path = $this->_storeManager->getStore()->getBaseUrl();
		
		$username = urldecode($this->getRequest()->getParam('username'));
		$timestamp = urldecode($this->getRequest()->getParam('timestamp'));
		$signature = urldecode($this->getRequest()->getParam('signature'));
		$cat = trim($this->getRequest()->getParam('cat'));

		if ($username == '' || $timestamp == '' || $signature == '') {
			$this->messageManager->addError("Request parameter are missing.");
			$this->_redirect("/");
			return true;
		}
		
		if (!$this->validate_url_timestamp($timestamp)) {
			$this->_logger->info('Innvalid timeStamp: ' . $timestamp);
			$this->messageManager->addError("Expired/Invalid TimeStamp");
			$this->_redirect("/");
			return true;
		}

		if (!$this->validate_username($username)) {
			$this->_logger->info('Innvalid usernaame: ' . $username);
			$this->messageManager->addError("Invalid proxy ID");
			$this->_redirect("/");
			return true;
		}
		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $signature)){
			 $this->_logger->info('Spacial characters in signature: ' . $signature);
			 $this->messageManager->addError("Spacial characters in signature");
			 $this->_redirect("/");
			 return true;
		}
		
		if(!isset($privateKey) || $privateKey == ''){
			$this->messageManager->addError('There is some error. Kindly contact site admin'); 
			$this->_logger->info("Required privet key settings are missingRedirecting to: ".$path);
		    header("Location:$path");
			exit;
		}
		
		
		$websiteId = $this->_helper->getWebsiteId(); 
		$store = $this->_helper->getStore();
		$storeId = $this->_helper->getStoreId();

		$hmac_data = $this->token($username, $timestamp, $privateKey);

		if($hmac_data != $signature){
			$this->messageManager->addError('HMAC does not match or invalid'); 
			$this->_logger->info("$hmac_data HMAC does not match to $signature or invalid");
		    header("Location:$path");
			exit;
		}

		$usrName = md5($username);
		$email = $usrName."@neatideas.com.au";
		
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
							 ->setFirstname("TBC")
							 ->setLastname("TBC")
							 ->setEmail($email)
							 ->setMemberno($username)
							 ->setPassword(sha1($username));
						
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
			
			if(isset($cat) && $cat !=''){ 
			  try{
					    $category = $this->_categoryRepository->get($cat, $this->_helper->getStoreId());
						$categoryUrl= $category->getUrl();
						$path=$categoryUrl;
						header("Location:".$path);
						exit();
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
	
	private function token($username, $timestamp, $privateKey) {
	
        $timestamp = str_replace(array(" ", "%20" ), array("+"), $timestamp);
        $str = "&username=" . $username . "&timeStamp=" . $timestamp;
        return $hmac = hash_hmac('sha3-256', $str, $privateKey); 
    }	
	
	public function validate_username($string) {
		$count = 0;
		preg_replace("/[^a-zA-Z0-9]/", "", $string, -1, $count);
		if ($count != 0) {
			return false;
		}
		if (strlen($string) != 80) {
			return false;
		}
		return true;
	}

	public function validate_url_timestamp($date) {
		$array_1 = explode("_", $date);
		if (count($array_1) == 2) {
			$array_2 = explode("-", $array_1[1]);
			if (count($array_2) == 4) {
				$time = $array_2[0] . ":" . $array_2[1] . ":" . $array_2[2];
				$main_date = $array_1[0] . " " . $time;
				if (date('Y-m-d H:i:s', strtotime($main_date)) == $main_date) {
					$city = "Australia/Melbourne";
					$timezone = new \DateTimeZone($city);
					$currentTime = new \DateTime('now', $timezone);
					$currentTime->format('U = Y-m-d H:i:s');
					$currentTime = $currentTime->format('Y-m-d H:i:s');
					$start = strtotime($main_date);
					$end = strtotime($currentTime);
					$diff = $end-$start; 
					
					if($diff<300){ 
						return true;
					}
					return true;
				}
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