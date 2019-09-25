<?php

namespace Serole\Sage\Model;

use \Magento\Framework\Model\AbstractModel;

class Exportorder extends AbstractModel
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

	
	public function __construct(\Serole\Sage\Helper\Data $sageHelper,
								\Magento\Framework\Serialize\Serializer\Json $serialize
							   ) 
	{ 
	
	  $this->_sageHelper = $sageHelper;
	  
	  $this->_serialize = $serialize;
	  
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
	  $this->createLog('sage_exportOrders.log');
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
             $this->_logger->info("connected to Mssql");			 
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
             $this->_logger->info("connected to Mysql");			 
		}catch (PDOException $e) {
             $this->_logger->info('Connection failed to Mysql: ' . $e->getMessage());	
		}
	}
	
	// Return last 24 hours credit memos from given date 
	public function getCreditMemos($date) { 
	
	    //$this->createLog('sage_processingOrderExport_cron.log');
	  
		$form = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) ));
		$fromDateTime = $form." 16:00:00";
		$toDateTime = $date." 15:59:59";
		
		$query = "SELECT sc.*, fc.amount as base_fooman_surcharge_amount, 
		          fc.tax_amount as fooman_surcharge_tax_amount
            	  FROM `sales_creditmemo` as sc 
		          LEFT JOIN fooman_totals_creditmemo as fc ON (sc.entity_id = fc.creditmemo_id)
		          WHERE (sc.created_at BETWEEN '$fromDateTime' AND '$toDateTime') and sc.is_m1_order='0'
				  ORDER BY sc.created_at DESC";
			
		$this->_logger->info("CreditMemo Query started");
	    $this->_logger->info($query);
	    $this->_logger->info("CreditMemo Query Ended");
		
		$stmt = $this->_mysqlConnection->prepare($query); 
		$stmt->execute();
		$cm = 0;
		  while ($row = $stmt->fetch()) {
            $this->addCmToSalesRecordHeader($row);
			$this->getAndAddCMItems($row);
			$this->getAndAddCMPaymentDetails($row);
			$cm++;
		  }
		  $this->_logger->info("Total processed CreditMemo =".$cm);
		  
		 //$this->createLog('sage_exportOrders.log');

	}
	
	// Return last 24 hours orders from given date 
	public function getMagentoOrders($date) { 
	  
		$form = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) ));
		$fromDateTime = $form." 16:00:00";
		$toDateTime = $date." 15:59:59";
		
		$query = "SELECT so.*, fi.amount as base_fooman_surcharge_amount, 
		          fi.tax_amount as fooman_surcharge_tax_amount
		          FROM `sales_order` as so 
		          LEFT JOIN fooman_totals_invoice as fi 
				  ON (so.entity_id = fi.order_id)
		          WHERE (so.created_at BETWEEN '$fromDateTime' AND '$toDateTime') and so.is_m1_order='0' 
				  AND so.status IN ('complete','processing') ORDER BY so.created_at DESC";
				  
		$this->_logger->info("OrderExport Query started");
	    $this->_logger->info($query);
	    $this->_logger->info("OrderExport Query Ended");
		
						   
		$stmt = $this->_mysqlConnection->prepare($query); 
		$stmt->execute();
		$ord = 0;
		  while ($row = $stmt->fetch()) {
            $this->addToSalesRecordHeader($row);
			$this->getAndAddOrderItems($row);
			$this->getAndAddOrderPaymentDetails($row);
			$ord++;
		  }
		  $this->_logger->info("Total processed Orders =".$ord);
		  $this->getMagentoFreeOrders($fromDateTime, $toDateTime);
	}
	
	// Return last 24 hours free orders from given date 
	public function getMagentoFreeOrders($fromDateTime, $toDateTime) { 
	  
		$query = "SELECT so.*, fi.amount as base_fooman_surcharge_amount, 
		          fi.tax_amount as fooman_surcharge_tax_amount
		          FROM `sales_order` as so 
				  JOIN `sales_order_payment` as sop
				  ON (so.entity_id = sop.parent_id)
		          LEFT JOIN fooman_totals_invoice as fi 
				  ON (so.entity_id = fi.order_id)
		          WHERE (so.created_at BETWEEN '$fromDateTime' AND '$toDateTime') and so.is_m1_order='0' 
				  AND so.status = 'closed' and sop.method = 'free' ORDER BY so.created_at DESC";
				  
		$this->_logger->info("OrderExport with free method Query started");
	    $this->_logger->info($query);
	    $this->_logger->info("OrderExport with free method Query Ended");
				
		$stmt = $this->_mysqlConnection->prepare($query); 
		$stmt->execute();
		$frOrd=0;
		  while ($row = $stmt->fetch()) {
            $this->addToSalesRecordHeader($row);
			$this->getAndAddOrderItems($row);
			$this->getAndAddOrderPaymentDetails($row);
			$frOrd++;
		  }
		  $this->_logger->info("Total processed Orders(Free) =".$frOrd);
	}
	
	// Return single order details 
	public function getSingleMagentoOrders($orderId) { 
	  			   
		//$query = "SELECT * FROM `sales_order` WHERE increment_id='$orderId'";
						   
		$query = "SELECT so.*, fi.amount as base_fooman_surcharge_amount, 
		          fi.tax_amount as fooman_surcharge_tax_amount
		          FROM `sales_order` as so 
		          LEFT JOIN fooman_totals_invoice as fi 
				  ON (so.entity_id = fi.order_id)
		          WHERE so.increment_id='$orderId'";
						   
		$stmt = $this->_mysqlConnection->prepare($query); 
		$stmt->execute();
		  while ($row = $stmt->fetch()) {
            $this->addToSalesRecordHeader($row);
			$this->getAndAddOrderItems($row);
			$this->getAndAddOrderPaymentDetails($row);
		  }

	}
	
	// Return single order credit memo 
	public function pushSingleCM($orderId) { 
	  			   
		//$query = "SELECT * FROM `sales_creditmemo` WHERE order_id='$orderId'";
						   
		$query = "SELECT sc.*, fc.amount as base_fooman_surcharge_amount, 
		          fc.tax_amount as fooman_surcharge_tax_amount
            	  FROM `sales_creditmemo` as sc 
		          LEFT JOIN fooman_totals_creditmemo as fc ON (sc.entity_id = fc.creditmemo_id)
		          WHERE  sc.order_id='$orderId'";
		
		$stmt = $this->_mysqlConnection->prepare($query); 
		$stmt->execute();
		
		  while ($row = $stmt->fetch()) {
            $this->addCmToSalesRecordHeader($row);
			$this->getAndAddCMItems($row);
			$this->getAndAddCMPaymentDetails($row);
		  }

	}
	
	public function getOrderIdByIncrementId($incrementId)
	{
			$query = "SELECT * FROM `sales_order` 
					   WHERE  increment_id='$incrementId'";
					   
			$stmt = $this->_mysqlConnection->prepare($query); 
			$stmt->execute();
			$row = $stmt->fetch();
			return $row['entity_id'];
	}
	
	public function processProcessingOrders()
	{
		    $selectInfoQuery = "SELECT * FROM sage_integration where sales_header = '0' AND sales_details = '0' AND sales_serialcode = '0' AND payment_receipt = '1'";
			
			$selctQry = $this->_mysqlConnection->prepare($selectInfoQuery);
			$selctQry->execute();

			while ($processRow = $selctQry->fetch()) {
		
				$oId = $processRow['orderid'];
				
				//$query = "SELECT * FROM `sales_order`  WHERE increment_id='$oId'";
				
				$query = "SELECT so.*, fi.amount as base_fooman_surcharge_amount, 
		          fi.tax_amount as fooman_surcharge_tax_amount
		          FROM `sales_order` as so 
		          LEFT JOIN fooman_totals_invoice as fi 
				  ON (so.entity_id = fi.order_id)
		          WHERE so.increment_id='$oId'";
						   
				$stmt = $this->_mysqlConnection->prepare($query); 
		        $stmt->execute();
			    $row = $stmt->fetch();
				$this->addToSalesRecordHeader($row);
				$this->getAndAddOrderItems($row);
		  }
	}
	
	protected function getOrderDetails($orderId)
	{
		$query = "SELECT so.*, fi.amount as base_fooman_surcharge_amount, 
		          fi.tax_amount as fooman_surcharge_tax_amount
		          FROM `sales_order` as so 
		          LEFT JOIN fooman_totals_invoice as fi 
				  ON (so.entity_id = fi.order_id) 
		                   WHERE so.entity_id='$orderId'";
						   
		$stmt = $this->_mysqlConnection->prepare($query); 
		$stmt->execute();
		$row = $stmt->fetch();
		return $row;
	}
	
	protected function getStoreDetails($storeId)
	{
		$query = "SELECT website.code as code FROM `store_website` as website LEFT JOIN `store` as store ON website.website_id = store.website_id WHERE store.store_id='$storeId'";
			   
		$stmt = $this->_mysqlConnection->prepare($query); 
		$stmt->execute();
		$storeDetails = $row = $stmt->fetch();
		return $storeDetails['code'];
	}
	
	protected function isOrderPosted($orderId)
	{
		$selectInfoQuery = "SELECT * FROM sage_integration where orderid = '$orderId'";
		
		$selctQry = $this->_mysqlConnection->prepare($selectInfoQuery);
		$selctQry->execute();
		$existingData =  $selctQry->fetch(); 
		
		//record in integration table in magento 
		if(isset($existingData) && is_array($existingData) && count($existingData) > 1 && $existingData['sales_header'] == 1) {
			return true; 
		}
		else{
			return false;
		}
	}
	
	protected function addCmToSalesRecordHeader($row)
	{
		
		 
			$creditMemoId = $row['increment_id'];
			
			$orderDetails = $this->getOrderDetails($row['order_id']);
		
			$orderId = $orderDetails['increment_id'];
			
			$isOrderPosted = $this->isOrderPosted($orderId);
			
		    if(!$isOrderPosted) {
				 $this->addToSalesRecordHeader($orderDetails);
			     $this->getAndAddOrderItems($orderDetails);
			     $this->getAndAddOrderPaymentDetails($orderDetails); 
			}
			
			
			$billingAddId = $row['billing_address_id'];
			$shippingAddId = $row['shipping_address_id'];
			
			$billingAddress = $this->_mysqlConnection->query("SELECT * FROM sales_order_address where entity_id='$billingAddId'")->fetch();
			
			$shippingAddress = $this->_mysqlConnection->query("SELECT * FROM sales_order_address where entity_id='$shippingAddId'")->fetch();
			
			$orderData['DOCTYPE'] = 2;
			
			$orderData['ORDERNO'] = $orderDetails['increment_id'];
			
			$orderData['CRMEMONO'] = $row['increment_id'];
			
			$orderData['IDSUBSTORE'] = $this->getStoreDetails($row['store_id']);

			$orderData['IDCUST'] =  $orderDetails['customer_id'];
			
			$orderData['DATEORDER'] = date("Ymd", strtotime('+8 hours', strtotime($row['created_at'])));
			
			$orderData['DATEENTRY'] = date("Ymd", strtotime('+8 hours', strtotime($row['created_at'])));
			$orderData['DATEDELIV'] = date("Ymd", strtotime('+8 hours', strtotime($row['created_at'])));
			
			$orderData['NAMECUST'] = $orderDetails['customer_firstname']." ".$orderDetails['customer_lastname'];
			
			
			$orderData['ORDCURR'] = "AUD";
			$orderData['ORDRATE'] = 1;
			
			$orderData['EMAIL'] = $billingAddress['email'];
			$orderData['BILADDR1'] = $billingAddress['street'];
			$orderData['BILCITY'] =  $billingAddress['city'];
			$orderData['BILSTATE'] = $billingAddress['region'];
			$orderData['BILZIP'] = $billingAddress['postcode'];
			$orderData['BILCOUNTRY'] =  $billingAddress['country_id'];
			
			$orderData['SHPNAME'] = $shippingAddress['firstname']. " ".$shippingAddress['lastname'];
			$orderData['SHPADDR1'] = $shippingAddress['street'];;
			$orderData['SHPCITY'] = $shippingAddress['city'];
			$orderData['SHPSTATE'] = $shippingAddress['region'];
			$orderData['SHPZIP'] =  $shippingAddress['postcode'];
			$orderData['SHPCOUNTRY'] =  $shippingAddress['country_id'];
			if($row['shipping_amount'] > 0)
			   $orderData['SHPREQUIRED'] =  1;
		    else
			   $orderData['SHPREQUIRED'] =  0;
		   
		    $memberQuery = "SELECT * FROM `customer_entity_varchar` 
		                   WHERE attribute_id='213' and entity_id='".$orderDetails['customer_id']."'";
						   
		    $memberData = $this->_mysqlConnection->prepare($memberQuery);
			$memberData->execute();
			$memberRow = $memberData->fetch();

			if(isset($memberRow['value']))
			  $orderData['MEMBERNO'] =  $memberRow['value'];
		  
		    if(!isset($row['base_fooman_surcharge_amount']) || is_null($row['base_fooman_surcharge_amount']))
				$row['base_fooman_surcharge_amount'] = 0.00;
			
			 if(!isset($row['fooman_surcharge_tax_amount']) || is_null($row['fooman_surcharge_tax_amount']))
				$row['fooman_surcharge_tax_amount'] = 0.00;
			
			$orderData['TOTORDAMT'] =  round(($row['subtotal_incl_tax']+$row['shipping_incl_tax']+$row['base_fooman_surcharge_amount']+$row['fooman_surcharge_tax_amount'])-$row['tax_amount'],2);

			$orderData['TOTTAXAMT'] =  round($row['tax_amount'],2);
			
			$orderData['TOTDISCAMT'] = abs(round($row['discount_amount'],2));
			
			$orderData['TOTNETAMT'] =  round($row['grand_total'],2);
			
			$orderData['ORDSTATUS'] =  0;
			
			$selectInfoQuery = "SELECT * FROM sage_integration where orderid = '$orderId'";
			
			$this->_logger->info('selectInfoQuery: '.$selectInfoQuery); 
			
			$selctQry = $this->_mysqlConnection->prepare($selectInfoQuery);
			$selctQry->execute();
			$existingData =  $selctQry->fetch(); 
			
			try{
				if(isset($existingData) && $existingData['credit_memo_header'] != 1)
				 {
					  $tableFields = implode(",",array_keys($orderData));
				
					  $tableValues = '"' . implode ( '", "', $orderData ) . '"';  
						
					  $query = "SET QUOTED_IDENTIFIER OFF; INSERT INTO dbo.SalesRecordHeader ($tableFields) VALUES($tableValues); SET QUOTED_IDENTIFIER ON;";
					  $this->_logger->info('query: '.$query); 
					  $result = $this->_mssqlConnection->query($query);
					  
					   if(isset($result))
						 {
							 $orderId = $orderData['ORDERNO'];
							 $this->_logger->info('Added CreditMemo: '.$orderData['CRMEMONO']);
							 $updateInfoQuery = "UPDATE sage_integration SET credit_memo_header='1' where orderid ='$orderId'";
							 $this->_mysqlConnection->query($updateInfoQuery); 
						 }
					   else
							$this->_logger->info('Failed CreditMemo: '.$orderData['CRMEMONO']);
				 }
				
			}catch (PDOException $e) {
				 $this->_logger->info('Error during insert CM into SalesRecordHeader : ' . $e->getMessage());	
			}
	}
	
	protected function addToSalesRecordHeader($row)
	{
		
		 //print_r($row);
			$orderId = $row['increment_id'];
			
			$selectInfoQuery = "SELECT * FROM sage_integration where orderid = '$orderId'";
			
			$selctQry = $this->_mysqlConnection->prepare($selectInfoQuery);
			$selctQry->execute();
			$existingData =  $selctQry->fetch(); 
			
            // inserting record in integration table in magento
		    if($existingData && isset($existingData) && count($existingData) > 1) {
				//return;
			}
			else{
				$insertInfoQuery = "INSERT INTO sage_integration (orderid) values('$orderId')";
				$infoQueryResult = $this->_mysqlConnection->query($insertInfoQuery);
			}
			
			 if($row['status'] == "processing") 
			    return;
			
			$billingAddId = $row['billing_address_id'];
			$shippingAddId = $row['shipping_address_id'];
			
			$billingAddress = $this->_mysqlConnection->query("SELECT * FROM sales_order_address where entity_id='$billingAddId'")->fetch();
			
			$shippingAddress = $this->_mysqlConnection->query("SELECT * FROM sales_order_address where entity_id='$shippingAddId'")->fetch();
			
			$orderData['DOCTYPE'] = 1;
			
			$orderData['ORDERNO'] = $row['increment_id'];
			
			$orderData['IDSUBSTORE'] = $this->getStoreDetails($row['store_id']);

			$orderData['IDCUST'] =  $row['customer_id'];
			$orderData['DATEORDER'] = date("Ymd", strtotime('+8 hours', strtotime($row['created_at'])));
			
			$orderData['DATEENTRY'] = date("Ymd", strtotime('+8 hours', strtotime($row['created_at'])));
			$orderData['DATEDELIV'] = date("Ymd", strtotime('+8 hours', strtotime($row['created_at'])));
			
			$orderData['NAMECUST'] = $row['customer_firstname']." ".$row['customer_lastname'];
			
			
			$orderData['ORDCURR'] = "AUD";
			$orderData['ORDRATE'] = 1;
			
			$orderData['EMAIL'] = $billingAddress['email'];
			$orderData['BILADDR1'] = $billingAddress['street'];
			$orderData['BILCITY'] =  $billingAddress['city'];
			$orderData['BILSTATE'] = $billingAddress['region'];
			$orderData['BILZIP'] = $billingAddress['postcode'];
			$orderData['BILCOUNTRY'] =  $billingAddress['country_id'];
			
			$orderData['SHPNAME'] = $shippingAddress['firstname']. " ".$shippingAddress['lastname'];
			$orderData['SHPADDR1'] = $shippingAddress['street'];;
			$orderData['SHPCITY'] = $shippingAddress['city'];
			$orderData['SHPSTATE'] = $shippingAddress['region'];
			$orderData['SHPZIP'] =  $shippingAddress['postcode'];
			$orderData['SHPCOUNTRY'] =  $shippingAddress['country_id'];
			if($row['shipping_amount'] > 0)
			   $orderData['SHPREQUIRED'] =  1;
		    else
			   $orderData['SHPREQUIRED'] =  0;
		   
		    $memberQuery = "SELECT * FROM `customer_entity_varchar` 
		                   WHERE attribute_id='169' and entity_id='".$row['customer_id']."'";
						   
		    $memberData = $this->_mysqlConnection->prepare($memberQuery);
			$memberData->execute();
			$memberRow = $memberData->fetch();

			if(isset($memberRow['value']))
			   $orderData['MEMBERNO'] =  $memberRow['value'];
		   
		    if(!isset($row['base_fooman_surcharge_amount']) || is_null($row['base_fooman_surcharge_amount']))
				$row['base_fooman_surcharge_amount'] = 0.00;
			
			if(!isset($row['fooman_surcharge_tax_amount']) || is_null($row['fooman_surcharge_tax_amount']))
				$row['fooman_surcharge_tax_amount'] = 0.00;
			
			$orderData['TOTORDAMT'] =  round(($row['subtotal_incl_tax']+$row['shipping_incl_tax']+$row['base_fooman_surcharge_amount']+$row['fooman_surcharge_tax_amount'])-$row['tax_amount'],2);

			$orderData['TOTTAXAMT'] =  round($row['tax_amount'],2);
			
			$orderData['TOTDISCAMT'] = abs(round($row['discount_amount'],2));
			
			$orderData['TOTNETAMT'] =  round($row['grand_total'],2);
			
			$orderData['ORDSTATUS'] =  0;
			
			$selectInfoQuery = "SELECT * FROM sage_integration where orderid = '$orderId'";
			
			$selctQry = $this->_mysqlConnection->prepare($selectInfoQuery);
			$selctQry->execute();
			$existingData =  $selctQry->fetch(); 

			$tableFields = implode(",",array_keys($orderData));
			
			$tableValues = '"' . implode ( '", "', $orderData ) . '"';  
			
			$query = "SET QUOTED_IDENTIFIER OFF; INSERT INTO dbo.SalesRecordHeader ($tableFields) VALUES($tableValues); SET QUOTED_IDENTIFIER ON;";
			
			try{
				if(!isset($existingData) || $existingData['sales_header'] != 1)
				 {
				  $result = $this->_mssqlConnection->query($query);
				 }
				 if(isset($result))
				 {
					 $orderId = $orderData['ORDERNO'];
					 $this->_logger->info('Added order: '.$orderData['ORDERNO']);
					 $updateInfoQuery = "UPDATE sage_integration SET sales_header='1' where orderid ='$orderId'";
					 $this->_mysqlConnection->query($updateInfoQuery); 
				 }
				 else
					$this->_logger->info('Failed order: '.$orderData['ORDERNO']);
			}catch (PDOException $e) {
				 $this->_logger->info('Error during insert into SalesRecordHeader : ' . $e->getMessage());	
			}
	}
	
	protected function getItemSerialCodes($sku, $orderId, $itemType, $orderType)
	{
		//$this->createLog('sage__order_item.log');
		$serialCodesQuery = "SELECT * FROM `order_item_serialcode` WHERE OrderID='$orderId'";
		
		$this->_logger->info('orderType:'.$orderType);
		
		$where = '';
		
			if($orderType == "creditmemo")
			{
				$where .= " and status = '0'";
			}
			
			if($orderType == "order")
			{
				$where .= " and status != '2'";
			}
			
			$this->_logger->info('SKU:'.$sku.' itemType:'.$itemType);
		/*
		    if($itemType == "virtual" || $itemType == "grouped" || $itemType == "simple")
			{
		      $where .= " and sku='$sku' and parentsku=''";
			}
		*/	
		    if($itemType == "bundle")
			{
			   $where .= " and parentsku='$sku'";
			}
			else
			{
				$where .= " and sku='$sku' and parentsku=''";
			}
			
			$serialCodesQuery = $serialCodesQuery.$where;
			
			$this->_logger->info('SerialCode Query:'.$serialCodesQuery);
			
		    $serialCodesData = $this->_mysqlConnection->prepare($serialCodesQuery);
			$serialCodesData->execute();
			$serialCodes = $serialCodesData->fetchAll();
			return $serialCodes;
			
	}
	
	protected function getAndAddOrderPaymentDetails($order)
	{
		    //$this->createLog('sage_payment_reiept.log');
			
		    $orderIncrementId = $order['increment_id'];
			
			$this->_logger->info('Order Id:'.$orderIncrementId);
			
		    $selectInfoQuery = "SELECT * FROM sage_integration where orderid = '$orderIncrementId'";
			
			$this->_logger->info('Integration table Query:'.$selectInfoQuery);
		
			$selctQry = $this->_mysqlConnection->prepare($selectInfoQuery);
			$selctQry->execute();
			$existingData =  $selctQry->fetch(); 
			
			if ($existingData && isset($existingData) && $existingData['payment_receipt'] ==1) {
				$this->_logger->info('record already exist');
				 return;
			}
			
		    $orderId = $order['entity_id'];
			
			
		    $paymentQuery = "SELECT * FROM `sales_order_payment` 
		                   WHERE parent_id='$orderId'";
			
			$this->_logger->info('payment table Query:'.$paymentQuery);
						   
		    $paymentData = $this->_mysqlConnection->prepare($paymentQuery);
			$paymentData->execute();
			$paymentRow = $paymentData->fetch();
			
			if(!empty($paymentRow))
			{
				$paymentInsertData['DOCTYPE'] = 1;
				$paymentInsertData['ORDERNO'] = $orderIncrementId;
				$paymentInsertData['IDCUST'] = $order['customer_id'];
				$paymentInsertData['IDSUBSTORE'] = $this->getStoreDetails($order['store_id']);
				$paymentInsertData['IDBANK'] = "Braintree";
				$paymentInsertData['IDRMIT'] = $paymentRow['last_trans_id'];
				$paymentInsertData['TEXTRMIT'] = $order['customer_firstname']." ".$order['customer_lastname'];
				$paymentInsertData['PAYCURR'] = "AUD";
				$paymentInsertData['PAYRATE'] = 1;
				$paymentInsertData['DATEORDER'] = date("Ymd", strtotime('+8 hours', strtotime($order['created_at'])));;
				$paymentInsertData['DATEENTRY'] = date("Ymd", strtotime('+8 hours', strtotime($order['created_at'])));;
				$paymentInsertData['DATEPAY'] = date("Ymd", strtotime('+8 hours', strtotime($order['created_at'])));;
				if($paymentRow['method'] == "braintree" || $paymentRow['method'] == "free" || $paymentRow['method'] == "braintree_paypal")
				  $paymentInsertData['MODEPAY'] = "Online";
			    else
				  $paymentInsertData['MODEPAY'] = "Offline";
			  
			    if($paymentRow['method'] != "accountpayment")
				  $paymentInsertData['TYPEPAY'] = $paymentRow['method'];
			 
				$paymentInsertData['TOTNETAMT'] = round($paymentRow['amount_paid'],2);
				$paymentInsertData['TOTPAYAMT'] = round($paymentRow['amount_paid'],2);
				$paymentInsertData['PAYSTATUS'] =0;
				
				$this->_logger->info($paymentInsertData);
				$this->addToReceiptRecord($paymentInsertData);
			}
			
			
	}
	
	protected function getAndAddCMPaymentDetails($row)
	{
		    $creditMemoId = $row['increment_id'];
			
			$order = $this->getOrderDetails($row['order_id']);
			
			$orderIncrementId = $order['increment_id'];
			
		    $selectInfoQuery = "SELECT * FROM sage_integration where orderid = '$orderIncrementId'";
		
			$selctQry = $this->_mysqlConnection->prepare($selectInfoQuery);
			$selctQry->execute();
			$existingData =  $selctQry->fetch(); 
			
			if (isset($existingData) && $existingData['credit_memo_receipt'] ==1) {
				 return;
			}

				$paymentInsertData['DOCTYPE'] = 2;
				$paymentInsertData['ORDERNO'] = $orderIncrementId;
				$paymentInsertData['CRMEMONO'] = $row['increment_id'];
				$paymentInsertData['IDCUST'] = $order['customer_id'];
				$paymentInsertData['IDSUBSTORE'] = $this->getStoreDetails($order['store_id']);
				$paymentInsertData['TEXTRMIT'] = $order['customer_firstname']." ".$order['customer_lastname'];
				$paymentInsertData['PAYCURR'] = "AUD";
				$paymentInsertData['PAYRATE'] = 1;
				$paymentInsertData['DATEORDER'] = date("Ymd", strtotime('+8 hours', strtotime($row['created_at'])));;
				$paymentInsertData['DATEENTRY'] = date("Ymd", strtotime('+8 hours', strtotime($row['created_at'])));;
				$paymentInsertData['DATEPAY'] = date("Ymd", strtotime('+8 hours', strtotime($row['created_at'])));
				$paymentInsertData['MODEPAY'] = "Offline";
			
				$paymentInsertData['TYPEPAY'] = "Refund";
				$paymentInsertData['TOTNETAMT'] = round($row['grand_total'],2);
				$paymentInsertData['TOTPAYAMT'] = round($row['grand_total'],2);
				$paymentInsertData['PAYSTATUS'] =0;
				
				$this->addToReceiptRecord($paymentInsertData);
			
			
	}
	
	
	public function getAndAddCMItems($creditMemo) { 
	 
		$cmId = $creditMemo['entity_id'];
		
		$orderDetails = $this->getOrderDetails($creditMemo['order_id']);
		
		$orderIncrementId = $orderDetails['increment_id'];
		
		$selectInfoQuery = "SELECT * FROM sage_integration where orderid = '$orderIncrementId'";
		
		$selctQry = $this->_mysqlConnection->prepare($selectInfoQuery);
		$selctQry->execute();
		$existingData =  $selctQry->fetch();  

		
		$query = "SELECT * FROM `sales_creditmemo_item` 
		                   WHERE parent_id = '$cmId' and base_price > 0";
						   
		$stmt = $this->_mysqlConnection->prepare($query);
		$stmt->execute();
		$i=1;
        $variance = 0;
		while ($row = $stmt->fetch()) {
			//print_r($row);
			//exit;
			$itemLine = $i;
			$i++;
			$orderItemData = array();
			$orderItemData['DOCTYPE'] = 2;
            $orderItemData['ORDERNO'] = $orderIncrementId;
			$orderItemData['CRMEMONO'] = $creditMemo['increment_id'];
			$orderItemData['LINENUM'] = $itemLine;
			$orderItemData['ITEMNO'] = $row['sku'];
			$orderItemData['[DESC]'] = $row['name'];
			$orderItemData['LOCATION'] = "WH";
			$orderItemData['QUANTITY'] = (int)$row['qty'];
			$orderItemData['PRICEUNIT'] = round($row['price'],2);
			$orderItemData['PRIDISCAMT'] = abs(round($row['discount_amount'],2));
			$orderItemData['EXTPRICE'] = round(($orderItemData['QUANTITY']*$row['price']),2);
			
			$variance = $variance + $orderItemData['EXTPRICE'];
			
			if($row['tax_amount']>0)
			{
			   $orderItemData['TAXAMT'] = round($row['tax_amount'],2);
			   $variance = $variance + $orderItemData['TAXAMT'];
			   $orderItemData['TAXCODE'] = "1"; //1 for GST
			}
		    else
			{
			   $orderItemData['TAXAMT'] = 0;
			   $orderItemData['TAXCODE'] = "2"; //2 for Purchases - GST Free
			}
		   
			if(!isset($existingData) || $existingData['credit_memo_details'] != 1)
			    $this->addToSalesRecordDetail($orderItemData);
			
			$itmId = $row['order_item_id'];
			
			$itemQuery = "SELECT product_type FROM `sales_order_item` 
		                   WHERE item_id='$itmId'"; 
						   
			  
			$itemStmt = $this->_mysqlConnection->prepare($itemQuery); 
			$itemStmt->execute();
			$prodType = $itemStmt->fetch();
			
			$serialCodes = $this->getItemSerialCodes($row['sku'], $orderIncrementId, $prodType['product_type'], "creditmemo");
			
			if(!empty($serialCodes) && isset($serialCodes))
			{
				$serialLine=1;
				
			    foreach($serialCodes as $sc)
				{
					$serialCodeArray = array();
					$serialCodeArray['DOCTYPE']=2;
					$serialCodeArray['ORDERNO']=$orderIncrementId;
					$serialCodeArray['CRMEMONO'] = $creditMemo['increment_id'];
					$serialCodeArray['LINENUM']=$itemLine;
					if(!empty($sc['parentsku']) && isset($sc['parentsku']) && $sc['parentsku'] != '')
					{
					 $serialCodeArray['ITEMNO']=$sc['parentsku'];
					 $serialCodeArray['COMPONENTS']=$sc['sku'];
					}
					else
					{
						$serialCodeArray['ITEMNO'] = $sc['sku'];
					}
					$serialCodeArray['SNLINENUM']=$serialLine++;
					$serialCodeArray['SERIALNUMBER']=trim($sc['SerialNumber']);
					
					if(!isset($existingData) || $existingData['credit_memo_serialcode'] != 1)
					   $this->addToSalesRecordProduct($serialCodeArray);
				}
			}
		  }
		  // credit card surcharge
		  //print_r($order);
		  if(isset($creditMemo['base_fooman_surcharge_amount']) && $creditMemo['base_fooman_surcharge_amount'] > 0)
		  {
			$orderItemData = array();
			$orderItemData['DOCTYPE'] = 2;
			$orderItemData['ORDERNO'] = $orderIncrementId;
			$orderItemData['CRMEMONO'] = $creditMemo['increment_id'];
			$orderItemData['LINENUM'] = $i++;
			$orderItemData['ITEMNO'] = "surcharge";
			$orderItemData['[DESC]'] = "Processing Charge";
			$orderItemData['LOCATION'] = "WH";
			$orderItemData['QUANTITY'] = 1;
			$orderItemData['PRICEUNIT'] = round($creditMemo['base_fooman_surcharge_amount'],2);
			$orderItemData['EXTPRICE'] = $orderItemData['PRICEUNIT'];
			//$orderItemData['PRIDISCAMT'] = $row['discount_amount'];
			$orderItemData['TAXAMT'] = round($creditMemo['fooman_surcharge_tax_amount'],2);
			$orderItemData['TAXCODE'] = "1";
			if(!isset($existingData) || $existingData['credit_memo_details'] != 1)
			  $this->addToSalesRecordDetail($orderItemData);
		      $variance = $variance + $orderItemData['EXTPRICE'] + $orderItemData['TAXAMT'];
		  }
		  
		  // shipping amount as different item
		  if($creditMemo['shipping_amount'] > 0)
		  {

			$orderItemData = array();
			$orderItemData['DOCTYPE'] = 2;
			$orderItemData['ORDERNO'] = $orderIncrementId;
			$orderItemData['CRMEMONO'] = $creditMemo['increment_id'];
			$orderItemData['LINENUM'] = $i++;
			$orderItemData['ITEMNO'] = "shipping";
			$orderItemData['[DESC]'] = $orderDetails['shipping_description'];
			$orderItemData['LOCATION'] = "WH";
			$orderItemData['QUANTITY'] = 1;
			$orderItemData['PRICEUNIT'] = round($creditMemo['shipping_amount'],2);
			$orderItemData['EXTPRICE'] = $orderItemData['PRICEUNIT'];
			//$orderItemData['PRIDISCAMT'] = $row['discount_amount'];
			$orderItemData['TAXAMT'] = round($creditMemo['shipping_tax_amount'],2);
			$orderItemData['TAXCODE'] = "1";
			if(!isset($existingData) || $existingData['credit_memo_details'] != 1)
			   $this->addToSalesRecordDetail($orderItemData);
		       $variance = $variance + $orderItemData['EXTPRICE'] + $orderItemData['TAXAMT'];
		  }
		  $discountVariance = 0;
		  // discount amount as different item
		  if(abs($creditMemo['discount_amount']) > 0)
		  {

			$orderItemData = array();
			$discountWithoutTax = round((abs($creditMemo['discount_amount'])/1.1),2);
			$discountTax = abs($creditMemo['discount_amount']) - $discountWithoutTax;
			$orderItemData['DOCTYPE'] = 2;
			$orderItemData['ORDERNO'] = $orderIncrementId;
			$orderItemData['CRMEMONO'] = $creditMemo['increment_id'];
			$orderItemData['LINENUM'] = $i++;
			$orderItemData['ITEMNO'] = "discount";
			$orderItemData['[DESC]'] = "Discount Amount";
			$orderItemData['LOCATION'] = "WH";
			$orderItemData['QUANTITY'] = -1;
			$orderItemData['PRICEUNIT'] = $discountWithoutTax;
			$orderItemData['EXTPRICE'] = $orderItemData['PRICEUNIT'];
			//$orderItemData['PRIDISCAMT'] = $row['discount_amount'];
			$orderItemData['TAXAMT'] = $discountTax;
			$orderItemData['TAXCODE'] = "1";
			if(!isset($existingData) || $existingData['credit_memo_details'] != 1)
			   $this->addToSalesRecordDetail($orderItemData);
		       $discountVariance = abs($creditMemo['discount_amount']) - ($discountWithoutTax+$discountTax);
		    $discAmt = abs($creditMemo['discount_amount']);
		    $grandTotal = round(($creditMemo['grand_total']+$discAmt),2);
		  }
		  else
		    $grandTotal = round($creditMemo['grand_total'],2);
		  
		  
		  $grandTotal = round($creditMemo['grand_total'],2);
		  
		  if($grandTotal > 0)
		  {
		    $roundingVariance = $grandTotal - $variance;
			$roundingVariance = $roundingVariance + $discountVariance;
			$isVariance = abs($roundingVariance);
			if($isVariance > 0)
			{
				$orderItemData = array();
				$orderItemData['DOCTYPE'] = 2;
				$orderItemData['ORDERNO'] = $orderIncrementId;
				$orderItemData['CRMEMONO'] = $creditMemo['increment_id'];
				$orderItemData['LINENUM'] = $i++;
				$orderItemData['ITEMNO'] = "variance";
				$orderItemData['[DESC]'] = "Rounding variance";
				$orderItemData['LOCATION'] = "WH";
				$orderItemData['QUANTITY'] = 1;
				$orderItemData['PRICEUNIT'] = $roundingVariance;
				$orderItemData['EXTPRICE'] = $roundingVariance;
				$orderItemData['TAXAMT'] = 0;
				$orderItemData['TAXCODE'] = "2";
				if(!isset($existingData) || $existingData['credit_memo_details'] != 1)
				   $this->addToSalesRecordDetail($orderItemData);
			}
		  } 
		  
			  $updateInfoQuery = "UPDATE sage_integration SET credit_memo_details='1' where orderid ='$orderIncrementId'";
			  $this->_mysqlConnection->query($updateInfoQuery); 
			  
			  $updateInfoQuery = "UPDATE sage_integration SET credit_memo_serialcode='1' where orderid ='$orderIncrementId'";
			  $this->_mysqlConnection->query($updateInfoQuery); 

	}
	
	
	public function getAndAddOrderItems($order) { 
	
	    //$this->createLog('sage_order_item.log');
	
	    if($order['status'] == "processing")
			 return;
		 
	
		$orderId = $order['entity_id'];
		
		$orderIncrementId = $order['increment_id'];
		
		$this->_logger->info('orderId:'.$orderIncrementId);
		
		$selectInfoQuery = "SELECT * FROM sage_integration where orderid = '$orderIncrementId'";
		
		$selctQry = $this->_mysqlConnection->prepare($selectInfoQuery);
		$selctQry->execute();
		$existingData =  $selctQry->fetch();  

		
		$query = "SELECT * FROM `sales_order_item` 
		                   WHERE (order_id = '$orderId' AND parent_item_id IS NULL)";
						   
	    $this->_logger->info('query:'.$query);
						   
		$stmt = $this->_mysqlConnection->prepare($query);
		$stmt->execute();
		$i=1;
        $variance = 0;
		while ($row = $stmt->fetch()) {
			
			$optionData = '';
			
			$productOptions = $this->_serialize->unserialize($row['product_options']);
			
			if(isset($productOptions['options']))
			{
				foreach($productOptions['options'] as $opt)
				{
					$optionData = $optionData.$opt['label'].":".$opt['value']."\n";
				}
			}
			$this->_logger->info('Sku:'.$row['sku']);
			$itemLine = $i;
			$i++;
			$orderItemData = array();
			$orderItemData['DOCTYPE'] = 1;
            $orderItemData['ORDERNO'] = $orderIncrementId;
			$orderItemData['LINENUM'] = $itemLine;
			$orderItemData['ITEMNO'] = $row['sku'];
			$orderItemData['[DESC]'] = $row['name'];
			$orderItemData['LOCATION'] = "WH";
			$orderItemData['QUANTITY'] = (int)$row['qty_ordered'];
			$orderItemData['PRICEUNIT'] = round($row['price'],2);
			//$orderItemData['PRIDISCAMT'] = abs(round($row['discount_amount'],2));
			$orderItemData['EXTPRICE'] = round(($orderItemData['QUANTITY']*$orderItemData['PRICEUNIT']),2);
			
			$variance = $variance + $orderItemData['EXTPRICE'];
			
			
			if($row['tax_amount']>0)
			{
			   $orderItemData['TAXAMT'] = round($row['tax_amount'],2);
			   $variance = $variance + $orderItemData['TAXAMT'];
			   $orderItemData['TAXCODE'] = "1"; //1 for GST
			}
		    else
			{
			   $orderItemData['TAXAMT'] = 0;
			   $orderItemData['TAXCODE'] = "2"; //2 for Purchases - GST Free
			}
			
			if(isset($productOptions['options']))
			{
				$orderItemData['AddText'] = $optionData;
			}
		   
			if(!isset($existingData) || $existingData['sales_details'] != 1)
			    $this->addToSalesRecordDetail($orderItemData);
			
			    $this->_logger->info('product_type:'.$row['product_type']);
			
		        $serialCodes = $this->getItemSerialCodes($row['sku'], $orderIncrementId, $row['product_type'], "order");
			  
			  //$this->_logger->info($serialCodes);
			
			if(!empty($serialCodes) && isset($serialCodes))
			{
				$serialLine=1;
				//echo "<pre>";
				//print_r($serialCodes);
				//exit;
				foreach($serialCodes as $sc)
				{
					$serialCodeArray = array();
					$serialCodeArray['DOCTYPE']=1;
					$serialCodeArray['ORDERNO']=$orderIncrementId;
					$serialCodeArray['LINENUM']=$itemLine;
					if(!empty($sc['parentsku']) && isset($sc['parentsku']) && $sc['parentsku'] != '')
					{
					 $serialCodeArray['ITEMNO']=$sc['parentsku'];
					 $serialCodeArray['COMPONENTS']=$sc['sku'];
					}
					else
					{
						$serialCodeArray['ITEMNO'] = $sc['sku'];
					}
					$serialCodeArray['SNLINENUM']=$serialLine++;
					$serialCodeArray['SERIALNUMBER']=trim($sc['SerialNumber']);
					
					if(!isset($existingData) || $existingData['sales_serialcode'] != 1)
					   $this->addToSalesRecordProduct($serialCodeArray);
				}
			}
		  }
		  // credit card surcharge
		  //print_r($order);
		  if(isset($order['base_fooman_surcharge_amount']) && $order['base_fooman_surcharge_amount'] > 0)
		  {
			$orderItemData = array();
			$orderItemData['DOCTYPE'] = 1;
			$orderItemData['ORDERNO'] = $orderIncrementId;
			$orderItemData['LINENUM'] = $i++;
			$orderItemData['ITEMNO'] = "surcharge";
			$orderItemData['[DESC]'] = "Processing Charge";
			$orderItemData['LOCATION'] = "WH";
			$orderItemData['QUANTITY'] = 1;
			$orderItemData['PRICEUNIT'] = round($order['base_fooman_surcharge_amount'],2);
			$orderItemData['EXTPRICE'] = $orderItemData['PRICEUNIT'];
			//$orderItemData['PRIDISCAMT'] = $row['discount_amount'];
			$orderItemData['TAXAMT'] = round($order['fooman_surcharge_tax_amount'],2);
			$orderItemData['TAXCODE'] = "1";
			
			
			
			if(!isset($existingData) || $existingData['sales_details'] != 1)
			  $this->addToSalesRecordDetail($orderItemData);
		      $variance = $variance + $orderItemData['EXTPRICE'] + $orderItemData['TAXAMT'];
		  }
		  
		  // shipping amount as different item
		  if($order['shipping_amount'] > 0)
		  {

			$orderItemData = array();
			$orderItemData['DOCTYPE'] = 1;
			$orderItemData['ORDERNO'] = $orderIncrementId;
			$orderItemData['LINENUM'] = $i++;
			$orderItemData['ITEMNO'] = "shipping";
			$orderItemData['[DESC]'] = $order['shipping_description'];
			$orderItemData['LOCATION'] = "WH";
			$orderItemData['QUANTITY'] = 1;
			$orderItemData['PRICEUNIT'] = round($order['shipping_amount'],2);
			$orderItemData['EXTPRICE'] = $orderItemData['PRICEUNIT'];
			//$orderItemData['PRIDISCAMT'] = $row['discount_amount'];
			$orderItemData['TAXAMT'] = round($order['shipping_tax_amount'],2);
			$orderItemData['TAXCODE'] = "1";
			if(!isset($existingData) || $existingData['sales_details'] != 1)
			   $this->addToSalesRecordDetail($orderItemData);
		       $variance = $variance + $orderItemData['EXTPRICE'] + $orderItemData['TAXAMT'];
		  }
		  $discountVariance = 0;
		  // discount amount as different item
		  if(abs($order['discount_amount']) > 0)
		  {
            
			$orderItemData = array();
			$discountWithoutTax = round((abs($order['discount_amount'])/1.1),2);
			$discountTax = round((abs($order['discount_amount'])-$discountWithoutTax),2);
			$orderItemData['DOCTYPE'] = 1;
			$orderItemData['ORDERNO'] = $orderIncrementId;
			$orderItemData['LINENUM'] = $i++;
			$orderItemData['ITEMNO'] = "discount";
			$orderItemData['[DESC]'] = "Discount Amount";
			$orderItemData['LOCATION'] = "WH";
			$orderItemData['QUANTITY'] = -1;
			$orderItemData['PRICEUNIT'] = $discountWithoutTax;
			$orderItemData['EXTPRICE'] = $orderItemData['PRICEUNIT'];
			$orderItemData['TAXAMT'] = $discountTax;
			$orderItemData['TAXCODE'] = "1";
			if(!isset($existingData) || $existingData['sales_details'] != 1)
			   $this->addToSalesRecordDetail($orderItemData);
		       $discountVariance = abs($order['discount_amount']) - ($discountWithoutTax+$discountTax);
			   $discAmt = abs($order['discount_amount']);
		       $grandTotal = round(($order['grand_total']+$discAmt),2);
		  }
		  else
		    $grandTotal = round($order['grand_total'],2);
		  
		  if($grandTotal > 0)
		  {
		    $roundingVariance = $grandTotal - $variance;
			$roundingVariance = $roundingVariance+$discountVariance;
			$isVariance = abs($roundingVariance);
			if($isVariance > 0)
			{
				$orderItemData = array();
				$orderItemData['DOCTYPE'] = 1;
				$orderItemData['ORDERNO'] = $orderIncrementId;
				$orderItemData['LINENUM'] = $i++;
				$orderItemData['ITEMNO'] = "variance";
				$orderItemData['[DESC]'] = "Rounding variance";
				$orderItemData['LOCATION'] = "WH";
				$orderItemData['QUANTITY'] = 1;
				$orderItemData['PRICEUNIT'] = $roundingVariance;
				$orderItemData['EXTPRICE'] = $roundingVariance;
				$orderItemData['TAXAMT'] = 0;
				$orderItemData['TAXCODE'] = "2";
				if(!isset($existingData) || $existingData['sales_details'] != 1)
				   $this->addToSalesRecordDetail($orderItemData);
			}
		  } 
		  
		  
			  $updateInfoQuery = "UPDATE sage_integration SET sales_details='1' where orderid ='$orderIncrementId'";
			  $this->_mysqlConnection->query($updateInfoQuery); 
			  
			  $updateInfoQuery = "UPDATE sage_integration SET sales_serialcode='1' where orderid ='$orderIncrementId'";
			  $this->_mysqlConnection->query($updateInfoQuery); 

	}
	
	protected function addToReceiptRecord($paymentInsertData)
	{
		//$this->createLog('sage_payment_reiept.log');

		$tableFields = implode(",",array_keys($paymentInsertData));
		//$tableValues = "'" . implode ( "', '", $paymentInsertData ) . "'"; 
		$tableValues = '"' . implode ( '", "', $paymentInsertData ) . '"';  
		
	    $query = "SET QUOTED_IDENTIFIER OFF; INSERT INTO dbo.ReceiptRecord ($tableFields) VALUES($tableValues); SET QUOTED_IDENTIFIER ON;  ";
		
		$this->_logger->info('reciept query:'.$query);
		
		try{
		     $result = $this->_mssqlConnection->query($query);
			 if(isset($result))
			 {
				 $orderIncrementId = $paymentInsertData['ORDERNO'];
				 $this->_logger->info("Added payment: of order id: ".$paymentInsertData['ORDERNO']);
				 
				 if($paymentInsertData['DOCTYPE'] ==2)
				    $updateInfoQuery = "UPDATE sage_integration SET credit_memo_receipt='1' where orderid ='$orderIncrementId'";
				else
					 $updateInfoQuery = "UPDATE sage_integration SET payment_receipt='1' where orderid ='$orderIncrementId'";
				
			     $this->_mysqlConnection->query($updateInfoQuery); 
			 }
			 else
				 $this->_logger->info("Failed payment:  of order id: ".$paymentInsertData['ORDERNO']);
		}catch (PDOException $e) {
             $this->_logger->info('Error during insert into ReceiptRecord : ' . $e->getMessage());	
		}

	}
	
	protected function addToSalesRecordProduct($serialCodeArray)
	{
		$tableFields = implode(",",array_keys($serialCodeArray));
		//$tableValues = "'" . implode ( "', '", $serialCodeArray ) . "'"; 
		
		$tableValues = '"' . implode ( '", "', $serialCodeArray ) . '"'; 
		
		$query = "SET QUOTED_IDENTIFIER OFF; INSERT INTO dbo.SalesRecordProduct ($tableFields) VALUES($tableValues); SET QUOTED_IDENTIFIER ON;";
		
		$this->_logger->info('SalesRecordProduct query: '.$query);
		
		try{
		     $result = $this->_mssqlConnection->query($query);
			 if(isset($result))
				 $this->_logger->info('Added serialcode: '.$serialCodeArray['ITEMNO']." of order id: ".$serialCodeArray['ORDERNO']);
			 else
				 $this->_logger->info('Failed serialcode: '.$serialCodeArray['ITEMNO']." of order id: ".$serialCodeArray['ORDERNO']);
		}catch (PDOException $e) {
             $this->_logger->info('Error during insert into SalesRecordProduct : ' . $e->getMessage());	
		}

	}
	
	protected function addToSalesRecordDetail($orderItemData)
	{
		$tableFields = implode(",",array_keys($orderItemData));
		
		//$tableValues = "'" . implode ( "', '", $orderItemData ) . "'";

        $tableValues = '"' . implode ( '", "', $orderItemData ) . '"';		
		
		$query = "SET QUOTED_IDENTIFIER OFF; INSERT INTO dbo.SalesRecordDetail ($tableFields) VALUES($tableValues); SET QUOTED_IDENTIFIER ON;";
		
		try{
		     $result = $this->_mssqlConnection->query($query);
			 if(isset($result))
				 $this->_logger->info('Added item: '.$orderItemData['ITEMNO']." of order id: ".$orderItemData['ORDERNO']);
			 else
				 $this->_logger->info('Failed item: '.$orderItemData['ITEMNO']." of order id: ".$orderItemData['ORDERNO']);
		}catch (PDOException $e) {
             $this->_logger->info('Error during insert into SalesRecordDetail : ' . $e->getMessage());	
		}

	}
	
	public function getProcessingOrder()
    {
		$selectInfoQuery = "SELECT * FROM sage_integration where sales_header = '0' AND sales_details = '0' AND sales_serialcode = '0' AND payment_receipt = '1'";
		
		$selctQry = $this->_mysqlConnection->prepare($selectInfoQuery);
		
		$selctQry->execute();
		$returnData = array();
		while($row = $stmt->selctQry()){
			$returnData[] = $row['orderid'];
		}
		return $returnData;
    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
   
} 