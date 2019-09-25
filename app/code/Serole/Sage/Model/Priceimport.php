<?php

namespace Serole\Sage\Model;

use \Magento\Framework\Model\AbstractModel;

class Priceimport extends AbstractModel
{
	private $_mssqlServer;
	
	private $_mssqlUser;
	
	private $_mssqlPassword;
	
	private $_mssqlDatabase;
	
	private $_mssqlPort;
	
	private $_mysqlServer;
	
	private $_mysqlUser;
	
	private $_mysqlPassword;
	
	private $_mysqlDatabase;
	
	private $_mysqlPort;
	
	private $_apiUserName;
	
	private $_apiPassword;
	
	private $_mssqlConnection;
	
	private $_mysqlConnection;
	
	protected $_sageHelper;
	
	protected $_logger;
	
	protected $_serialize;
	
	protected $_objectManager;
	
	protected $_attributeSets = [];
	
	private $_productRepository;
	
	protected $productResourceModel;
	
	protected $productFactory;

	
	public function __construct(
	       \Serole\Sage\Helper\Data $sageHelper,
		   \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		   \Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
		   \Magento\Catalog\Model\ProductFactory $productFactory
		) 
	{ 
	
	  $this->_sageHelper = $sageHelper;
	  
	  $this->productResourceModel = $productResourceModel;
	  
	  $this->productFactory = $productFactory;
	  
	  $this->_productRepository = $productRepository;
	  
	  $this->_apiUserName = $this->_sageHelper->getAPIUsername();
	  $this->_apiPassword = $this->_sageHelper->getAPIPassword();
	  
	  $this->_mssqlServer = $this->_sageHelper->getMsSqlServer();
	  $this->_mssqlUser = $this->_sageHelper->getMsSqlUserName();
	  $this->_mssqlPassword = $this->_sageHelper->getMsSqlPassword();
	  $this->_mssqlDatabase = $this->_sageHelper->getMsSqlDatabase();
	  $this->_mssqlPort = $this->_sageHelper->getMsSqlPort();
	  
	  $this->_mysqlServer = $this->_sageHelper->getMySqlServer();
	  $this->_mysqlUser = $this->_sageHelper->getMySqlUserName();
	  $this->_mysqlPassword = $this->_sageHelper->getMySqlPassword();
	  $this->_mysqlDatabase = $this->_sageHelper->getMySqlDatabase();
	  $this->_mysqlPort = $this->_sageHelper->getMySqlPort();
	  
	  $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	  
	  $this->createLog('sage_PriceItems.log');
      $this->getMssqlConnection();
	  $this->getMysqlConnection(); 
	  
    }
	
	
	private function getMssqlConnection() {
		
		$host = $this->_mssqlServer.":".$this->_mssqlPort;
		$user = $this->_mssqlUser;
		$password = $this->_mssqlPassword;
		$database = $this->_mssqlDatabase;
		try{
		     $this->_mssqlConnection = new \PDO("dblib:host=$host;dbname=$database",$user,$password);
             //$this->_logger->info("connected to Mssql");			 
		}catch (PDOException $e) {
           $this->_logger->info('Connection failed to Mssql: ' . $e->getMessage());	
		}
  
	}
	
	private function getMysqlConnection() {
		
		$host = $this->_mysqlServer.":".$this->_mysqlPort;
		$user = $this->_mysqlUser;
		$password = $this->_mysqlPassword;
		$database = $this->_mysqlDatabase;
		try{
		     $this->_mysqlConnection = new \PDO("mysql:host=$host;dbname=$database",$user,$password);
             //$this->_logger->info("connected to Mysql");			 
		}catch (PDOException $e) {
             $this->_logger->info('Connection failed to Mysql: ' . $e->getMessage());	
		}
	}
	
	public function ifProductExist($sku)
	{
       $this->_logger->info('ifProductExist function start');	
		$query = "select * from catalog_product_entity where sku = '$sku'";
	
		$stmt = $this->_mysqlConnection->prepare($query); 
	
		$stmt->execute();

		$row = $stmt->fetchAll();

	  $this->_logger->info('ifProductExist function End');
		if(count($row))
			return $row[0]['entity_id'];
		else
			return false;
	
	}
	
	public function getStoreCode($websiteCode)
	{
       $this->_logger->info('getStoreCode function start');	
	   
	   $websiteCode = strtolower($websiteCode); 
	   
	   if($websiteCode == "base")
	   {
		   $this->_logger->info('website code:base');
           return 0;		   
	   }
	   
		$query = "SELECT store.store_id FROM store JOIN store_website on store.website_id = store_website.website_id where store_website.code = '$websiteCode'";
	
		$stmt = $this->_mysqlConnection->prepare($query); 
	
		$stmt->execute();

		$row = $stmt->fetchAll();
		
	    $this->_logger->info('getStoreCode function End');
	  
		if(count($row))
		{
			$sId = $row[0]['store_id'];
			$this->_logger->info("store id is '$sId' for website code '$websiteCode'");	
			return $sId;
		}
		else
			return false;
	
	}
	
	
	public function getPriceFromSage()
	{
		echo "<pre>";
		// get all attribute sets
		$this->_logger->info('getPriceFromSage function Start');
		
		$success = 0;
		$error = 0;

		$updateQuery = "UPDATE dbo.ItemPricing SET LTSYNCDATE=:LTSYNCDATE, LTSYNCTIME=:LTSYNCTIME, PRICESTATUS=:PRICESTATUS, PRICEERROR=:PRICEERROR WHERE ITEMNO=:ITEMNO AND STOREID=:STOREID";
		
        $updateStmt = $this->_mssqlConnection->prepare($updateQuery);
		
		// to get all item from sage need to be imported
		$query = "SELECT * FROM dbo.ItemPricing WHERE PRICESTATUS = '0'";
		
		$stmt = $this->_mssqlConnection->prepare($query); 
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		if(!empty($rows))
		{
			
		  foreach($rows as $row) {
			  try {
			    $result = $this->updateProductPrice($row);
			  }
			  catch(\Exception $e)
			  {
				  $this->_logger->info('Error while updating product price: '.$e->getMessage());
			  }
			  $itemNo = trim($row['ITEMNO']);
			  $storeId = trim($row['STOREID']);
			  $date = date('Y-m-d');
			  $time = date('H:i:s');
			  $params = array(
						'PRICESTATUS' => $result['PRICESTATUS'],
						'PRICEERROR' => $result['PRICEERROR'],
						'LTSYNCDATE' => $date,
						'LTSYNCTIME' => $time,
						'ITEMNO' => $itemNo,
						'STOREID' => $storeId
					       );
			//print_r($params);
			  $updateStmt->execute($params);
			
			 if($result['status'])
			 {
			   $success++;
			 }
			 else{
				 $error++;
			 }
			 //break;
		  }
	    } 
		$total = $success + $error;
		//exit;
		echo 'Total no. of skus updated: ' .$total. " Success records: ".$success." Error records: ".$error;
        $this->_logger->info('Total no. of skus updated: ' .$total. " Success records: ".$success." Error records: ".$error); 
		
		$this->_logger->info('getPriceFromSage function End');

	}
	
	public function updateProductPrice($row)
	{
		$this->_logger->info('updateProductPrice function Start for SKU:'.$row['ITEMNO']);
		$returnData = array();
		
		$existingProdId = $this->ifProductExist(trim($row['ITEMNO']));
		
		$storeId = $this->getStoreCode(trim($row['STOREID']));
		
		$this->_logger->info('Store id:'.$storeId);
		
		if($storeId === false)
		{
			 $returnData['status'] = false;
			 $returnData['PRICESTATUS'] = 2;
			 $returnData['PRICEERROR'] = $row['ITEMNO'].": store id does not exist"; 
			 return $returnData;
		}
		
		if(!$existingProdId)
		{
			 $returnData['status'] = false;
			 $returnData['PRICESTATUS'] = 2;
			 $returnData['PRICEERROR'] = $row['ITEMNO'].": product does not exist"; 
			 return $returnData;
		}
		
		
		try{
			$sku = trim($row['ITEMNO']);
			$price = trim($row['RRP']);
			$subsidy = trim($row['Subsidy']);
			$priceStart = date("m/d/Y", strtotime(trim($row['PRICESTART'])));
			$priceEnd = date("m/d/Y", strtotime(trim($row['PRICEEND'])));
			$specialPrice = trim($row['ACTPRICE']);
			
			$this->_logger->info('Store id:'.$storeId." Sku: ".$sku." price: ".$price." priceStart: ".$priceStart." priceEnd: ".$priceEnd." specialPrice: ".$specialPrice);
			
			$product = $this->productFactory->create(); 
			
			$this->productResourceModel->load($product, $existingProdId);

            //$product = $productFactory->create()->setStoreId($storeId)->load($existingProdId);
			
			$product->setStoreId($storeId);
			
			if(isset($price) && $price != '')
			    $product->setPrice($price);
			
			if(isset($subsidy) && $subsidy != '')
			    $product->setSubsidy($subsidy);
		   
		    if(isset($specialPrice) && $specialPrice != '')
			    $product->setSpecialPrice($specialPrice);
			
		    if(isset($priceStart) && $priceStart != '')
				$product->setSpecialFromDate($priceStart); 
			
			if(isset($priceEnd) && $priceEnd != '')
				$product->setSpecialToDate($priceEnd);
			
			$this->productResourceModel->saveAttribute($product, 'price');
			$this->productResourceModel->saveAttribute($product, 'subsidy');
			$this->productResourceModel->saveAttribute($product, 'special_price');
			$this->productResourceModel->saveAttribute($product, 'special_from_date');
			$this->productResourceModel->saveAttribute($product, 'special_to_date');
			
			//$product->save();


			$returnData['status'] = true;
			$returnData['PRICESTATUS'] = 1;
			$returnData['PRICEERROR'] = "Successfully Updated";
			
		}
		catch(UrlAlreadyExistsException $e)
		{
           $this->_logger->info('Error while creating product with sku: ' .$row['ITEMNO'].' and Error is: '.$e->getMessage());
           $returnData['status'] = false;
		   $returnData['PRICESTATUS'] = 2;
		   $returnData['PRICEERROR'] = "Error in update: ".$e->getMessage();		   
		}
		catch(AlreadyExistsException $e)
		{
           $this->_logger->info('Error while creating product with sku: ' .$row['ITEMNO'].' and Error is: '.$e->getMessage());
           $returnData['status'] = false;
		   $returnData['PRICESTATUS'] = 2;
		   $returnData['PRICEERROR'] = "Error in update: ".$e->getMessage();		   
		}
		catch(DuplicateException $e)
		{
           $this->_logger->info('Error while creating product with sku: ' .$row['ITEMNO'].' and Error is: '.$e->getMessage());
           $returnData['status'] = false;
		   $returnData['PRICESTATUS'] = 2;
		   $returnData['PRICEERROR'] = "Error in update: ".$e->getMessage();		   
		}
		catch(\Exception $e)
		{
           $this->_logger->info('Error while creating product with sku: ' .$row['ITEMNO'].' and Error is: '.$e->getMessage());
           $returnData['status'] = false;
		   $returnData['PRICESTATUS'] = 2;
		   $returnData['PRICEERROR'] = "Error in update: ".$e->getMessage();		   
		}
		$this->_logger->info('updateProductPrice function End for SKU:'.$row['ITEMNO']);
		//print_r($returnData);
		return $returnData;
	}
	
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
   
} 