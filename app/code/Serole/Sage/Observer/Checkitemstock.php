<?php
namespace Serole\Sage\Observer;

use \Magento\Checkout\Model\Session as CheckoutSession;

class Checkitemstock implements \Magento\Framework\Event\ObserverInterface
{
	
  protected $_checkoutSession;
  
  protected $_inventory;
  
  protected $_messageManager;
  
  protected $_objectManager;
  
  protected $_responseFactory;
  
  protected $_url;
  
  protected $_logger;
  
  protected $_helper;
  
  protected $_sku = array();
  
  protected $_skuQty = array();
  
  
  public function __construct(
        CheckoutSession $checkoutSession,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
		\Serole\Sage\Helper\Data $helper,
		\Serole\Sage\Model\Inventory $inventory
		) 
	{
        $this->_checkoutSession = $checkoutSession;
		$this->_inventory = $inventory;
		$this->_messageManager = $messageManager;
		$this->_responseFactory = $responseFactory;
        $this->_url = $url;
		$this->_helper = $helper;
		$this->createLog('sage_Inventory_checkout.log'); 
    }
  
  public function execute(\Magento\Framework\Event\Observer $observer)
	{
		//$dataA = array("quote1,order1,ACE-MOV-ND-CHI-001,3","quote1,order1,ACE-MOV-NP-DEL-001,4");
		//$this->_inventory->getSerilaCodes($dataA);
		//exit;
		$cartItems = $this->_checkoutSession->getQuote()->getAllVisibleItems();
		$items = array();
		$i = 0;
		foreach($cartItems as $item)
		{
			$items[$i]['identifier'] = $item->getSku();
			$items[$i]['type'] = "sku";
			$items[$i]['qty'] =	$item->getQty();
            $i++;			
		}
		
		$result = $this->_inventory->getSageStockCheck($items);

		 if(isset($result['error']) && $result['error'] == 1)
		 {
			$this->_logger->info('Error skus:'.$result["errorSkus"]);
			
			$message = $result["errorString"];
			$this->_messageManager->addError($message);
			$redirectionUrl = $this->_url->getUrl('checkout/cart/index?skus='.$result["errorSkus"]);
			$this->_logger->info('skus:'.$result["errorSkus"]);
			header("Location:$redirectionUrl");
			exit;
		 }
		 
		 return $this;
	}
  
  public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}

}