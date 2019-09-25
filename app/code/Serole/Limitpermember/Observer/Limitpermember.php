<?php
namespace Serole\Limitpermember\Observer;

class Limitpermember implements \Magento\Framework\Event\ObserverInterface
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
  
  protected $_purchaseLimit;

  protected $_limitPerCustomer;
		
  protected $_limitPerMember;
		
  protected $_includeOldOrders;
		
  protected $_purchaseLimitDays;
  
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
		$this->createLog("limit-customer-purchase.log");
    }
  
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
	    $this->_logger->info("Limitcustomer started");
	  
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
  
    protected function getQtyInCart($productId, $quote){ 			    
		try {
		
		    //echo $quote->getId();
			
		    // How many of this product are in the cart? 
			$itemsCollection = $quote->getItemsCollection();
			
			$qtyInCart = 0;
			
			foreach ($itemsCollection as $item) {
				if ($item->getProduct()->getId() == $productId){
					$qtyInCart += $item->getQty();
				}
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
	
  private function getQtyOrdered($type,$productId, $storeId, $websiteId){   
  
		try {
		    $qtyPrevOrdered = 0;
			
			$customer = $this->_customer->getCustomer();
			$memberNo = $customer->getData('memberno');
			$custId = $customer->getData('entity_id');
			
			$this->_logger->info("memberNo:".$memberNo);
			$this->_logger->info("custId:".$custId);

			$customerIds = [];
			
			if ($custId != ''){		
			
			     if($type == "member" && $memberNo != '' && isset($memberNo))
				  { 
					// Find all the customers with this member no
						$customers = $this->_customerFactory->create()
											->getCollection()
											->addAttributeToFilter('website_id',array('eq' => $websiteId))
											->addAttributeToFilter('memberno',array('eq' => $memberNo));
											
						foreach ($customers as $customer) {
							$customerIds[] = $customer->getId();
						}  
				  }
                 elseif($type == "customer" && $custId != '' && isset($custId))
				 {
					 $customerIds[] = $custId;
				 }					 
				 if (count($customerIds) > 0){
					 
					$this->_logger->info("customerIds:".implode(",",$customerIds));
					
					$days = "-".$this->_purchaseLimitDays." days";
					
					$this->_logger->info("days:".$days);
					
					if(isset($this->_purchaseLimitDays) && $this->_purchaseLimitDays != '')
					{
					    $orders = $this->_orderCollectionFactory
						                    ->create()
											->addFieldToFilter('created_at', array(
																'from'     => strtotime($days, time()),
																'to'       => time(),
																'datetime' => true
															))		                			    
											->addFieldToFilter('customer_id',array('in'=>$customerIds))
											->addFieldToFilter('status','complete');
					}
					else
					{
						$orders = $this->_orderCollectionFactory
											->create()          			    
											->addFieldToFilter('customer_id',array('in'=>$customerIds))
											->addFieldToFilter('status','complete');
					}
					
					if(isset($orders) and count($orders) > 0)
					{	 
				      $this->_logger->info("order counts:".count($orders));
					  foreach ($orders as $order) {
							$items = $this->_orderItemCollectionFactory->create()
												->addAttributeToFilter('order_id',$order->getId())
												->addAttributeToFilter('product_id',$productId);
							foreach ($items as $item) {											
								$qtyPrevOrdered += $item->getQtyInvoiced();
							}
						}
					}
				 }
				    
			}
		} catch (Exception $e){
			 $this->_logger->info("Exception while getting old order Qty".$e->getMessage());	    			    	
		}   			    					
		return $qtyPrevOrdered;
    }

	protected function _goBack($message)
	{
		
	   $this->_messageManager->addError($message);
		
		//to stop further processing
		$this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
		
		$result = [];
		
		//when need to redirect uncomment below line
		header("location:".$this->_redirect->getRefererUrl());
		exit;
		/*
		return $this->_controller->getResponse()->representJson(
				$this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
		);
		*/
	}
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}