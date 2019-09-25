<?php

namespace Serole\Sage\Model;

use \Magento\Framework\Model\AbstractModel;

class Inventory extends AbstractModel
{
	private $_apiUrl;
	
	private $_apiUserName; 
	
	private $_apiPassword;
	
	protected $_sageHelper;
	
	protected $_logger;
	
	protected $_objectManager;
	
	private $productRepository;
	
	protected $_sku = array();
  
    protected $_skuQty = array();

	
	public function __construct(\Serole\Sage\Helper\Data $sageHelper) 
	{ 
	  ini_set("soap.wsdl_cache_enabled", 0);
	  $this->_sageHelper = $sageHelper;
	  
	  $this->_apiUrl = $this->_sageHelper->getAPIUrl();
	  $this->_apiUserName = $this->_sageHelper->getAPIUsername();
	  $this->_apiPassword = $this->_sageHelper->getAPIPassword();
	  
	  $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	  
	  $this->createLog('sage_Inventory.log'); 
	  
    }
	/*
	   sku array as input
	   Output will be array
	*/
	public function getCheckStock($skuArrays, $skuQtyArrays=array())
	{
		$skuArray = array_values(array_unique($skuArrays));
		
		$skus = implode(",",$skuArray);
		
        $this->_logger->info('getCheckStock function start for skus:'.$skus);
		
		$this->_logger->info('API URL:'.$this->_apiUrl);
		
		$soapclient = new \SoapClient($this->_apiUrl.'?WSDL');
		
		$params = array("arrayPara"=>$skuArray,"strEmail" => trim($this->_apiUserName), "strPassword" => trim($this->_apiPassword));
		
		$this->_logger->info('getCheckStock params:');
		$this->_logger->info($params);
		$this->_logger->info('before response');
        $response = $soapclient->CheckStock($params);
		$this->_logger->info('after response');

		$xmlResPonse = simplexml_load_string($response->CheckStockResult);
		$this->_logger->info('Response Data:');
		$this->_logger->info((array)$xmlResPonse);
		
		$errorArray = array();
		$error = 0;
		$errorString = '';
		$errorSkus= array();
		
		if(!empty($xmlResPonse->item))
		{
			if(!empty($xmlResPonse->item[0]))
			{
				foreach($xmlResPonse->item as $item)
				{
				  foreach($skuQtyArrays as $skuQtyArray)
				  {
					if($item->status == 1)
					{
						if($item->qty == 0)
						{
							$error = 1;
							
							if(isset($skuQtyArray[trim($item->sku)]) && $skuQtyArray[trim($item->sku)]['type'] == "bundle")
							{
							  $errorString .= $skuQtyArray[trim($item->sku)]['bundle-sku'].":(".$item->sku.") Out of stock"."<br />";
							  $errorSkus[] = $skuQtyArray[trim($item->sku)]['bundle-sku'];
							}
							else
							{
								if(!in_array(trim($item->sku), $errorSkus))
								{
									$errorString .= $item->sku.":Out of stock"."<br />";
									$errorSkus[] = trim($item->sku); 
								}
							}
						}
						else{

							if(isset($skuQtyArray[trim($item->sku)]) && $item->qty < $skuQtyArray[trim($item->sku)]['qty'])
							{
								$error = 1;
								if($skuQtyArray[trim($item->sku)]['type'] == "bundle")
								{
								    $this->_logger->info('Sage Qty:'.$item->qty." Requested qty:".$skuQtyArray[trim($item->sku)]['qty']. "of bundle sku:".$skuQtyArray[trim($item->sku)]['bundle-sku']);
								  if(!in_array($skuQtyArray[trim($item->sku)]['bundle-sku'],$errorSkus))
								  {
									   $errorString .= $skuQtyArray[trim($item->sku)]['bundle-sku'].":Out of stock"."<br />";
									   $errorSkus[] = $skuQtyArray[trim($item->sku)]['bundle-sku'];
								  }
								}
								else{
									$this->_logger->info('Sage Qty:'.$item->qty." Requested qty:".$skuQtyArray[trim($item->sku)]['qty']. "of sku:".$item->sku);
									$errorString .= $item->sku.":Out of stock"."<br />";
									$errorSkus[] = $item->sku;
								}
							}
						}
						
					}
					else if($item->status == 3)
					{
						$this->_logger->info('Error from api for sku:'.$item->sku. "Error:".$item->error);
						$error = 1;
						if(isset($skuQtyArray[trim($item->sku)]))
						{
							if($skuQtyArray[trim($item->sku)]['type'] == "bundle")
							{
								$errorString .= $skuQtyArray[trim($item->sku)]['bundle-sku'].":(".$item->sku.")".$item->error."<br />";
								$errorSkus[] = $skuQtyArray[trim($item->sku)]['bundle-sku'];
							}
							else
							{
								$errorString .= $item->sku.":".$item->error."<br />";
								$errorSkus[] = $item->sku; 
							}
						}
					}
					
				 }

				}
			}
			
			$errorArray['error'] = $error;
			$errorArray['errorString'] = $errorString;
			$errorArray['errorSkus'] = implode(",",$errorSkus);
			
			if($error)
			{
				$this->_logger->info($errorString);
			}
			
		}
		else{
			$this->_logger->info('getCheckStock Response:Empty');
			$errorArray['error'] = 1;
			$errorArray['errorString'] = "There is some problem. Kindly try after some time";
			$errorArray['errorSkus'] = '';
			$this->_logger->info('There is empty response from api for skus: '.$skus);
		}

	    $this->_logger->info('getCheckStock function End');

		return $errorArray;
	    
	}
	
		/*
	   $quoteId: Quote id
	   $OrderId: order increment id
	*/
	
	public function orderIDUpdate($quoteId, $OrderId)
	{
        $this->createLog('sage_Inventory_orderIDUpdate.log'); 
        $this->_logger->info('orderIDUpdate Model function start');
		$this->_logger->info($this->_apiUrl.'?WSDL');
		$soapclient = new \SoapClient($this->_apiUrl.'?WSDL');
		
		$params = array("strQuoteID"=>$quoteId, "strOrderID"=>$OrderId, "strEmail" => trim($this->_apiUserName), "strPassword" => trim($this->_apiPassword));
		
        $response = $soapclient->OrderIDUpdate($params);
        $result = trim($response->OrderIDUpdateResult);
		if($result == "Update successfully")
			$this->_logger->info('QuoteId: '.$quoteId." OrderId: ".$OrderId." updated successfully");
		else
			$this->_logger->info('QuoteId: '.$quoteId." OrderId: ".$OrderId." update Failed");
		
		$this->_logger->info('orderIDUpdate Model function End');
		
		$this->createLog('sage_Inventory.log'); 
	}
	 
	
		/*
	   sku array as input
	   Output will be array
	*/
	public function getStockQty($skuArray)
	{
		$skus = implode(",",$skuArray);
		
        $this->_logger->info('getStockQty function start for skus:'.$skus);
		
		$soapclient = new \SoapClient($this->_apiUrl.'?WSDL');
		
		$params = array("arrayPara"=>$skuArray,"strEmail" => trim($this->_apiUserName), "strPassword" => trim($this->_apiPassword));
		
        $response = $soapclient->CheckStock($params);
		
		$xmlResPonse=simplexml_load_string($response->CheckStockResult);
		
		$resultArray = array();

		if(!empty($xmlResPonse->item))
		{
			foreach($xmlResPonse->item as $item)
			{
				$sku = trim($item->sku);
				$qty = trim($item->qty);
				
				if($item->status == 1)
				{
					if($item->qty == 0)
					{
						$resultArray[$sku]['qty'] = 0;
						$resultArray[$sku]['error'] = 0;
						$resultArray[$sku]['message'] = "Out of stock";
					}
					else
					{
						$resultArray[$sku]['qty'] = $qty;
						$resultArray[$sku]['error'] = 0;
						$resultArray[$sku]['message'] = "In stock";
					}
				}
				else if($item->status == 3)
				{
					$resultArray[$sku]['qty'] = 0;
					$resultArray[$sku]['error'] = 1;
					$resultArray[$sku]['message'] = $item->error;
				}

			}
			
			
		}
	    $this->_logger->info('getCheckStock function End');
		return $resultArray;
	    
	}
	
	public function stockUpdate($skuArray)
	{
		$skus = implode(",",$skuArray);
		
        $this->_logger->info('stockUpdate function start for skus: New');
        $this->_logger->info($skuArray);

		$soapclient = new \SoapClient($this->_apiUrl.'?WSDL');
		
		$params = array("arrayPara"=>$skuArray,"strEmail" => trim($this->_apiUserName), "strPassword" => trim($this->_apiPassword));
        $this->_logger->info($params);
		
        $response = $soapclient->StockUpdate($params);
		
		$xmlResPonse = simplexml_load_string($response->StockUpdateResult);
		
		$this->_logger->info('stockUpdate response:');
		$this->_logger->info($xmlResPonse); 
		
		$errorArray = array();
		$error = 0;
		$errorString = '';
		$errorSkus= array();
		
		if(!empty($xmlResPonse->item))
		{
			
				foreach($xmlResPonse->item as $item)
				{
                  $this->_logger->info("Error Status SKU:".$item->status.":".$item->status);
				  
				  if($item->status == 2)
					{
						$this->_logger->info("Error Status SKU:".$item->status.":".$item->status);
						$error = 1;
						$errorString .= $item->sku.":".$item->error."<br />";
						$errorSkus[] = $item->sku;
					}

				}
			
			$errorArray['error'] = $error;
			$errorArray['errorString'] = $errorString;
			$errorArray['errorSkus'] = implode(",",$errorSkus);
			
			if($error)
			{
				$this->_logger->info($errorString);
			}
			
		}
		else{
			$errorArray['error'] = 1;
			$errorArray['errorString'] = 'There is empty response from api for skus: '.$skus;
			$errorArray['errorSkus'] = '';
			$this->_logger->info('There is empty response from api for skus: '.$skus);
		}

	    $this->_logger->info('getCheckStock function End');

		return $errorArray;
	}
    
  public function getSageStockCheck($items)
   {
	   $i=0;
	foreach($items as $item)
	{
	    if($item['type'] == "sku")
	  	  $prod = $this->_sageHelper->getProductBySku($item['identifier']);
	    else
		  $prod = $this->_sageHelper->getProductById($item['identifier']);
		 
		 $typeId = $prod->getTypeId(); 
		 
		 
		 if($typeId == "bundle")
		 {
			 
			 $this->getBundleProductOptionsData($prod, $item['qty'], $i);
		 }
		 else
		 {
		   $isStockItem = $prod->getIsStockItem();
		   if(isset($isStockItem) && $isStockItem == 1)
		   {
			   $this->_sku[] = $prod->getSku();
			   $this->_skuQty[$i][trim($prod->getSku())]['qty'] = $item['qty'];
			   $this->_skuQty[$i][trim($prod->getSku())]['type'] = "not-bundle";
			   $this->_skuQty[$i][trim($prod->getSku())]['bundle-sku'] = "NA";
		   }
		 }
		 $i++;
	 }
	 if(!empty($this->_sku))
	 {
		 $result = $this->getCheckStock($this->_sku,$this->_skuQty);
	 }
	 else{
		 $result['error'] = 0;
	 }
	 return $result;
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
        foreach ($selectionCollection as $proselection) {
			 $chldProd = $this->_sageHelper->getProductBySku($proselection->getSku());
			 $isStockItm = $chldProd->getIsStockItem();
			 if(isset($isStockItm) && $isStockItm == 1)
	         {
				$this->_sku[] = $proselection->getSku();
				$this->_skuQty[$i][trim($proselection->getSku())]['qty'] = $qty*$proselection->getSelectionQty();
				$this->_skuQty[$i][trim($proselection->getSku())]['type'] = "bundle";
				$this->_skuQty[$i][trim($proselection->getSku())]['bundle-sku'] = $product->getSku();
			 }
   
        } 
       
    }
	
	/*
	  example request
	  quoteId,orderIncrementId,SKU,SerialNumber
	  $data = array("q1,o1,sku1,sn1","q1,o1,sku1,sn2");
	  
	  Return: array
	*/
	
	public function CheckPhysicalSerialCode($data)
	{
		$this->createLog('sage_physicalSerialcode.log'); 
		$this->_logger->info('Request Parameter:');
		
		$response = array();
		try{
			//$data = array("573,8000000078,EVE-MOV-NP-CHI-005,EVE21032","573,8000000078,EVE-MOV-NP-CHI-005,EVE21033");
			$this->_logger->info($data);
			$soapclient = new \SoapClient($this->_apiUrl.'?WSDL');
			
			$params = array("arrayPara"=>$data,"strEmail" => trim($this->_apiUserName), "strPassword" => trim($this->_apiPassword));
			
		    $this->_logger->info($params);
			
			$apiResponse = $soapclient->CheckPhysicalSerialCode($params);
		
			$xml = $apiResponse->CheckPhysicalSerialCodeResult;
			
			$this->_logger->info('Request Parameter:');
			
			$this->_logger->info($xml);

			$xmlResPonse = simplexml_load_string($xml);
			
			if(!empty($xmlResPonse->item))
			{
				foreach($xmlResPonse->item as $item)
				{
					$error = (string)trim($item->Error);
					if($error != '')
					{
					   $response['status'] = "Error";
					   $response['msg'] = "Item Error";
					   $itemSku = (string)trim($item->Item);
					   $serilCodes = (string)trim($item->SerialNumber);
					   $response['errorItem'][$itemSku][$serilCodes] = $error;
					}
				}
				if(empty($response))
				{
					$response['status'] = "Success";
				}
			}
			else{
			     $response['status'] = "Error";
				 $response['msg'] = "Getting empty API response";
			}
			
			
		}
		catch(\Exception $e)
		{
			$response['status'] = "Error";
			$response['msg'] = "SAGE ERROR:".$e->getMessage();
		}
		$this->createLog('sage_Inventory.log'); 
		return $response;
		
	}
	
	/*
	  example request
	  quoteId,orderIncrementId,SKU,Quantity
	  $data = array("q1,o1,s1,2","q1,o1,s2,2");
	  
	  Return: array of request no of serial codes
	*/
	
	public function getSerilaCodes($data)
	{
		$this->createLog('sage_serialcode.log'); 
		$this->_logger->info('getSerilaCodes function start');
		$this->_logger->info($this->_apiUrl.'?WSDL');
		$soapclient = new \SoapClient($this->_apiUrl.'?WSDL');
		
		$params = array("arrayPara"=>$data,"strEmail" => trim($this->_apiUserName), "strPassword" => trim($this->_apiPassword));
		
		$this->_logger->info('Request Parameter:');
		$this->_logger->info('-------------------------------------------------------');
		$this->_logger->info($data);
		$this->_logger->info('-------------------------------------------------------');
		$this->_logger->info($params);
        $this->_logger->info('-------------------------------------------------------');
		
		try{

				$response = $soapclient->GetSerialCode($params);

			
				$xml = $response->GetSerialCodeResult;
				
				$this->_logger->info('Response XML:'.$xml);

				$xmlResPonse = simplexml_load_string($xml);
			 
				$serilCodes = array();

				if(!empty($xmlResPonse->item))
				{
					$AllData = array();
					$i=0;
					$count = 0;
					$message = array();
					foreach($xmlResPonse->item as $item)
					{
						$sku = trim($item->Item);
						if(!in_array($sku,$AllData))
						{
							$i=0;
							$AllData[] = $sku; 
						}
						$error = (string)trim($item->Error);
					   if($error == '') {
						   $serilCodes['response'] = "Success";
						   $serilCodes['sku'][$sku][$i]['OrderID'] = (string)trim($item->OrderID);
						   $serilCodes['sku'][$sku][$i]['SerialNumber'] = (string)trim($item->SerialNumber);
						   $serilCodes['sku'][$sku][$i]['ExpireDate'] = (string)trim($item->ExpireDate);
						   $serilCodes['sku'][$sku][$i]['PIN'] = (string)trim($item->PIN);
						   $serilCodes['sku'][$sku][$i]['SecondSerialCode'] = (string)trim($item->SecondSerialCode[0]);
						   $serilCodes['sku'][$sku][$i]['URL'] = (string)trim($item->URL[0]);
						   $serilCodes['sku'][$sku][$i]['StartDate'] = (string)trim($item->StartDate);
						   $serilCodes['sku'][$sku][$i]['Value'] = (string)trim($item->Value);
					   }
					   else{
						   if($error == "2")
							   $error = "No serial codes received from SAGE for SKU:$sku. Please handle it manually";
						   $serilCodes['response'] = "Error";
						   $message[] = $sku.":".$error;
					   }
						$i++;
					}
					$serilCodes['TotalCount'] = count($xmlResPonse);
				}
				else
				{
					$this->_logger->info('Empty response for serial codes from SAGE');
					$serilCodes['response'] = "Error";
					$message[] = "Empty response for serial codes from SAGE";
				}
		    
			}
			catch(\Exception $e)
			{
				$this->_logger->info($e); 
				$serilCodes['response'] == " Error";
				$message[] = $e->getMessage();
			}

		  if($serilCodes['response'] == "Error")
		  {
			  $serilCodes['message'] = implode(",", $message);
		  }
		  
		  //setting default inventory log file
		  //$this->_logger->info('Return Data:'.$serilCodes);
		  
		  $this->_logger->info('getSerilaCodes function End');
		  
		  $this->createLog('sage_Inventory.log'); 
		  
		  return $serilCodes;
	}

	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
   
} 