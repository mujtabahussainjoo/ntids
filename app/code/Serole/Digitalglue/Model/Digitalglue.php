<?php
namespace Serole\Digitalglue\Model;

class Digitalglue extends \Magento\Framework\Model\AbstractModel
{
	
	private $_apiUrl;
	
	private $_apiUserName; 
	
	private $_apiPassword;
	
	protected $_helper;
	
	protected $_objectManager;
	
	protected $_connection;
	
	protected $_logger;
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\Digitalglue\Model\ResourceModel\Digitalglue');
	  
	    $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
		$this->_helper = $this->_objectManager->create('Serole\Digitalglue\Helper\Data');
		
		$this->_apiUrl = $this->_helper->getAPIUrl();
	    $this->_apiUserName = $this->_helper->getAPIUsername();
	    $this->_apiPassword = $this->_helper->getAPIPassword();
		
		$resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->_connection = $resource->getConnection();
		
		$this->createLog('digital_glue.log');
    }
	
	
	/* Input $orderData is array
	   $orderData['orderId'] = 'oid';
	   $orderData['OrderItems'] = Array
                (
                    [0] => array
                        (
                            [Quantity] => 1
                            [Amount] => 50
                            [SKU] => JBHSCVARI002
                            [ReferenceNumber] => Test59
                        )

                    [1] => array
                        (
                            [Quantity] => 1
                            [Amount] => 50
                            [SKU] => JBHSCVARI002
                            [ReferenceNumber] => Test59
                        )

                )
	*/
	public function getDigitalGlueData($orderData)
	{
		$this->_logger->info('Request data');
		$this->_logger->info($orderData);
		$requestData = array();
		$requestData['OrderReferenceNumber'] = $orderData['orderId'];
		
		//Recipient Details
		$requestData['Recipient']['Email'] = "admin@neatideas.com";
		$requestData['Recipient']['FirstName'] = "";
		$requestData['Recipient']['LastName'] = "";
		$requestData['Recipient']['MobileNumber'] = "";
		$requestData['Recipient']['PhoneNumber'] = "";
		
		//PersonalisationInfo Details
		$requestData['DigitalStoreCardOrder']['PersonalisationInfo']['To'] = "";
		$requestData['DigitalStoreCardOrder']['PersonalisationInfo']['Message'] = "";
		$requestData['DigitalStoreCardOrder']['PersonalisationInfo']['From'] = "";
		
		//Order Items Details
		$requestData['DigitalStoreCardOrder']['OrderItems'] = $orderData['OrderItems'];
		
		$this->_logger->info($orderData);
		
		$jsonRequest = json_encode($requestData);
		
		$this->_logger->info($jsonRequest);
		
		$result = $this->callAPI("POST", $jsonRequest);
		
		$this->_logger->info("----------------Result---------------");
		$this->_logger->info($result);
		
		if($result['status'] == "success")
		{
			$resultData = array();
			$resultData['status'] = "success";
			$i=0;
			foreach($result['OrderItems'] as $item)
			{
				$resultData['OrderItems'][$i]['referencenumber'] = $result['OrderReferenceNumber'];
				$resultData['OrderItems'][$i]['sku'] = $item['SKU'];
				$resultData['OrderItems'][$i]['magento_sku'] = $item['ReferenceNumber'];
				$resultData['OrderItems'][$i]['quantity'] = $item['Quantity'];
				$resultData['OrderItems'][$i]['statuscode'] = $item['StatusCode'];
				$resultData['OrderItems'][$i]['statusmessage'] = $item['StatusMessage'];
				$resultData['OrderItems'][$i]['receiptnumber'] = $item['ReceiptNumber'];
				$resultData['OrderItems'][$i]['amount'] = $item['Amount'];
				$resultData['OrderItems'][$i]['redemptionurl'] = $item['RedemptionUrl'];
				$resultData['OrderItems'][$i]['costperunit'] = $item['CostPerUnit'];
				$resultData['OrderItems'][$i]['rrpperunit'] = $item['RRPPerUnit'];
				$resultData['OrderItems'][$i]['includesgst'] = $item['IncludesGst'];
				if(isset($item['RedemptionExpiryDate']))
				  $resultData['OrderItems'][$i]['expirydate'] =  $item['RedemptionExpiryDate'];
			    else
				  $resultData['OrderItems'][$i]['expirydate'] = '';
				
				$Quantity = $item['Quantity'];
				$StatusCode = $item['StatusCode'];
				$SKU = $item['SKU'];
				$ReferenceNumber = $result['OrderReferenceNumber'];
				$magento_sku = $item['ReferenceNumber'];
				$StatusMessage = $item['StatusMessage'];
				$ReceiptNumber = $item['ReceiptNumber'];
				$Amount = $item['Amount'];
				$RedemptionUrl = $item['RedemptionUrl'];
				$CostPerUnit = $item['CostPerUnit'];
				$RRPPerUnit = $item['RRPPerUnit'];
				$IncludesGst = $item['IncludesGst'];
				if(isset($item['RedemptionExpiryDate']))
				  $RedemptionExpiryDate = $item['RedemptionExpiryDate'];
			    else
				  $RedemptionExpiryDate = '';
			  
		        $query = "INSERT INTO digitalglue (`referencenumber`,`sku`,`magento_sku`,`quantity`,`statuscode`,`statusmessage`,`receiptnumber`,`amount`,`redemptionurl`,`expirydate`,`costperunit`,`rrpperunit`,`includesgst`) VALUES('$ReferenceNumber','$SKU','$magento_sku','$Quantity','$StatusCode','$StatusMessage','$ReceiptNumber','$Amount','$RedemptionUrl','$RedemptionExpiryDate','$CostPerUnit','$RRPPerUnit','$IncludesGst')";
				
				$this->_connection->query($query);
				
				/*$serialTableQuery = "INSERT INTO order_item_serialcode (`OrderID`,`sku`,`ExpireDate`,`URL`,`status`) 
				                     VALUES('$ReferenceNumber','$SKU','$RedemptionExpiryDate','$RedemptionUrl','1')";
									 
				$this->_connection->query($serialTableQuery); 
				*/
				$i++;
			}
			$this->_logger->info("--------Return Result------------");
			$this->_logger->info($resultData);
			return $resultData;
		}
		else
			return $result;
		
	}
	
	protected function callAPI($method, $data){
		$response = array();
		try{
			   $curl = curl_init();
			   $url = $this->_apiUrl;
			   $apiUser = $this->_apiUserName;
			   $apiPassword = $this->_apiPassword;
			   switch ($method){
				  case "POST":
					 curl_setopt($curl, CURLOPT_POST, 1);
					 if ($data)
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					 break;
				  case "PUT":
					 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
					 if ($data)
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
					 break;
				  default:
					 if ($data)
						$url = sprintf("%s?%s", $url, http_build_query($data));
			   }

			   // OPTIONS:
			   curl_setopt($curl, CURLOPT_URL, $url);
			   $headers = array(
				'Content-Type:application/json',
				'Authorization: Basic '. base64_encode("$apiUser:$apiPassword") 
			   );
			   curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

			   // EXECUTE:
			   $curlResult = curl_exec($curl);
			   
			   if(!$curlResult)
			   {
				   $response['status'] = "error";
				   
				   if (curl_errno($curl)) {
						$error_msg = curl_error($curl);
						$response['errorMessage'] = "Curl Error:".$error_msg;
					}
				   else
			         $response['errorMessage'] = "Api connection error";
			   }
			   curl_close($curl);
			   
			   $result = json_decode($curlResult, true);
			   
			   if(empty($result) || $result =='')
			   {
				   $response['status'] = "error";
			       $response['errorMessage'] = "Empty Response from Api";
			   }
			   elseif(!empty($result['Errors']))
			   {
				   $response['status'] = "error";
			       $response['errorMessage'] = implode(",",$result['Errors']);
			   }
			   elseif(empty($result['Response']['DigitalStoreCardOrder']['OrderItems']))
			   {
				   $response['status'] = "error";
			       $response['errorMessage'] = "Empty Items Response from Api";
			   }
			   else{
				   $response['status'] = "success";
				   $this->_logger->info("--------curl Result Start------------");
			       $this->_logger->info($result);
				   $this->_logger->info("--------curl Result End------------");
			       $response['OrderReferenceNumber'] = $result['Response']['OrderReferenceNumber'];
			       $response['OrderItems'] = $result['Response']['DigitalStoreCardOrder']['OrderItems'];
			   }
		}
		catch(Exception $e)
		{
			$response['status'] = "error";
			$response['errorMessage'] = $e->getMessage();
		}
		return $response;
	}
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}
?>