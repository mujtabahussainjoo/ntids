<?php
namespace Serole\Sage\Observer;

use \Magento\Sales\Model\AdminOrder\Create  as OrderCreate;

class Checkstockbeforepaymentadmin implements \Magento\Framework\Event\ObserverInterface
{
	
  protected $_orderCreate;
  
  protected $_inventory;
  
  protected $_messageManager;
  
  protected $_objectManager;
  
  protected $_responseFactory;
  
  protected $_logger;
  
  protected $_helper;
  
  protected $_sku = array();
  
  protected $_skuQty = array();
  
  protected $_stockUpdateArray = array();
  
  protected $_quoteId;

  protected $store;
  
  
  public function __construct(
        OrderCreate $orderCreate,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\ResponseFactory $responseFactory,
		\Serole\Sage\Helper\Data $helper,
		\Magento\Store\Model\StoreManagerInterface $store,
		\Serole\Sage\Model\Inventory $inventory
		) 
	{
        $this->_orderCreate = $orderCreate;
		$this->_inventory = $inventory;
		$this->_messageManager = $messageManager;
		$this->_responseFactory = $responseFactory;
		$this->_helper = $helper;
	    $this->store = $store;
		$this->createLog('sage_Inventory_checkout_before_payment_admin.log'); 
    }
  
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
	        $this->_logger->info("execute started");
  	  
			$items = $this->_orderCreate->getQuote()->getAllVisibleItems();
			$skus = array();
			$this->_quoteId = $this->_orderCreate->getQuote()->getId();
			$this->_logger->info("Quote Id:".$this->_quoteId);
			$i=0;
			foreach($items as $item)
			{
				$this->_logger->info("Sku:".$item->getSku());
				$prod = $this->_helper->getProductBySku($item->getSku());
				 
				 $typeId = $prod->getTypeId(); 
				 
				 $this->_logger->info("Sku Type:".$typeId);
				 
				 if($typeId == "bundle")
				 { 
					 $this->getBundleProductOptionsData($prod, $item->getQty(), $i);
				 }
				 else
				 {
				   $isStockItem = $prod->getIsStockItem();
				   $this->_logger->info("isStockItem:".$isStockItem);
				   if(isset($isStockItem) && $isStockItem == 1)
				   {
						$itemSku = $item->getSku();
						$itemQty = $item->getQty();
						$quoteId = $this->_quoteId;
						$this->_stockUpdateArray[] = "$quoteId,$itemSku,$itemQty,1";
						$this->_sku[] = $prod->getSku();
						$this->_skuQty[$i][trim($prod->getSku())]['qty'] = $item->getQty();
						$this->_skuQty[$i][trim($prod->getSku())]['type'] = "not-bundle";
						$this->_skuQty[$i][trim($prod->getSku())]['bundle-sku'] = "NA";
					 }
				 }
			$i++;
			}
			if(!empty($this->_sku))
			 {
				 $this->_logger->info($this->_sku);
				 $this->_logger->info($this->_skuQty);
			     $result = $this->_inventory->getCheckStock($this->_sku, $this->_skuQty);
		         $this->_logger->info($result);
				 $this->_logger->info('skus:'.$result["errorSkus"]);
				 
				 if($result['error'] == 1)
				 {
					$message = $result["errorString"];
					$this->_messageManager->addError($message);
					$this->_helper->setValue($result["errorSkus"]);
				echo "<script> window.history.go(-1); </script>";
				exit;
				 }
				 else
				 {
					 if(!empty($this->_stockUpdateArray))
					 {
						 $this->_logger->info("Stock Update Api Request");
						 $this->_logger->info($this->_stockUpdateArray);
						 $updateResult = $this->_inventory->stockUpdate($this->_stockUpdateArray);
						  $this->_logger->info("Stock Update Api Response");
						  $this->_logger->info($updateResult);
						 if($updateResult['error'] == 1)
						 {
							$message = $updateResult["errorString"];
							 mail("dhananjay.kumar@serole.com", "Error in Stockupdate API", $message);
						 }
					 }
				 }
			 }
			 else
			 {
				 $this->_logger->info("No Skus");
			 }
		     return true;
      
  }
  
    public function getBundleProductOptionsData($product, $qty, $i)
    {
        //get all the selection products used in bundle product.
		//$product = $this->_productFactory->create()->load($productId);
		
        $selectionCollection = $product->getTypeInstance(true)
            ->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
            );
		$quoteId = $this->_quoteId;
        foreach ($selectionCollection as $proselection) {
			 $chldProd = $this->_helper->getProductBySku($proselection->getSku());
			 $isStockItm = $chldProd->getIsStockItem();
			 if(isset($isStockItm) && $isStockItm == 1)
	         {
				$itemSku = $proselection->getSku();
				$itemQty = $qty*$proselection->getSelectionQty();
				$this->_stockUpdateArray[] = "$quoteId,$itemSku,$itemQty,1";
				$this->_sku[] = $proselection->getSku();
				$this->_skuQty[$i][trim($proselection->getSku())]['qty'] = $qty*$proselection->getSelectionQty();
				$this->_skuQty[$i][trim($proselection->getSku())]['type'] = "bundle";
				$this->_skuQty[$i][trim($proselection->getSku())]['bundle-sku'] = $product->getSku();
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