<?php
namespace Serole\Smsauth\Observer;

class Smsauth implements \Magento\Framework\Event\ObserverInterface
{
   protected $_objectManager;
  
  protected $_productRepository;
  
  protected $_messageManager;
  
  protected $_redirect;
  
  protected $_actionFlag;
  
  protected $_quote;
  
  protected $_customer;
  
  protected $_customerFactory;
  
  protected $_orderCollectionFactory;
  
  protected $_orderItemCollectionFactory;
  
  protected $_CartAmountLimit;

  protected $_CartQtyLimit;
		
  protected $_daysLimit;
  
  protected $_controller;
  
  protected $_logger;
  
  protected $_checkoutSession;
  
  protected $_url;
  
   public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository, 
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Magento\Framework\App\ActionFlag $actionFlag,
		\Magento\Framework\UrlInterface $url,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
    ) {
		$this->_checkoutSession = $checkoutSession;
        $this->_productRepository = $productRepository;
		$this->_messageManager = $messageManager;
		$this->_redirect = $redirect;
		$this->_actionFlag = $actionFlag;
		$this->_url = $url;
		$this->_quote = $checkoutSession->getQuote();
		$this->_customer = $customerSession;
		$this->_customerFactory = $customerFactory;
		$this->_orderCollectionFactory = $orderCollectionFactory;
		$this->_orderItemCollectionFactory = $orderItemCollectionFactory;
		$this->createLog("sms-auth.log");
    }
  
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
	    $this->_logger->info("SMS Auth started");
	  
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		
		$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
		
		$storeId     = $storeManager->getStore()->getId();
		
		$this->_logger->info("storeId:".$storeId);
		
		$websiteId = $storeManager->getWebsite()->getWebsiteId();
		
		$cartItems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
		
		foreach($cartItems as $item)
		{
			$sku = $item->getSku();
			$this->_logger->info("sku:".$sku);
			$product = $this->_productRepository->get($sku);
			$productId = $product->getId();
			$this->_logger->info("productId:".$productId);
			
			$qty = $item->getQty();
			
			$this->_logger->info("SKU:".$sku." requestedQty:".$qty);
			
			$this->_purchaseLimit = $product->getData('purchase_limit');
		
			$this->_logger->info("SKU:".$sku." purchaseLimitEnabled:".$this->_purchaseLimit);

			$this->_limitPerCustomer = $product->getData('limit_per_customer');
			
			$this->_limitPerMember = $product->getData('limit_per_member');
			
			$this->_includeOldOrders = $product->getData('limit_per_member_inclusive');
			
			$this->_purchaseLimitDays = $product->getData('ni_product_limit_repurchase');
			
			if($this->_purchaseLimit && isset($this->_purchaseLimit))
		    {
				$this->_logger->info("SKU:".$sku." limitPerCustomer:".$this->_limitPerCustomer);
				$this->_logger->info("SKU:".$sku." limitPerMember:".$this->_limitPerMember);
				$this->_logger->info("SKU:".$sku." includeOldOrders:".$this->_includeOldOrders);
				$this->_logger->info("SKU:".$sku." purchaseLimitDays:".$this->_purchaseLimitDays);
				
				if(isset($this->_limitPerCustomer) && $this->_limitPerCustomer != '' && $qty > $this->_limitPerCustomer)
				{
					$this->_logger->info("SKU:".$sku." Added to cart + requested Qty:".$qty." is greater than customer limit:".$this->_limitPerCustomer);
					$message = "You can not purchase more than ".$this->_limitPerCustomer." quantity per customer.";
					$this->_messageManager->addError($message);
					$redirectionUrl = $this->_url->getUrl('checkout/cart/index?skus='.$sku);
					header("Location:$redirectionUrl");
					exit;
				}
				
				if(isset($this->_limitPerMember) && $this->_limitPerMember != '' && $qty > $this->_limitPerMember)
				{
					$this->_logger->info("SKU:".$sku." Added to cart + requested Qty:".$qty." is greater than member limit:".$this->_limitPerMember);
					
					$message = "You can not purchase more than ".$this->_limitPerMember." quantity per member no.";
					$this->_messageManager->addError($message);
					$redirectionUrl = $this->_url->getUrl('checkout/cart/index?skus='.$sku);
					header("Location:$redirectionUrl");
					exit;
				}
				
				if($this->_includeOldOrders && isset($this->_includeOldOrders))
				{
					
					$oldOrdersPerCustomer = $this->getQtyOrdered("customer", $productId, $storeId, $websiteId);
					
					$this->_logger->info("oldOrdersPerCustomer:".$oldOrdersPerCustomer);
					
					$totalQtyForCustomer = $qty + $oldOrdersPerCustomer;
					
					$this->_logger->info("Cart + Old orders for customer:".$totalQtyForCustomer);
					
					if($totalQtyForCustomer > $this->_limitPerCustomer && $this->_limitPerCustomer)
					{
						$message = "You can not purchase more than ".$this->_limitPerCustomer." quantity per customer.";
						$this->_messageManager->addError($message);
						$redirectionUrl = $this->_url->getUrl('checkout/cart/index?skus='.$sku);
						header("Location:$redirectionUrl");
						exit;
					}	
					
					$oldOrdersPerMember = $this->getQtyOrdered("member", $productId, $storeId, $websiteId);
					
					$this->_logger->info("oldOrdersPerMember".$oldOrdersPerMember);
					
					$totalQtyForMember = $qty + $oldOrdersPerMember;
					
					$this->_logger->info("Cart + Old orders for member:".$totalQtyForMember);
					
					if(($totalQtyForMember > $this->_limitPerMember) && $this->_limitPerMember)
					{
						$message = "You can not purchase more than ".$this->_limitPerMember." quantity per member no.";
						$this->_messageManager->addError($message);
						$redirectionUrl = $this->_url->getUrl('checkout/cart/index?skus='.$sku);
						header("Location:$redirectionUrl");
						exit;
					}
				}
			}
				
		}

		return $this;
  }

   protected function getQtyInCart($quote){ 			    
		try {
			$itemsCollection = $quote->getItemsCollection();
			$qtyInCart = 0;
			foreach ($itemsCollection as $item) {
				
					$qtyInCart += $item->getQty();
			}
		} catch (Exception $e){
           $this->_logger->info("Exception while getting cart Qty".$e->getMessage());			
		}		    		
		return $qtyInCart;
    }
  
  protected function getProduct($id, $storeId) {
        $prod = $this->_productRepository->getById($id, false, $storeId);
        return $prod;
    }
	
  private function getPastOrdered($type,$productId, $storeId, $websiteId){   
  
		try {
		    
			$customer = $this->_customer->getCustomer();
			$custId = $customer->getData('entity_id');
			
			$this->_logger->info("custId:".$custId);

			$customerIds = [];
			
			if ($custId != ''){	
			
					$customerIds[] = $custId;
					if(isset($this->_daysLimit) && $this->_daysLimit != '')
					{
						$days = "-".$this->_daysLimit." days";
						
					}
					else
					{
						$days = "-1 days";
					}						
					
						$this->_logger->info("days:".$days);
					    $orders = $this->_orderCollectionFactory
						                    ->create()
											->addFieldToFilter('created_at', array(
																'from'     => strtotime($days, time()),
																'to'       => time(),
																'datetime' => true
															))		                			    
											->addFieldToFilter('customer_id',array('in'=>$customerIds))
											->addFieldToFilter('status','complete');
					
					
					if(isset($orders) and count($orders) > 0)
					{	 
				      return true;
					}
					else
						return false;    
			}
		} catch (Exception $e){
			 $this->_logger->info("Exception while getting old order Qty".$e->getMessage());	    			    	
		}
    }

	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}