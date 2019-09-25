<?php

namespace Serole\Sage\Model;

use \Magento\Framework\Model\AbstractModel;

class Bundleimport extends AbstractModel
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
	  
	  $this->createLog('sage_ImportBundleItems.log');
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
       $this->_logger->info("ifProductExist function start for sku:$sku");
	   
		$query = "select * from catalog_product_entity where sku = '$sku'";
	
		$stmt = $this->_mysqlConnection->prepare($query); 
	
		$stmt->execute();

		$row = $stmt->fetchAll();
	  $this->_logger->info("ifProductExist function End for sku:$sku");
		if(count($row))
			return true;
		else
			return false;
	
	}
	
	
	public function loadMyProduct($sku)
	{
		return $this->productRepository->get($sku);
	}
	
	public function getBundleLinks($childData)
	{
		$this->_logger->info('getBundleLinks function start');
		$links = array();
		
		foreach($childData as $data)
		{
			//$product = $this->loadMyProduct($data['COMPONENT']);
			
			$link = $this->_objectManager->create(\Magento\Bundle\Api\Data\LinkInterface::class);	
			$link->setPosition(0);
			$link->setSku($data['COMPONENT']);
			$link->setIsDefault(true);
			$link->setQty($data['QUANTITY']);
			$link->setPrice(0);
			$link->setPriceType(\Magento\Bundle\Api\Data\LinkInterface::PRICE_TYPE_FIXED);
			$links[] = $link;
		}
		
		$this->_logger->info('getBundleLinks function end');
		return $links;
	}
	
	
	public function getAllAttributeSets()
	{
		$this->_logger->info('getAllAttributeSets function Start');
		$query = "select * from eav_attribute_set";
		
		$stmt = $this->_mysqlConnection->prepare($query); 
		
		$stmt->execute();
		
		  while ($row = $stmt->fetch()) {
			  
			$this->_attributeSets[$row['attribute_set_name']] = $row['attribute_set_id']; 
			
		  }
		$this->_logger->info('getAllAttributeSets function End');
	}
	
	
	public function getItemsFromSage()
	{	
		$this->_logger->info('getItemsFromSage function Start');
		
		$success = 0;
		$error = 0;

		$updateQuery = "UPDATE dbo.NewItem SET LTSYNCDATE=:LTSYNCDATE, LTSYNCTIME=:LTSYNCTIME, ITEMSTATUS=:ITEMSTATUS, ITEMERROR=:ITEMERROR WHERE ITEMNO=:ITEMNO";
		
		$updateQuery1 = "UPDATE dbo.NewItemDetail SET LTSYNCDATE=:LTSYNCDATE, LTSYNCTIME=:LTSYNCTIME WHERE ITEMNO=:ITEMNO";
		
        $updateStmt = $this->_mssqlConnection->prepare($updateQuery);
		
		$updateStmt1 = $this->_mssqlConnection->prepare($updateQuery1);
		
		// to get all item from sage need to be imported

		//$query = "SELECT * FROM dbo.NewItemDetail WHERE ITEMSTATUS IS NULL";
		
		echo $query = "SELECT dbo.NewItem.*, dbo.NewItemDetail.COMPONENT, dbo.NewItemDetail.QUANTITY   FROM dbo.NewItem join dbo.NewItemDetail on dbo.NewItem.ITEMNO= dbo.NewItemDetail.ITEMNO  WHERE dbo.NewItem.ITEMSTATUS = '0' and dbo.NewItem.BUNDLE='1' and dbo.NewItem.MAGTYPE='Bundle Product'";
		 
		 

		$stmt = $this->_mssqlConnection->prepare($query); 
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		if(!empty($rows))
		{
			
		  $this->getAllAttributeSets();  // get all attribute sets
		  $bundleProducts = array();
		  $i=0;
		  foreach($rows as $row) {
			  $bundleProducts[trim($row['ITEMNO'])][$i]['COMPONENT'] = trim($row['COMPONENT']);
			  $bundleProducts[trim($row['ITEMNO'])][$i]['QUANTITY'] = trim($row['QUANTITY']);
			  if(isset($row['DESC']))
				  $bundleProducts[trim($row['ITEMNO'])][$i]['DESC'] = trim($row['DESC']);
			  else
				  $bundleProducts[trim($row['ITEMNO'])][$i]['DESC'] = "Package Product";
			  if(isset($row['TAXCODE']) && trim($row['TAXCODE']) == "1")
				  $bundleProducts[trim($row['ITEMNO'])][$i]['TAXCODE'] = 2;
			  else
				  $bundleProducts[trim($row['ITEMNO'])][$i]['TAXCODE'] = 0;
			  if(isset($row['ATTRSET']))
			  {
				  $attrbtSet = trim($row['ATTRSET']); 
				  if(isset($this->_attributeSets[$attrbtSet]))
					$bundleProducts[trim($row['ITEMNO'])][$i]['ATTRSET'] = $this->_attributeSets[$attrbtSet];
				  else
						$bundleProducts[trim($row['ITEMNO'])][$i]['ATTRSET'] = 4;
			  }
			  else
				  $bundleProducts[trim($row['ITEMNO'])][$i]['ATTRSET'] = 4;
			  
			  $bundleProducts[trim($row['ITEMNO'])][$i]['ICSTATUS'] = trim($row['ICSTATUS']);
		
			  $i++;
		  }
		 
		  foreach($bundleProducts as $bundleSku=>$bundleChild) {
			  $result = array();
			  $prodError = 0;
			  try {
				   $existingProd = $this->ifProductExist($bundleSku);  
				   if($existingProd)
					{
						 $prodError = 1;
						 $result['status'] = false;
						 $result['ITEMSTATUS'] = 2;
						 $result['ITEMERROR'] = $bundleSku.": product already exist"; 
					}
					else
					{
						
						foreach($bundleChild as $child)
						{
							$existingChildProd = $this->ifProductExist($child['COMPONENT']);
							
							if(!$existingChildProd)
							{
								 $prodError = 1;
								 $result['status'] = false;
								 $result['ITEMSTATUS'] = 2;
								 $result['ITEMERROR'] = $child['COMPONENT'].": child product does not exist"; 
							}
							$bundleDesc = $child['DESC'];
							$bundleTax = $child['TAXCODE'];
							$attrSet = $child['ATTRSET'];
							$ICSTATUS = $child['ICSTATUS'];
						}
						
						if($prodError == 0)
						{
							try{
								$links = $this->getBundleLinks($bundleChild);
								
								
								$product = $this->_objectManager->create(\Magento\Catalog\Api\Data\ProductInterface::class);
                                $urlKey = $this->cleanString(trim($bundleDesc)."-".trim($bundleSku)); 
								$product->setSku($bundleSku); 
								$product->setName($bundleDesc); 
								$product->setAttributeSetId($attrSet);
								$product->setUrlKey($urlKey);
								$product->setStatus(2); 
								$product->setWeight(0); 
								$product->setVisibility(4); 
								$product->setCustomAttribute('tax_class_id', $bundleTax); 
								$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE); 
								$product->setPrice(0);   
								$product->setWeightType(1);
								$product->setPriceType(1);
								$product->setSkuType(1);
								$product->setSageSyncedDate(Date('d-m-Y'));			
								$product->setSupplierCode(trim($row['SUPPLIER']));			
								$product->setBacktoback(trim($row['BACKTOBACK'])); 
								$product->setIsserializeditem(trim($row['SERIALNO'])); 
								$product->setVendorEmailAddress(trim($row['VENEMAIL'])); 
								$product->setStockloc(trim($row['STOCKLOC']));
								$product->setIsStockItem($ICSTATUS);
								$product->setStockData(
									array(
										'use_config_manage_stock' => 0,
										'manage_stock' => 0
									)
								);
								$product->save();
								
								$optionRepository = $this->_objectManager->create(\Magento\Bundle\Api\ProductOptionRepositoryInterface::class);
								
								$option = $this->_objectManager->create(\Magento\Bundle\Api\Data\OptionInterface::class);
								
								$option->setTitle('Package Details');
								$option->setType('checkbox');
								$option->setRequired(true);
								$option->setPosition(1);
								$option->setProductLinks($links);
								//$optionRepository->save($product, true);
								$optionRepository->save($product, $option);
								
								 $result['status'] = true;
								 $result['ITEMSTATUS'] = 1;
								 $result['ITEMERROR'] = $bundleSku.": imported successfully"; 
							}
							catch(Exception $e) 
							  {
								 $result['status'] = false;
								 $result['ITEMSTATUS'] = 2;
								 $result['ITEMERROR'] = $bundleSku.": Error: ".$e->getMessage(); 
								 $this->_logger->info('--Error while creating product--: '.$e->getMessage());
							  }
							
							
							
						}
						
					}
			  }
			  catch(Exception $e)
			  {
				  $result['status'] = false;
				  $result['ITEMSTATUS'] = 2;
				  $result['ITEMERROR'] = $bundleSku.": Error: ".$e->getMessage(); 
				  $this->_logger->info('Error while creating product: '.$e->getMessage());
			  }
			$date = date('Y-m-d');
			$time = date('H:i:s');
			
			$params = array(
						'ITEMSTATUS' => $result['ITEMSTATUS'],
						'ITEMERROR' => $result['ITEMERROR'],
						'LTSYNCDATE' => $date,
						'LTSYNCTIME' => $time,
						'ITEMNO' => $bundleSku
					       );
			
			$updateStmt->execute($params);
			
			$params1 = array(
			            'ITEMSTATUS' => $result['ITEMSTATUS'],
						'LTSYNCDATE' => $date,
						'LTSYNCTIME' => $time,
						'ITEMNO' => $bundleSku
					       );
			
			$updateStmt1->execute($params1);
			
			 if($result['status'])
			 {
			   $success++;
			 }
			 else{
				 $error++;
			 }
			 break;
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
		
		if(isset($row['TAXCODE']) && trim($row['TAXCODE']) == "GST")
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
			$product->setStatus(0); 
			$product->setWeight(0); 
			$product->setVisibility(4);
			$product->setTaxClassId($taxCode); 
			$product->setTypeId($prodType);
			$product->setPrice(0); 
			$product->setUrlKey($urlKey); 
			$product->setBacktoback(trim($row['BACKTOBACK'])); 
			$product->setVendorEmailAddress(trim($row['VENEMAIL'])); 
			$product->setStockloc(trim($row['STOCKLOC'])); 
			$product->setSerialno(trim($row['SERIALNO'])); 
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
		catch(Exception $e)
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