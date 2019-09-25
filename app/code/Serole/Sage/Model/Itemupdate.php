<?php

namespace Serole\Sage\Model;

use \Magento\Framework\Model\AbstractModel;

class Itemupdate extends AbstractModel
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

	
	public function __construct(\Serole\Sage\Helper\Data $sageHelper) 
	{ 
	
	  $this->_sageHelper = $sageHelper;
	  
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
	  
	  $this->createLog('sage_ItemUpdate.log');
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
	
	public function getProductsku($id)
	{
        $this->_logger->info('getProductsku function start');
		
		$query = "select sku from catalog_product_entity where entity_id = '$id'";
	
		$stmt = $this->_mysqlConnection->prepare($query); 
	
		$stmt->execute();

		$row = $stmt->fetchAll();
	  
		if(count($row))
		{
			$this->_logger->info('sku:'.$row[0]['sku']." for product id:".$id);
			return $row[0]['sku'];
		}
		else
			return false;
	
	}

	
	public function getUpdatedItems()
	{	
		$this->_logger->info('getUpdatedItems function Start');
		
		$query = "SELECT * FROM product_name_update_trigger WHERE status = '0'";
		
		$stmt = $this->_mysqlConnection->prepare($query); 
		$stmt->execute();
		$rows = $stmt->fetchAll();
		
		if(!empty($rows))
		{	
          $prodId = array();	
		  foreach($rows as $row) {
			    $prodId[] = $row['product_id'];
			    $sku = $this->getProductsku($row['product_id']);
				$name = $this->cleanString($row['name']);
				$status = 0;
				$date = date('Y-m-d');
			    $time = date('H:i:s');
				$updatedData[]	= 	"('$sku','$name','$date','$time','$status')";	
		  }
		  $insertData = implode(",",$updatedData);
			try
			{
				$insertQuery = "INSERT INTO dbo.ItemUpdate (ITEMNO, [DESC], LTSYNCDATE, LTSYNCTIME, ITEMSTATUS) VALUES $insertData";
				
				$insertStmt = $this->_mssqlConnection->prepare($insertQuery);
				  
				$insertStmt->execute();
				
				$pIds = implode(",",$prodId);
				
				$updateQuery = "UPDATE product_name_update_trigger SET status='1' where product_id IN($pIds)";
				
				$updateStmt = $this->_mysqlConnection->prepare($updateQuery);
				  
				$updateStmt->execute();
				
				exit;
			}
			catch(Exception $e)
			{
				$this->_logger->info('Error while inserting' .$e->getMessage()); 
			}

	    } 
		else{
			$this->_logger->info('There is no records');
		}
		
		$this->_logger->info('getUpdatedItems function End');

	}
	
	public function cleanString($string) 
	{
	    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
   
} 