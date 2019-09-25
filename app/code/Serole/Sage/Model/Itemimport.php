<?php

namespace Serole\Sage\Model;

use \Magento\Framework\Model\AbstractModel;

class Itemimport extends AbstractModel
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
	
	private $productRepository;

	
	public function __construct(
	       \Serole\Sage\Helper\Data $sageHelper,
		   \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
		) 
	{ 
	
	  $this->_sageHelper = $sageHelper;
	  
	  $this->productRepository = $productRepository;
	  
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
	  
	  $this->createLog('sage_ImportItems.log');
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
			return true;
		else
			return false;
	
	}
	
	
	public function getAllAttributeSets()
	{
		$this->_logger->info('getAllAttributeSets function Start');
		$query = "select * from eav_attribute_set where attribute_set_name != 'Default'";
		
		$stmt = $this->_mysqlConnection->prepare($query); 
		
		$stmt->execute();
		
		  while ($row = $stmt->fetch()) {
			  
			$this->_attributeSets[$row['attribute_set_name']] = $row['attribute_set_id']; 
			
		  }
		$this->_logger->info('getAllAttributeSets function End');
	}
	
	public function updateSageItem($itemSku, $data)
	{
		$itemSku =trim($itemSku);
		
		if($data['ITEMERROR'] != "")
		   $query = "UPDATE dbo.NewItem SET LTSYNCDATE='".$data['LTSYNCDATE']."', LTSYNCTIME='".$data['LTSYNCTIME']."', ITEMSTATUS='".$data['ITEMSTATUS']."', ITEMERROR ='".$data['ITEMERROR']."' WHERE ITEMNO = :ITEMNO";
		else
			$query = "UPDATE dbo.NewItem SET LTSYNCDATE='".$data['LTSYNCDATE']."', LTSYNCTIME='".$data['LTSYNCTIME']."', ITEMSTATUS='".$data['ITEMSTATUS']."' WHERE ITEMNO = :ITEMNO";
		
		
		$params['ITEMNO'] = $itemSku;
		
		$stmt = $this->_mssqlConnection->prepare($query); 
		
		$stmt->execute($params);
		
		return $stmt;
	}
	
	public function getItemsFromSage()
	{	
		$this->_logger->info('getItemsFromSage function Start');
		
		$success = 0;
		$error = 0;

		$updateQuery = "UPDATE dbo.NewItem SET LTSYNCDATE=:LTSYNCDATE, LTSYNCTIME=:LTSYNCTIME, ITEMSTATUS=:ITEMSTATUS, ITEMERROR=:ITEMERROR WHERE ITEMNO=:ITEMNO";
		
        $updateStmt = $this->_mssqlConnection->prepare($updateQuery);
		
		// to get all item from sage need to be imported
		$query = "SELECT * FROM dbo.NewItem WHERE ITEMSTATUS = '0' and BUNDLE='0'";
		
		$stmt = $this->_mssqlConnection->prepare($query); 
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		if(!empty($rows))
		{
			
		  $this->getAllAttributeSets();  // get all attribute sets
		  
		  foreach($rows as $row) {
			  try {
			    $result = $this->createProduct($row);
			  }
			  catch(\Exception $e)
			  {
				  $this->_logger->info('Error while creating product: '.$e->getMessage());
			  }
			  
			$itemNo = trim($row['ITEMNO']);
			$date = date('Y-m-d');
			$time = date('H:i:s');
			
			$params = array(
						'ITEMSTATUS' => $result['ITEMSTATUS'],
						'ITEMERROR' => $result['ITEMERROR'],
						'LTSYNCDATE' => $date,
						'LTSYNCTIME' => $time,
						'ITEMNO' => $itemNo
					       );
			
			
			$updateStmt->execute($params);
			
			 if($result['status'])
			 {
			   $success++;
			 }
			 else{
				 $error++;
			 }
			 //echo "Successfully Imported:".$itemNo."\n";
		  }
	    } 
		  $total = $success + $error;
		  
        $this->_logger->info('Total no. of skus imported: ' .$total. " Success records: ".$success." Error records: ".$error); 
		
		$this->_logger->info('getItemsFromSage function End');

	}
	
	public function createProduct($row)
	{
		$this->_logger->info('createProduct function Start for SKU:'.$row['ITEMNO']);
		$returnData = array();
		
		$existingProd = $this->ifProductExist(trim($row['ITEMNO']));
		
		if($existingProd)
		{
			 $returnData['status'] = false;
			 $returnData['ITEMSTATUS'] = 2;
			 $returnData['ITEMERROR'] = $row['ITEMNO'].": product already exist"; 
			 return $returnData;
		}
		
		$attrbtSet = trim($row['ATTRSET']); 
		if(isset($this->_attributeSets[$attrbtSet]))
            $attrSet = $this->_attributeSets[$attrbtSet];
		else
			$attrSet = 4;
		
		$this->_logger->info('Attribute Set Id:'.$attrSet);
		
		if(isset($row['TAXCODE']) && trim($row['TAXCODE']) == "1")
            $taxCode = 2;
		else
			$taxCode = 0;
		
		if(isset($row['MAGTYPE']) && trim($row['MAGTYPE']) == "Virtual Product")
            $prodType = "virtual";
		elseif(isset($row['MAGTYPE']) && trim($row['MAGTYPE']) == "Simple Product")
			$prodType = "simple";
		else
		  {
			 $returnData['status'] = false;
			 $returnData['ITEMSTATUS'] = 2;
			 $returnData['ITEMERROR'] = "product type not defined"; 
			 return $returnData;
		  }
		
		try{
			$urlKey = $this->cleanString(trim($row['DESC'])."-".trim($row['ITEMNO'])); 
			$product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
			$product->setSku(trim($row['ITEMNO'])); 
			$product->setName(trim($row['DESC'])); 
			$product->setAttributeSetId($attrSet); 
			$product->setStatus(2); 
			$product->setWeight(0); 
			$product->setVisibility(4);
			$product->setTaxClassId($taxCode); 
			$product->setTypeId($prodType);
			$product->setPrice(0); 
			$product->setUrlKey($urlKey);
            $product->setSageSyncedDate(Date('d-m-Y'));			
            $product->setSupplierCode(trim($row['SUPPLIER']));			
			$product->setBacktoback(trim($row['BACKTOBACK'])); 
			$product->setIsserializeditem(trim($row['SERIALNO'])); 
			$product->setVendorEmailAddress(trim($row['VENEMAIL'])); 
			$product->setStockloc(trim($row['STOCKLOC']));
            $product->setIsStockItem(trim($row['ICSTATUS']));			
			//$product->setSerialno(trim($row['SERIALNO'])); 
			$product->setStockData(
									array(
										'use_config_manage_stock' => 0,
										'manage_stock' => 0
									)
								);
			$product->save();
			$returnData['status'] = true;
			$returnData['ITEMSTATUS'] = 1;
			$returnData['ITEMERROR'] = "Successfully Imported";
			
		}
		catch(UrlAlreadyExistsException $e)
		{
           $this->_logger->info('Error while creating product with sku: ' .$row['ITEMNO'].' and Error is: '.$e->getMessage());
           $returnData['status'] = false;
		   $returnData['ITEMSTATUS'] = 2;
		   $returnData['ITEMERROR'] = "Error in import: ".$e->getMessage();		   
		}
		catch(AlreadyExistsException $e)
		{
           $this->_logger->info('Error while creating product with sku: ' .$row['ITEMNO'].' and Error is: '.$e->getMessage());
           $returnData['status'] = false;
		   $returnData['ITEMSTATUS'] = 2;
		   $returnData['ITEMERROR'] = "Error in import: ".$e->getMessage();		   
		}
		catch(DuplicateException $e)
		{
           $this->_logger->info('Error while creating product with sku: ' .$row['ITEMNO'].' and Error is: '.$e->getMessage());
           $returnData['status'] = false;
		   $returnData['ITEMSTATUS'] = 2;
		   $returnData['ITEMERROR'] = "Error in import: ".$e->getMessage();		   
		}
		catch(\Exception $e)
		{
           $this->_logger->info('Error while creating product with sku: ' .$row['ITEMNO'].' and Error is: '.$e->getMessage());
           $returnData['status'] = false;
		   $returnData['ITEMSTATUS'] = 2;
		   $returnData['ITEMERROR'] = "Error in import: ".$e->getMessage();		   
		}
		$this->_logger->info('createProduct function End for SKU:'.$row['ITEMNO']);
		return $returnData;
	}
	
	public function cleanString($string) 
	{
	    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

	    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
   
} 