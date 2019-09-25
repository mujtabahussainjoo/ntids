<?php
namespace BtSuperApp\CustomModule\Model;
use BtSuperApp\CustomModule\Api\HelloInterface;
use \Magento\Framework\App\Bootstrap;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order;
use Braintree_Customer;
use Braintree_Configuration;
use Braintree_Transaction;
use Braintree_CreditCard;
use Braintree_ClientToken;
//use Surcharge;

class Hello implements HelloInterface
{
	//private $customer_id;
	private $wishlist;
	protected $_resourceConnection;
	protected $_connection;
	protected $request;
	protected $typeFactory;
	private $surcharge;
	private $surchargeConfigHelper;
	
	private $environment = 'sandbox';
	private $merchantId = 's267rydcq9f3g9g7';
	private $publicKey = 'kv6r8tn639rbfzpx';
	private $privateKey = '8d270ea4a88cade9026d143e0d67977a';
		
	public function __construct(
		\Magento\Wishlist\Model\Wishlist $wishlist,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Framework\App\RequestInterface $request,
		
		\Magento\Quote\Model\QuoteFactory $quoteFactory,
		\Magento\Quote\Model\ResourceModel\Quote $quoteModel,
		\Magento\Quote\Model\Quote $quote,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Customer\Model\AddressFactory $addressFactory,
		\Magento\Braintree\Model\Adapter\BraintreeAdapterFactory $bt_adapter,
		
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
		\Magento\Theme\Block\Html\Header\Logo $logo,
		\Fooman\Surcharge\Model\TypeFactory $typeFactory,
		\Magento\Checkout\Model\Cart $cart,
		
		\Fooman\Surcharge\Helper\SurchargeConfig $surchargeConfigHelper,
		\Fooman\Surcharge\Api\SurchargeInterface $surcharge
	) {
		$this->wishlist = $wishlist;
		$this->_resourceConnection = $resourceConnection;
		$this->_connection = $this->_resourceConnection->getConnection();
		$this->request = $request;
		$this->quote = $quote;
		
		$this->quoteFactory = $quoteFactory;
		$this->quoteModel=$quoteModel;
		$this->bt_adapter = $bt_adapter;
		
		$this->_customerFactory = $customerFactory;
        $this->_addressFactory = $addressFactory;
		
        $this->quoteManagement = $quoteManagement;        
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
		$this->_logo = $logo;
		$this->typeFactory = $typeFactory;
		$this->cart = $cart;
		
		$this->surcharge = $surcharge;
		$this->surchargeConfigHelper = $surchargeConfigHelper;
	}

	public function displayWishlist($customer_id) {
		
		$wishlist_collection = $this->wishlist->loadByCustomerId($customer_id, true)->getItemCollection();

		$wishlist_item = array();
		$i=1;
		foreach ($wishlist_collection as $item) {
			$productName = $item->getProduct()->getName();
			$productId = $item->getProduct()->getId();
			$WishlistItemId = $item->getWishlistItemId();
			$wishlist_item[$i]['prod_name'] = $productName;
			$wishlist_item[$i]['prod_id'] = $productId;
			$wishlist_item[$i]['quantity'] = '0';
			$wishlist_item[$i]['prod_price'] = '0';
			$wishlist_item[$i]['images'] = array();
			//$wishlist_item[$i]['WishlistItemId'] = $WishlistItemId;
			$i++; 
		}
		return $wishlist_item;  
	}
	
	public function getUserSettings($customer_id) {
		
		
		if(trim($customer_id) != '' && $customer_id > 0){
			$query = "Select * FROM mage_customer_setting where customer_id = '".$customer_id."' LIMIT 1";
			$collection = $this->_connection->fetchAll($query);
			$userSetting = array();
			if(count($collection) > 0)
			{
				$userSetting[0]['customer_id'] = $collection[0]['customer_id'];
				$userSetting[0]['proximity_notification'] = $collection[0]['proximity_notification'];
				$userSetting[0]['push_notification'] = $collection[0]['push_notification'];
				$userSetting[0]['geo_location'] = $collection[0]['geo_location'];
				$userSetting[0]['phone_number'] = $collection[0]['phone_number'];
				
				return $userSetting;
			} else {
			
				return $userSetting;
			}
		} else {
			return false;
		}
	}
	
	public function saveUserSetting($customer_id,$proximity_notification,$push_notification,$geo_location,$phone_number)
	{
		if(trim($customer_id) != '' && $customer_id > 0)
		{
			$query = "Select * FROM mage_customer_setting where customer_id = '".$customer_id."' LIMIT 1";
			$collection = $this->_connection->fetchAll($query);
			if(count($collection) > 0)
			{ 
				$qry = "UPDATE mage_customer_setting SET proximity_notification = '".$proximity_notification."', push_notification = '".$push_notification."', geo_location = '".$geo_location."', phone_number = '".$phone_number."' WHERE customer_id = '".$customer_id."'";
				$this->_connection->query($qry);
				return 'updated';
			} else {
				$qryInsert = "INSERT INTO mage_customer_setting SET proximity_notification = '".$proximity_notification."', push_notification = '".$push_notification."', geo_location = '".$geo_location."', phone_number = '".$phone_number."', customer_id = '".$customer_id."'";
				$this->_connection->query($qryInsert);
				return 'inserted';
			}
		} else {
			return false;
		}
		return false;
	}
	
	/*
	 * Params
	 * customer_id : customer id for registered users.
	 * Description : Generate customer token from braintree
	 */
	public function getGenerateToken($customerId) {
		//$config = $this->surchargeConfigHelper;
		//print_r($this->surchargeConfigHelper->getConfig('percent')); exit;
		
		if ($customerId != "") {
			Braintree_Configuration::environment ( $this->environment );
			Braintree_Configuration::merchantId ( $this->merchantId );
			Braintree_Configuration::publicKey ( $this->publicKey );
			Braintree_Configuration::privateKey ( $this->privateKey );
			
			/*Braintree_Configuration::environment ( 'sandbox' );
			Braintree_Configuration::merchantId ( 's267rydcq9f3g9g7' );
			Braintree_Configuration::publicKey ( 'kv6r8tn639rbfzpx' );
			Braintree_Configuration::privateKey ( '8d270ea4a88cade9026d143e0d67977a' );*/
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
			
			$query = "Select * FROM mage_customer_setting where customer_id = '".$customerId."' LIMIT 1";
			$collection = $this->_connection->fetchAll($query);
			
			if (count ( $collection ) == 0) {
				$result = Braintree_Customer::create ( [ 
						'firstName' => $customerObj->getFirstname (),
						'lastName' => $customerObj->getLastname (),
						'company' => ' ',
						'email' => $customerObj->getEmail (),
						'phone' => '',
						'fax' => '',
						'website' => '' 
				] );
				
				if ($result->success) {
					$returnArray ['braintree_customer_id'] = $result->customer->id;
				}
				
				$returnArray ['client_token'] = Braintree_ClientToken::generate ( [ 
					"customerId" => $returnArray ['braintree_customer_id'] 
				] );
				
				$insert = "INSERT mage_customer_setting SET customer_id='" . $customerId . "',proximity_notification='0',push_notification='0',geo_location='0',phone_number='',`client_token`='" . $returnArray ['client_token'] . "',braintree_customer_id='" . $returnArray ['braintree_customer_id'] . "',braintree_customer_register_token=''";
				$this->_connection->query($insert);
				
			} else {
				if(trim($collection[0]['braintree_customer_id']) != ''){
					$returnArray ['braintree_customer_id'] = $collection[0]['braintree_customer_id'];
					$returnArray ['client_token'] = $collection[0]['client_token'];
				} else {
					$result = Braintree_Customer::create ( [ 
						'firstName' => $customerObj->getFirstname (),
						'lastName' => $customerObj->getLastname (),
						'company' => ' ',
						'email' => $customerObj->getEmail (),
						'phone' => '',
						'fax' => '',
						'website' => '' 
					] );
					
					if ($result->success) {
						$returnArray ['braintree_customer_id'] = $result->customer->id;
					}
					
					$returnArray ['client_token'] = Braintree_ClientToken::generate ( [ 
						"customerId" => $returnArray ['braintree_customer_id'] 
					] );
					$insert = "UPDATE mage_customer_setting SET `client_token`='" . $returnArray ['client_token'] . "',braintree_customer_id='" . $returnArray ['braintree_customer_id'] . "',braintree_customer_register_token='' WHERE customer_id='" . $customerId . "'";
					$this->_connection->query($insert);
				}
			}
			
			
			/*if (count ( $collection ) == 0) {
				$insert = "INSERT mage_customer_setting SET customer_id='" . $returnArray ['braintree_customer_id'] . "',proximity_notification='0',push_notification='0',geo_location='0',phone_number='',`client_token`='" . $returnArray ['client_token'] . "',braintree_customer_id='" . $returnArray ['braintree_customer_id'] . "',braintree_customer_register_token=''";
			} else {
				$insert = "UPDATE mage_customer_setting SET `client_token`='" . $returnArray ['client_token'] . "',braintree_customer_id='" . $returnArray ['braintree_customer_id'] . "',braintree_customer_register_token='' WHERE customer_id='" . $returnArray ['braintree_customer_id'] . "'";
			}*/
			//$this->_connection->query($insert);
			//print_r($returnArray); exit;
			return json_encode ( $returnArray );
		} else {
			return "Customer ID  is missing";
		}		
	}
	
	public function getLogoUrl(){
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();		
		$logo = $objectManager->get('\Magento\Theme\Block\Html\Header\Logo');
		echo $logo->getLogoSrc(); exit;
	}
	
	/*
	 * Params
	 * customer_id : customer id for registered users.
	 * nonce_from_client : customer nonce from braintree
	 * amount : total amount from the cart
	 * save_my_card : 0 or 1, add card details in the braintree would be 1
	 * newsletter : 0 or 1, subscribe specific user to newsletter would be 1
	 * Description : confirm purchase for generate order 
	 */
	public function getConfirmPurchase($customerId,$websiteId,$store,$nonce_from_client,$amount,$save_my_card,$newsletter) {
		Braintree_Configuration::environment ( $this->environment );
		Braintree_Configuration::merchantId ( $this->merchantId );
		Braintree_Configuration::publicKey ( $this->publicKey );
		Braintree_Configuration::privateKey ( $this->privateKey );
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerObj = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
		//$customerEmail = $customerObj->getEmail();
		
		$quote = $this->quoteFactory->create();
		$customerQuote=$this->quoteModel->loadByCustomerId($quote,$customerId); // where `$customerId` is your `customer id`
		$items=$customerQuote;
		print_r($items->getData());
		exit;
		$quote = $this->quoteFactory->create()->getCollection()->addFieldToFilter('customer_id',$customerId);
		//print_r($quote->getData()); exit;
		$b_address = $customerObj->getPrimaryBillingAddress ();
		if( $b_address ) {
			$getId = $b_address->getId();
			$getFirstname = $b_address->getFirstname();
			$getLastname = $b_address->getLastname();
			$getStreet1 = $b_address->getStreet1();
			$getStreet2 = $b_address->getStreet2();
			$getCity = $b_address->getCity();
			$getCountryId = $b_address->getCountryId();
			$getRegion = $b_address->getRegion();
			$getPostcode = $b_address->getPostcode();
			$getTelephone = $b_address->getTelephone();
		} else {
			$getId = "";
			$getFirstname = "";
			$getLastname = "";
			$getStreet1 = "";
			$getStreet2 = "";
			$getCity = "";
			$getCountryId = "";
			$getRegion = "";
			$getPostcode = "";
			$getTelephone = "";
		}
		
		$billingAddress = array(
			'id' => $getId,
			'prefix' => '',
			'firstname' => $getFirstname,
			'middlename' => '',
			'lastname' => $getLastname,
			'suffix' => '',
			'company' => '',
			'street' => array (
				'0' => $getStreet1,
				'1' => $getStreet2
			),
			'city' => $getCity,
			'country_id' => $getCountryId,
			'region_id' => $getRegion,
			"region_code"=> $getCountryId,
			'region' => $getRegion,
			'postcode' => $getPostcode,
			'telephone' => $getTelephone,
			'fax' => '',
			'vat_id' => '',
			'save_in_address_book' => 1,
			'same_as_billing' => 1
		);
		
		//$quote->getBillingAddress()->addData($billingAddress);
		print_r($quote->getShippingAddress()); exit;
		$result = Braintree_Customer::create ( [ 
				'firstName' => $customerObj->getFirstname (),
				'lastName' => $customerObj->getLastname (),
				'company' => ' ',
				'email' => $customerObj->getEmail (),
				'phone' => '',
				'fax' => '',
				'website' => '' 
		] );
		
		if ($result->success) {
			$returnArray ['braintree_customer_id'] = $result->customer->id;
		}
		
		$returnArray ['client_token'] = Braintree_ClientToken::generate ( [ 
				"customerId" => $returnArray ['braintree_customer_id'] 
		] );
		
		
		
		/*$billingAddress = $quote->getBillingAddress ()->addData ( array (
				'customer_address_id' => $getId,
				'prefix' => '',
				'firstname' => $getFirstname,
				'middlename' => '',
				'lastname' => $getLastname,
				'suffix' => '',
				'company' => '',
				'street' => array (
					'0' => $getStreet1,
					'1' => $getStreet2
				),
				'city' => $getCity,
				'country_id' => $getCountryId,
				'region_id' => $getRegion,
				'region' => $getRegion,
				'postcode' => $getPostcode,
				'telephone' => $getTelephone,
				'fax' => '',
				'vat_id' => '',
				'save_in_address_book' => 1 
		) );*/
		
		//$billingAddress = $this->addressRepository->getById($billingAddressId);
		$bt_adapter = $this->bt_factory->create(); 
		$value = $bt_adapter->generate();
		print_r($value);
		exit;
		
	}
	
	
	public function changeOrderStatusToComplete($orderId)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order = $objectManager->create('Magento\Sales\Model\Order')->load($orderId);
		
		$orderState = Order::STATE_COMPLETE;
		$order->setState($orderState)->setStatus(Order::STATE_COMPLETE);
		$order->save();
		return $orderId;
	}
	
	public function getCustomPdfUrl($orderId){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order = $objectManager->create('Magento\Sales\Model\Order')->load($orderId);
		$pdfHelper = $objectManager->create('\Serole\Pdf\Helper\Pdf');
        $fileBasepath = $pdfHelper->getRootBaseDir()."/neatideafiles/";
        $fileBaseUrl = $pdfHelper->getDefaultBaseUrl()."/neatideafiles/";
        $filePath = $fileBasepath."pdf/".$order->getIncrementId().".pdf";
        $fileUrl = $fileBaseUrl."pdf/".$order->getIncrementId().".pdf";
		return $fileUrl;
	}
	
}
