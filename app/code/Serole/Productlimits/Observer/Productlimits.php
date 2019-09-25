<?php
namespace Serole\Productlimits\Observer;

use \Magento\Checkout\Model\Session as CheckoutSession;

class Productlimits implements \Magento\Framework\Event\ObserverInterface
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
  
  protected $_stockUpdateArray = array();
  
  protected $_quoteId;
  
  protected $_pdfHelper;

  protected $store;
  
  public function __construct(
        CheckoutSession $checkoutSession,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
		\Serole\Sage\Helper\Data $helper,
		\Serole\Pdf\Helper\Pdf $pdfHelper,
		\Magento\Store\Model\StoreManagerInterface $store,
		\Serole\Sage\Model\Inventory $inventory
		) 
  {
        $this->_checkoutSession = $checkoutSession;
		$this->_inventory = $inventory;
		$this->_messageManager = $messageManager;
		$this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->_pdfHelper = $pdfHelper;
		$this->_helper = $helper;
	    $this->store = $store;
		$this->createLog('sage_Productslimit.log'); 
  }
  
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
	  $this->_logger->info("execute started for store:".$this->store->getStore()->getCode());
	  
	  // For now this is just validation of the Event/Village business rules 
        $event_voucher = 398; //Normal Movie Voucher
        $event_food = 399; //Normal Food Voucher
        $event_gc_voucher = 400; //Gold Class Movie Voucher
        $event_gc_food = 401; //Gold Class  Food Voucher
        $village_event_voucher = 402; //village Normal Movie Voucher
        $village_event_food = 403; //village Normal Food Voucher
        $village_event_gc_voucher = 404; //village Gold Class Food Voucher
        $village_event_gc_food = 405; //village Gold Class Movie Voucher
		
        // get the quote
        //$quote = $observer->getEvent()->getQuote();
		
        $items = $this->_checkoutSession->getQuote()->getAllVisibleItems();

        $store = $this->store->getStore();

        $counters = array(
            $event_voucher => 0,
            $event_food => 0,
            $event_gc_voucher => 0,
            $event_gc_food => 0,
            $village_event_voucher => 0,
            $village_event_food => 0,
            $village_event_gc_voucher => 0,
            $village_event_gc_food => 0
        );

        // iterate through the items on the quote 
        foreach ($items as $item) {
			
			$this->_logger->info("Sku:".$item->getSku());
			
			$prod = $this->_helper->getProductBySku($item->getSku());
			 
			$typeId = $prod->getTypeId(); 
			 
			$this->_logger->info("Sku Type:".$typeId);
			 
			if($typeId == "bundle")
			{ 
				 continue;
			}
			
            $productId = $prod->getId();
			
			 $eventRule = $prod->getResource()->getAttribute('event_business_rule_type')->getFrontend()->getValue($prod);
			 $attributeExist =$prod->getResource()->getAttribute('event_business_rule_type');
			 $eventBusRule = $attributeExist->getSource()->getOptionId($eventRule); 

			
            $this->_logger->info("Item " . $item->getSku() . ' event_bus_rule=' . $eventBusRule . " storeId=" . $store->getId());

            if ($eventBusRule && $eventBusRule != '' && $eventBusRule != 397) {
                $counters[$eventBusRule] += $item->getQty();
            }
        }
		
        $this->_logger->info("Normal Vouchers: " . $counters[$event_voucher]);
        $this->_logger->info("Food Vouchers: " . $counters[$event_food]);
        $this->_logger->info("GC Vouchers: " . $counters[$event_gc_voucher]);
        $this->_logger->info("GC Food Vouchers: " . $counters[$event_gc_food]);
        
        $this->_logger->info("Village Normal Vouchers: " . $counters[$village_event_voucher]);
        $this->_logger->info("Village Food Vouchers: " . $counters[$village_event_food]);
        $this->_logger->info("Village GC Vouchers: " . $counters[$village_event_gc_voucher]);
        $this->_logger->info("Village GC Food Vouchers: " . $counters[$village_event_gc_food]);

        $error = '';
        /*
          EVENT BUSINESS RULES - NORMAL
          - Minimum 4 tickets (can be combination of cinema + food)
          - 1 cinema for every food ticket
         */
        // For every food voucher there MUST be a corresponding cinema voucher
        if ($counters[$event_food] > $counters[$event_voucher]) {
            $error = 'Not enough Event/Village cinema vouchers for the number of Event candy bar vouchers in carts. ';
        }

        // For every food voucher there MUST be a corresponding cinema voucher
        if ($counters[$event_gc_food] > $counters[$event_gc_voucher]) {
            $error = 'Not enough Event/Village gold class cinema vouchers for the number of Event gold class candy bar vouchers in cart. ';
        }

        if ($error == '') {

            // Minimum of 4 vouchers (for food+cinema or cinema on it's own) 
            $event_all = $counters[$event_food] + $counters[$event_voucher] + $counters[$event_gc_food] + $counters[$event_gc_voucher];

            if ($event_all > 0 && $event_all < 4) {
                $error = 'Not enough Event/Village cinema vouchers in cart. ';
            }
        }
        /*
          EVENT BUSINESS RULES - Village NORMAL
          - Minimum 2 tickets (can be combination of cinema + food)
          - 1 cinema for every food ticket
         */
        // For every food voucher there MUST be a corresponding cinema voucher
        if ($counters[$village_event_food] > $counters[$village_event_voucher]) {
            $error = 'Not enough Event/Village cinema vouchers for the number of Event candy bar vouchers in cart. ';
        }

        // For every food voucher there MUST be a corresponding cinema voucher
        if ($counters[$village_event_gc_food] > $counters[$village_event_gc_voucher]) {
            $error = 'Not enough Event/Village gold class cinema vouchers for the number of Event gold class candy bar vouchers in cart. ';
        }

        if ($error == '') {

            // Minimum of 2 vouchers (for food+cinema or cinema on it's own) 
            $village_event_all = $counters[$village_event_food] + $counters[$village_event_voucher] + $counters[$village_event_gc_food] + $counters[$village_event_gc_voucher];

            if ($village_event_all > 0 && $village_event_all < 2) {
                $error = 'Not enough Event/Village cinema vouchers in cart. ';
            }
        }


        if ($error != '') {

            $error .= '<a href="' . $this->_url->getUrl('event-and-village-cinemas-rules') . '">Click here</a> for more information';

            $this->_logger->info("error " . $error);
            $this->_messageManager->addError($error);
			$redirectionUrl = $this->_url->getUrl('checkout/cart');
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