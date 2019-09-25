<?php
namespace Serole\Sage\Observer;

use \Magento\Checkout\Model\Session as CheckoutSession;

class Checkstockbeforepayment implements \Magento\Framework\Event\ObserverInterface
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
		$this->createLog('sage_Inventory_checkout_before_payment.log'); 
    }
  
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
	   $this->_logger->info("execute started for store:".$this->store->getStore()->getCode());
  	   if($this->store->getStore()->getCode() != 'racvportal'){
			$items = $this->_checkoutSession->getQuote()->getAllVisibleItems();
			$this->_logger->info("items:"); 
			//$this->_logger->info($items);
			$skus = array();
			$this->_quoteId = $this->_checkoutSession->getQuote()->getId();
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
				/* 
				 $this->_logger->info("stock check request");
				 $this->_logger->info($this->_sku);
				 $this->_logger->info($this->_skuQty);
			     $result = $this->_inventory->getCheckStock($this->_sku, $this->_skuQty);
				 
				 if($result['error'] == 1)
				 {
					$this->_logger->info("Error from api while stock check");
				    $this->_logger->info('Error:'.$result["errorString"]);
					$message = $result["errorString"];
					$this->_messageManager->addError($message);
					$this->_helper->setValue($result["errorSkus"]);
					exit;
				 }
				 else
				 {
					 */
					 if(!empty($this->_stockUpdateArray))
					 {
						 $this->_logger->info("Stock Update Api Request");
						 $this->_logger->info($this->_stockUpdateArray);
						 $updateResult = $this->_inventory->stockUpdate($this->_stockUpdateArray);
						  $this->_logger->info("Stock Update Api Response");
						  $this->_logger->info($updateResult);
						 if($updateResult['error'] == 1)
						 {
							$this->_logger->info("Error from api while stock update");
				            $this->_logger->info('Error:'.$result["errorString"]);
							$message = $updateResult["errorString"];
							$this->_messageManager->addError($message);
					        $this->_helper->setValue($result["errorSkus"]);
							$this->_pdfHelper->sendEmailToAdminPDFissue('','',"Stock Update Api:".$message);
							exit;
						 }
					 }
				 //}
			 }
		     return true;
      }
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