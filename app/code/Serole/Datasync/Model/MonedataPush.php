<?php

   namespace Serole\Datasync\Model;

   class MonedataPush extends \Magento\Framework\Model\AbstractModel{

       protected $sourceDBconn;

       protected $destinationDBconn;

       protected $tablesValidationError;

       protected $sourcesTablesMissed;

       protected $listSourceTableMissed;

       protected $sourcedbConnectionError;

       protected $destinationdbConnectionError;

       protected $customerData;

       protected $orderData;

       protected $storedData;
	   
	   protected $serialize;


	   protected $indexerFactory;

       public function __construct(\Magento\Framework\Model\Context $context,
                                   \Magento\Framework\Registry $registry,
								   \Magento\Framework\Serialize\Serializer\Json $serialize,
                                   \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
                                   \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
                                   \Magento\Indexer\Model\Indexer $indexerFactory,
                                   array $data = [])
       {
           parent::__construct($context, $registry, $resource, $resourceCollection, $data);
           $this->sourceDBconn;
		   $this->serialize = $serialize;
           $this->indexerFactory = $indexerFactory;
           $this->destinationDBconn;
           $this->tablesValidationError = 0;
           $this->listSourceTableMissed = array();
           $this->sourcedbConnectionError;
           $this->destinationdbConnectionError;
           $this->sourcesTablesMissed = 0;
           $this->customerData = array();
           $this->orderData = array();
           $this->storeData = array();
           $this->M2storeData = array();
           $this->sourceDBConnectInit();
           $this->destinationDBConnectInit();
           $this->getStoresData();
           $this->getM2StoresData();
       }

       public function createLog($message){
		   $date = date('Y-m-d');
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$date.'_datasync-error-process.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           $logger->info($message);
       }

       public function sqlQueries($message){
           $date = date('Y-m-d');
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$date.'_sql-get-queries.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           $logger->info($message);
       }

       public function dataSaveLog($message){
           $date = date('Y-m-d');
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$date.'_sql-dataSaveLog.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           $logger->info($message);
       }

       public function showData($message){
           $date = date('Y-m-d');
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$date.'_data-show.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           $logger->info($message);
       }

       public function customerReindex(){
           $customerReindex = $this->indexerFactory;
           $customerReindex->load('customer_grid');
           $customerReindex->reindexAll();
       }

       private function sourceDBConnectInit(){
               $dbhost = "magento-live-new.cruxv62u21s2.ap-southeast-2.rds.amazonaws.com";
               $dbuser = "neatideas";
               $dbpass = "l3t5g0l1v3";
               $db     = "ni_magento";
               $this->sourceDBconn = new \mysqli($dbhost, $dbuser, $dbpass,$db);
               if ($this->sourceDBconn->connect_error) {
                   $this->sourcedbConnectionError = $this->sourceDBconn->connect_error;
                   return FALSE;
               }else {
                   return TRUE;
               }
       }

       private function destinationDBConnectInit(){
           $dbhost = "m2-live-cluster.cluster-cruxv62u21s2.ap-southeast-2.rds.amazonaws.com";
           $dbuser = "neatideas";
           $dbpass = "l3t5g0l1v4321";
           $db     = "ni_magento2_prod"; //m2_structure_only
           $this->destinationDBconn = new \mysqli($dbhost, $dbuser, $dbpass,$db);
           if ($this->destinationDBconn->connect_error) {
               $this->destinationdbConnectionError = $this->destinationDBconn->connect_error;
               return FALSE;
           }else {
               return TRUE;
           }
       }

       private function sourceDBConnectClose(){
           $this->sourceDBconn->close();
       }
       private function destinationDbClose(){
           $this->destinationDBconn->close();
       }

       public function arrayKeyFind($value,$multiArray,$index){
           foreach($multiArray as $arrayItemtkey => $arrayItem) {
               if($arrayItem[$index] == $value){
                   return $arrayItemtkey;
               }
           }
           return false;
       }


      public function syncData(){
           try {
                $dataSycdate = date('Y-m-d H:i:s');
                $to      = 'dhananjay.kumar@serole.com';
                $subject = 'Data Sync Process Start -'.$dataSycdate;
                $message = 'Data Sync Process Start -'.$dataSycdate;
                $headers = 'From: iamramesh.a@gmail.com' . "\r\n" .
                           'Reply-To: dhananjay.kumar@serole.com' . "\r\n" .
                           'X-Mailer: PHP/' . phpversion();

                mail($to, $subject, $message, $headers);

               $date = date('Y-m-d',strtotime("-1 days"));
               $this->createLog('----------------' . $date . '----------------');
               if ($this->sourcedbConnectionError || $this->destinationdbConnectionError) {
                   $this->createLog($this->sourcedbConnectionError);
                   $this->createLog($this->destinationdbConnectionError);
                  return "Problem with database connection.";
               }
               $this->customerDataSync($date= TRUE, $email = FALSE);
               $this->saveCustomerData($date = TRUE, $id = FALSE);
               $this->saveOrderData();
               $this->customerReindex();
			   
           }catch (\Exception $e){
               $this->createLog($e->getMessage());
           }
       }

       public function customerDataSync($date,$email,$storeId=false,$customerId=false){
		   $data = date('Y-m-d',strtotime("-1 days"));

		   $fromDate = $data.' 00:00:00';
	       $toDate = $data.' 23:59:59';

           //$fromDate = '2019-07-09 00:00:00';
           //$toDate = '2019-07-09 23:59:59';
           
           try {
               if(empty($this->storeData)){
                   $this->getStoresData();
               }
               if(empty($this->M2storeData)){
                   $this->getM2StoresData();
               }
               $customerEntitySql = '';
               if($date == TRUE) {
                   $customerEntitySql = "select * from mage_customer_entity where created_at >= '$fromDate' and created_at <= '$toDate' "; //entity_id = 1021327 "; //created_at >= '$fromDate' and created_at <= '$toDate' 
               }elseif($email && $storeId && $date == FALSE){
                   $websiteId = $this->storeData[$storeId];
                   $customerEntitySql = "select * from mage_customer_entity where entity_id = $customerId ";
               }
               if($customerEntitySql) {
                   $customerEntityResults = $this->sourceDBconn->query($customerEntitySql);
                   if($this->sourceDBconn->error){
                        $this->createLog("----------------------customerDataSync------------------");
                        $this->createLog($this->sourceDBconn->error);
                        $this->createLog($customerEntitySql);
                   }else {
                       while ($customerEntityRow = mysqli_fetch_array($customerEntityResults)) {
                           $this->customerData[$customerEntityRow['entity_id']]['customer_entity'] = $customerEntityRow;
                           $this->getCustomerData($customerEntityRow['entity_id']);
                       }
                   }
               }
           }catch (\Exception $e){
               $this->createLog($e->getMessage());
           }
       }

       public function getStoresData(){
           $storeSql = "select * from mage_core_store";
           $results = $this->sourceDBconn->query($storeSql);
           while ($storeRecord = mysqli_fetch_array($results)) {
               $this->storeData[$storeRecord['store_id']] = $storeRecord['website_id'];
           }
       }

       public function getM2StoresData(){
           $storeSql = "select * from store";
           $results = $this->destinationDBconn->query($storeSql);
           while ($storeRecord = mysqli_fetch_array($results)) {
               $this->M2storeData[$storeRecord['store_id']] = $storeRecord['website_id'];
           }
       }

       public function loadCustomerM1($customerId){
           $customerExistSql = "select * from mage_customer_entity where entity_id = $customerId ";
           $customerExistSqlResult = $this->sourceDBconn->query($customerExistSql);
           $customerResult = mysqli_fetch_array($customerExistSqlResult,MYSQLI_ASSOC);
           //echo "<pre>"; print_r($customerResult);
           if($customerResult){
              return $customerResult['email'];
           }else{
               return false;
           }
       }

       public function checkCustomerExistM2($email,$customerStoreId,$m1Id,$customerId=false ){
           if(empty($this->storeData)){
               $this->getStoresData();
           }
           $email = addslashes($email);
           $websiteId = $this->storeData[$customerStoreId];

           if($customerId) {
               $customerM1 = $this->loadCustomerM1($customerId);
               if($customerM1){
                   $email = $customerM1;
               }
           }

           $checkEmailSql = "select * from customer_entity where email = '$email' and website_id = $websiteId";

           $this->dataSaveLog("------------------Is customer Exists-----------------------");
           $this->dataSaveLog($checkEmailSql);
           $results = $this->destinationDBconn->query($checkEmailSql);
           $customerResult = mysqli_fetch_array($results,MYSQLI_ASSOC);
           if(empty($customerResult)){
               return FALSE;
           }else{
               return $customerResult['entity_id'];
           }
       }

       public function saveCustomerData($date, $id){
           try {
               $this->customerDataSync($date = TRUE, $email = FALSE, $storeId = FALSE);
               $customerObj = array();
               if (!empty($this->customerData)) {
                   if ($id) {
                       $customerObj[$id] = $this->customerData[$id];
                   } else {
                       $customerObj = $this->customerData;
                   }

                   foreach ($customerObj as $customerEnityIdKey => $customerData) {
                       $email = $customerData['customer_entity']['email'];
                       $customerStoreId = $customerData['customer_entity']['store_id'];
                       if($id == FALSE) {
                           $emailStatus = $this->checkCustomerExistM2($email, $customerStoreId,$customerEnityIdKey);
                           if ($emailStatus != '') {
                               $this->createLog("email Already Exist -->" . $email);
                               continue;
                           }
                       }
                       $customerEntityId = '';
                       foreach ($customerData as $customerDataType => $customerDataItem) {
                           //$customerEntityId = '';
                           $this->dataSaveLog('------------------------customer-entity--------------------------------------');
                           $this->dataSaveLog($customerDataItem);
                           $insertId = '';
                           if ($customerDataType == 'customer_entity') {
                               $customerWebsiteId = $customerData['customer_entity']['website_id'];
                               $customerEmail = addslashes($customerData['customer_entity']['email']);
                               $customerGroupId = $customerData['customer_entity']['group_id'];
                               $createdAt = $customerData['customer_entity']['created_at'];
                               $updatedAt = $customerData['customer_entity']['updated_at'];
                               $isActive = $customerData['customer_entity']['is_active'];
                               $disableAutoGroupChange = $customerData['customer_entity']['disable_auto_group_change'];
							   if(isset($customerData['customer_entity']['first_name']))
									$firstName = addslashes($customerData['customer_entity']['first_name']);
						       else
								   $firstName='';
						       if(isset($customerData['customer_entity']['last_name']))
									$lastName = addslashes($customerData['customer_entity']['last_name']);
								else
									$lastName = '';
						       if(isset($customerData['customer_entity']['password_hash']))
									$password = $customerData['customer_entity']['password_hash'].":0";
						       else
								   $password ='';
                               $suspended = $customerData['customer_entity']['is_suspended'];

                               $gender = ' ';
                               if (isset($customerData['customer_entity']['gender'])) {
                                   $gender = $customerData['customer_entity']['gender'];
                               }

                               $suffix = ' ';
                               if (isset($customerData['customer_entity']['suffix'])) {
                                   $suffix = $customerData['customer_entity']['suffix'];
                               }

                               $middleName = ' ';
                               if (isset($customerData['customer_entity']['middle_name'])) {
                                   $middleName = addslashes($customerData['customer_entity']['middle_name']);
                               }

                               $prefix = ' ';
                               if (isset($customerData['customer_entity']['prefix'])) {
                                   $prefix = $customerData['customer_entity']['prefix'];
                               }

                               $createdIn = ' ';
                               if (isset($customerData['customer_entity']['created_in'])) {
                                   $createdIn = $customerData['customer_entity']['created_in'];
                               }

                               $dob = ' ';
                               if (isset($customerData['customer_entity']['dob'])) {
                                   $dob = $customerData['customer_entity']['dob'];
                               }

                               $rpToken = ' ';
                               if (isset($customerData['customer_entity']['rp_token'])) {
                                   $rpToken = $customerData['customer_entity']['rp_token'];
                               }

                               $rpTokenTime = ' ';
                               if (isset($customerData['customer_entity']['rp_token_datetime'])) {
                                   $rpTokenTime = $customerData['customer_entity']['rp_token_datetime'];
                               }

                               $billingAddress = '';
                               if (isset($customerData['customer_entity']['billing_address'])) {
                                   $billingAddress = addslashes($customerData['customer_entity']['billing_address']);
                               }

                               $shippingAddress = ' ';
                               if (isset($customerData['customer_entity']['shipping_address'])) {
                                   $shippingAddress = addslashes($customerData['customer_entity']['shipping_address']);
                               }

                               $customerEntitySaveSql = "insert into customer_entity (
                                                                                   website_id,
                                                                                   email,
                                                                                   group_id,
                                                                                   increment_id,
                                                                                   store_id,
                                                                                   created_at,
                                                                                   updated_at,
                                                                                   is_active,
                                                                                   disable_auto_group_change,
                                                                                   created_in,
                                                                                   prefix,
                                                                                   firstname,
                                                                                   middlename,
                                                                                   lastname,
                                                                                   suffix,
                                                                                   dob,
                                                                                   password_hash,
                                                                                   rp_token,
                                                                                   rp_token_created_at,
                                                                                   default_billing,
                                                                                   default_shipping,
                                                                                   taxvat,
                                                                                   confirmation,
                                                                                   gender,
                                                                                   failures_num,
                                                                                   first_failure,
                                                                                   lock_expires,
                                                                                   is_suspended) 
                                                     values ( '$customerWebsiteId',
                                                              '$customerEmail',
                                                              '$customerGroupId',
                                                              NULL,
                                                              '$customerStoreId',
                                                              '$createdAt',
                                                              '$updatedAt',
                                                              '$isActive',
                                                              '$disableAutoGroupChange',
                                                              '$createdIn',
                                                              '$prefix',
                                                              '$firstName',
                                                              '$middleName',
                                                              '$lastName',
                                                              '$suffix',
                                                              '$dob',
                                                              '$password',
                                                              '$rpToken',
                                                              '$rpTokenTime',
                                                              '$billingAddress',
                                                              '$shippingAddress',
                                                              NULL,
                                                              NULL,
                                                              '$gender',
                                                              NULL,
                                                              NULL,
                                                              NULL,
                                                              '$suspended')";

                               $this->destinationDBconn->query($customerEntitySaveSql);
                               $this->sqlQueries("-----------------------Start----customer-entity------".$customerEmail."-------------------------------");
                               $this->sqlQueries($customerEntitySaveSql);

                               if ($this->destinationDBconn->error) {
                                   $this->createLog("-----------------------customer-entity--------------------------------");
                                   $this->createLog($customerEntitySaveSql);
                                   $this->createLog($this->destinationDBconn->error);
                                   continue;
                               } else {
                                   $customerEntityId = $this->destinationDBconn->insert_id;
                                   $this->dataSaveLog($customerEntityId);
                                   $this->customerData[$customerEnityIdKey]['customer_entity']['m2_id'] = $customerEntityId;
                               }
                           }

                           if ($customerEntityId) {
                               $this->sqlQueries("customer Id".$customerEntityId);
                               $this->sqlQueries($customerDataType);
                               if ($customerDataType == 'customer_int') {
                                   $this->sqlQueries($customerDataType. " In to ");
                                   if (!empty($customerData['customer_int'])) {
                                       $customerIntTbleSql = "INSERT INTO customer_entity_int (attribute_id,entity_id,value) values ";
                                       $this->dataSaveLog("---------------------customer_int--------------------------------");
                                       foreach ($customerData['customer_int'] as $customerIntKey => $customerIntData) {
                                           $this->dataSaveLog($customerIntData);
                                           $attributeId = $customerIntData['attribute_id'];
                                           if ($customerIntData['attribute_id'] == 232) {
                                               $attributeId = 273;
                                           }
                                           $customerIntTbleSql .= "($attributeId,$customerEntityId,'$customerIntData[value]'),";
                                       }
                                       $customerIntsqlTrim = rtrim($customerIntTbleSql, ',');

                                       $this->destinationDBconn->query($customerIntsqlTrim);
                                       $this->sqlQueries("-----------------------customer_int--------------------------------");
                                       $this->sqlQueries($customerIntsqlTrim);

                                       if ($this->destinationDBconn->error) {
                                           $this->createLog("-----------------------customer_int--------------------------------");
                                           $this->createLog($customerIntsqlTrim);
                                           $this->createLog($this->destinationDBconn->error);
                                           continue;
                                       }
                                   }

                               } elseif ($customerDataType == 'customer_varchar') {
                                   $this->sqlQueries($customerDataType. " In to ");
                                   if (!empty($customerData['customer_varchar'])) {
                                       $runCustomerVarcharSql = 0;
                                       $customerVarcharTbleSql = "INSERT INTO customer_entity_varchar (attribute_id,entity_id,value) values ";

                                       $this->dataSaveLog("-----------------------customer_varchar--------------------------------");
                                       foreach ($customerData['customer_varchar'] as $customerVarcharKey => $customerVarcharData) {
                                           $this->dataSaveLog($customerVarcharData);
                                           $attributeId = $customerVarcharData['attribute_id'];
                                           if ($customerVarcharData['attribute_id'] == 213) {
                                               $attributeId = 169;
                                           } else if ($customerVarcharData['attribute_id'] == 254) {
                                               $attributeId = 252;
                                           } else if ($customerVarcharData['attribute_id'] == 206) {
                                               $attributeId = 275;
                                           } else if ($customerVarcharData['attribute_id'] == 265) {
                                               $attributeId = 276;
                                           }

                                          if(in_array($attributeId, array(169,252,275,276))) {
                                              $runCustomerVarcharSql = 1;
                                              $customerVarcharTbleSql .= "($attributeId,$customerEntityId,'$customerVarcharData[value]'),";
                                          }
                                       }
                                       if($runCustomerVarcharSql == 1) {
                                           $customerVarcharsqlTrim = rtrim($customerVarcharTbleSql, ',');

                                           $this->destinationDBconn->query($customerVarcharsqlTrim);
                                           $this->sqlQueries("-----------------------customer-varchar--------------------------------");
                                           $this->sqlQueries($customerVarcharsqlTrim);

                                           if ($this->destinationDBconn->error) {
                                               $this->createLog("-----------------------customer-varchar--------------------------------");
                                               $this->createLog($customerVarcharsqlTrim);
                                               $this->createLog($this->destinationDBconn->error);
                                               continue;
                                           }
                                       }
                                   }

                               } elseif ($customerDataType == 'customer_address_entity') {
                                   $this->sqlQueries($customerDataType. " In to ");
                                   if (!empty($customerData['customer_address_entity'])) {
                                       $customerAddressSql = "insert into customer_address_entity (parent_id,created_at,updated_at,is_active,city,company,
                                                    country_id,fax,firstname,lastname,middlename,postcode,prefix,region,region_id,street,suffix,telephone,
                                                    vat_id,vat_is_valid,vat_request_date,vat_request_id,vat_request_success) values";
                                       $this->dataSaveLog('---------------customer_address_entity-------------------');
                                       foreach ($customerData['customer_address_entity'] as $addressKey => $addressItem) {
                                           $this->dataSaveLog($addressItem);
                                           $company = '';
                                           if (isset($addressItem['company'])) {
                                               $company = addslashes($addressItem['company']);
                                           }
                                           $city = '';
                                           if (isset($addressItem['city'])) {
                                               $city = addslashes($addressItem['city']);
                                           }
                                           $countryId = '';
                                           if (isset($addressItem['country_id'])) {
                                               $countryId = $addressItem['country_id'];
                                           }
                                           $fax = '';
                                           if (isset($addressItem['fax'])) {
                                               $fax = $addressItem['fax'];
                                           }
                                           $firstName = '';
                                           if (isset($addressItem['firstname'])) {
                                               $firstName = addslashes($addressItem['firstname']);
                                           }
                                           $lastName = '';
                                           if (isset($addressItem['lastname'])) {
                                               //$this->dataSaveLog("----lastname");
                                               $lastname = addslashes($addressItem['lastname']);
                                               //$this->dataSaveLog($lastName);
                                           }
                                           $middleName = '';
                                           if (isset($addressItem['middlename'])) {
                                               $middleName = addslashes($addressItem['middlename']);
                                           }
                                           $postcode = '';
                                           if (isset($addressItem['postcode'])) {
                                               $postcode = $addressItem['postcode'];
                                           }
                                           $prefix = '';
                                           if (isset($addressItem['prefix'])) {
                                               $prefix = $addressItem['prefix'];
                                           }
                                           $region = '';
                                           if (isset($addressItem['region'])) {
                                               $region = addslashes($addressItem['region']);
                                           }
                                           $regionId = '';
                                           if (isset($addressItem['region_id'])) {
                                               $regionId = $addressItem['region_id'];
                                           }
                                           $street = '';
                                           if (isset($addressItem['street'])) {
                                               $street = addslashes($addressItem['street']);
                                           }
                                           $suffix = '';
                                           if (isset($addressItem['suffix'])) {
                                               $suffix = $addressItem['suffix'];
                                           }
                                           $telephone = '';
                                           if (isset($addressItem['telephone'])) {
                                               $telephone = $addressItem['telephone'];
                                           }
                                           $vat_id = '';
                                           if (isset($addressItem['vat_id'])) {
                                               $vat_id = $addressItem['vat_id'];
                                           }
                                           $vat_is_valid = '';
                                           if (isset($addressItem['vat_is_valid'])) {
                                               $vat_is_valid = $addressItem['vat_is_valid'];
                                           }
                                           $vat_request_date = '';
                                           if (isset($addressItem['vat_request_date'])) {
                                               $vat_request_date = $addressItem['vat_request_date'];
                                           }
                                           $vat_request_id = '';
                                           if (isset($addressItem['vat_request_id'])) {
                                               $vat_request_id = $addressItem['vat_request_id'];
                                           }
                                           $vat_request_success = '';
                                           if (isset($addressItem['vat_request_success'])) {
                                               $vat_request_success = $addressItem['vat_request_success'];
                                           }
                                               //$this->dataSaveLog($lastName);
                                               //$this->dataSaveLog($addressItem['lastname']);
                                               $customerAddressSql .= "('$customerEntityId','$addressItem[created_at]','$addressItem[updated_at]','$addressItem[is_active]','$city',
                                                                 '$company','$countryId','$fax','$firstName','$lastName','$middleName','$postcode','$prefix','$region','$regionId',
                                                                 '$street','$suffix','$telephone','$vat_id','$vat_is_valid','$vat_request_date','$vat_request_id','$vat_request_success'),";
                                       }

                                       $customerAddressqlTrim = rtrim($customerAddressSql, ',');

                                       $this->destinationDBconn->query($customerAddressqlTrim);

                                       $this->sqlQueries("-----------------------customer-entity-address--------------------------------");
                                       $this->sqlQueries($customerAddressqlTrim);


                                       $addressInsertId = $this->destinationDBconn->insert_id;

                                       if($addressInsertId){
                                           $updateCustomerEnitityBillingRecord = "update customer_entity set default_billing = $addressInsertId where entity_id = $customerEntityId and default_billing = $addressKey";
                                           $updateCustomerEnitityShippingRecord = "update customer_entity set default_shipping = $addressInsertId where entity_id = $customerEntityId and default_shipping = $addressKey";
                                           $this->destinationDBconn->query($updateCustomerEnitityBillingRecord);
                                           $this->destinationDBconn->query($updateCustomerEnitityShippingRecord);
                                       }

                                       if ($this->destinationDBconn->error) {
                                           $this->createLog("-----------------------customer-entity-address--------------------------------");
                                           $this->createLog($customerAddressqlTrim);
                                           $this->createLog($this->destinationDBconn->error);
                                           continue;
                                       }
                                   }
                               }
                           }
                       }
                   }
               }
           }catch (\Exception $e){
               $this->createLog("Exception From customer save method");
               $this->createLog($e->getMessage());
           }
       }


       public function getCustomerData($customerEntityId){
           $customerChildDataSql = " select 
                                              customer_entity.entity_id as customerEntityId,
                                              
                                              customer_int.value_id as customerIntValueId, 
                                              customer_int.entity_type_id as customerIntEntityTypeId,
                                              customer_int.attribute_id as customerIntAttributeId,
                                              customer_int.value as customerIntValue,
                                            
                                              customer_varchar.value_id as customerVarcharValueId, 
                                              customer_varchar.entity_type_id as customerVarcharEntityTypeId,
                                              customer_varchar.attribute_id as customerVarcharAttributeId, 
                                              customer_varchar.value as customerVarcharValue,
                                              
                                              customer_datetime.value_id as customerDateTimeValueId,
                                              customer_datetime.entity_type_id as customerDateTimeEntityTypeId,
                                              customer_datetime.attribute_id as customerDateTimeAttributeId,
                                              customer_datetime.value as customerDateTimeValue
                                            
                                              FROM mage_customer_entity as customer_entity
                                              lEFT JOIN mage_customer_entity_int as customer_int ON customer_int.entity_id = customer_entity.entity_id
                                              LEFT JOIN mage_customer_entity_varchar as customer_varchar ON customer_varchar.entity_id = customer_entity.entity_id
                                              LEFT JOIN mage_customer_entity_datetime as customer_datetime ON customer_datetime.entity_id = customer_entity.entity_id
                                              where customer_entity.entity_id = $customerEntityId
                                              ";
           //echo $customerChildDataSql;
           /*
            FROM mage_customer_entity_int as customer_int
                                              LEFT JOIN mage_customer_entity_varchar as customer_varchar ON customer_varchar.entity_id = customer_int.entity_id
                                              LEFT JOIN mage_customer_entity_datetime as customer_datetime ON customer_varchar.entity_id = customer_datetime.entity_id
                                              where customer_int.entity_id = $customerEntityId

            */
           $customerChildDataResults = $this->sourceDBconn->query($customerChildDataSql);

           while ($customerChildDataRow = mysqli_fetch_array($customerChildDataResults)){

               if(!isset($this->customerData[$customerEntityId]['customer_int'][$customerChildDataRow['customerIntValueId']])){
                   if($customerChildDataRow['customerIntAttributeId'] == 13){
                       $this->customerData[$customerEntityId]['customer_entity']['billing_address'] = $customerChildDataRow['customerIntValue'];
                   }elseif ($customerChildDataRow['customerIntAttributeId'] == 14){
                       $this->customerData[$customerEntityId]['customer_entity']['shipping_address'] = $customerChildDataRow['customerIntValue'];
                   }elseif ($customerChildDataRow['customerIntAttributeId'] == 18){
                       $this->customerData[$customerEntityId]['customer_entity']['gender'] = $customerChildDataRow['customerIntValue'];
                   }else {
                       $this->customerData[$customerEntityId]['customer_int'][$customerChildDataRow['customerIntValueId']]['value_id'] = $customerChildDataRow['customerIntValueId'];
                       $this->customerData[$customerEntityId]['customer_int'][$customerChildDataRow['customerIntValueId']]['entity_type_id'] = $customerChildDataRow['customerIntEntityTypeId'];
                       $this->customerData[$customerEntityId]['customer_int'][$customerChildDataRow['customerIntValueId']]['attribute_id'] = $customerChildDataRow['customerIntAttributeId'];
                       $this->customerData[$customerEntityId]['customer_int'][$customerChildDataRow['customerIntValueId']]['entity_id'] = $customerEntityId;
                       $this->customerData[$customerEntityId]['customer_int'][$customerChildDataRow['customerIntValueId']]['value'] = $customerChildDataRow['customerIntValue'];
                   }
               }

               if(!isset($this->customerData[$customerEntityId]['customer_varchar'][$customerChildDataRow['customerVarcharValueId']]) && $customerChildDataRow['customerVarcharValueId'] != ''){
                   if($customerChildDataRow['customerVarcharAttributeId'] == 3){
                       $this->customerData[$customerEntityId]['customer_entity']['created_in'] = $customerChildDataRow['customerVarcharValue'];
                   }elseif ($customerChildDataRow['customerVarcharAttributeId'] == 4){
                       $this->customerData[$customerEntityId]['customer_entity']['prefix'] = $customerChildDataRow['customerVarcharValue'];
                   }elseif ($customerChildDataRow['customerVarcharAttributeId'] == 5){
                       $this->customerData[$customerEntityId]['customer_entity']['first_name'] = $customerChildDataRow['customerVarcharValue'];
                   }elseif ($customerChildDataRow['customerVarcharAttributeId'] == 6){
                       $this->customerData[$customerEntityId]['customer_entity']['middle_name'] = $customerChildDataRow['customerVarcharValue'];
                   }elseif ($customerChildDataRow['customerVarcharAttributeId'] == 7){
                       $this->customerData[$customerEntityId]['customer_entity']['last_name'] = $customerChildDataRow['customerVarcharValue'];
                   }elseif ($customerChildDataRow['customerVarcharAttributeId'] == 8){
                       $this->customerData[$customerEntityId]['customer_entity']['suffix'] = $customerChildDataRow['customerVarcharValue'];
                   }elseif ($customerChildDataRow['customerVarcharAttributeId'] == 11){
                       $this->customerData[$customerEntityId]['customer_entity']['dob'] = $customerChildDataRow['customerVarcharValue'];
                   }elseif ($customerChildDataRow['customerVarcharAttributeId'] == 12){
                       $this->customerData[$customerEntityId]['customer_entity']['password_hash'] = $customerChildDataRow['customerVarcharValue'];
                   }elseif ($customerChildDataRow['customerVarcharAttributeId'] == 126){
                       $this->customerData[$customerEntityId]['customer_entity']['rp_token'] = $customerChildDataRow['customerVarcharValue'];
                   }else {
                       $this->customerData[$customerEntityId]['customer_varchar'][$customerChildDataRow['customerVarcharValueId']]['value_id'] = $customerChildDataRow['customerVarcharValueId'];
                       $this->customerData[$customerEntityId]['customer_varchar'][$customerChildDataRow['customerVarcharValueId']]['entity_type_id'] = $customerChildDataRow['customerVarcharEntityTypeId'];
                       $this->customerData[$customerEntityId]['customer_varchar'][$customerChildDataRow['customerVarcharValueId']]['attribute_id'] = $customerChildDataRow['customerVarcharAttributeId'];
                       $this->customerData[$customerEntityId]['customer_varchar'][$customerChildDataRow['customerVarcharValueId']]['entity_id'] = $customerEntityId;
                       $this->customerData[$customerEntityId]['customer_varchar'][$customerChildDataRow['customerVarcharValueId']]['value'] = $customerChildDataRow['customerVarcharValue'];
                   }
               }

               if(!isset($this->customerData[$customerEntityId]['customer_datetime'][$customerChildDataRow['customerDateTimeValueId']])){
                   if($customerChildDataRow['customerDateTimeValueId'] == 127){
                       $this->customerData[$customerEntityId]['customer_entity']['rp_token_datetime'] = $customerChildDataRow['customerDateTimeValue'];
                   }else {
                       $this->customerData[$customerEntityId]['customer_datetime'][$customerChildDataRow['customerDateTimeValueId']]['value_id'] = $customerChildDataRow['customerDateTimeValueId'];
                       $this->customerData[$customerEntityId]['customer_datetime'][$customerChildDataRow['customerDateTimeValueId']]['entity_type_id'] = $customerChildDataRow['customerDateTimeEntityTypeId'];
                       $this->customerData[$customerEntityId]['customer_datetime'][$customerChildDataRow['customerDateTimeValueId']]['attribute_id'] = $customerChildDataRow['customerDateTimeAttributeId'];
                       $this->customerData[$customerEntityId]['customer_datetime'][$customerChildDataRow['customerDateTimeValueId']]['entity_id'] = $customerEntityId;
                       $this->customerData[$customerEntityId]['customer_datetime'][$customerChildDataRow['customerDateTimeValueId']]['value'] = $customerChildDataRow['customerDateTimeValue'];
                   }
               }

               $this->customerAddress($customerEntityId);
           }

       }


       public function customerAddress($customerEntity){
           //$this->sourceDBConnectInit();
           $customerAddressSql = "
                                    select 
                                       customer_address_entity.entity_id  as  customerAddressEntityEntityId,
                                       customer_address_entity.entity_type_id  as customerAddressEntityTypeId,
                                       customer_address_entity.attribute_set_id as customerAddressEntityAttributeSetId,
                                       customer_address_entity.increment_id as customerAddressEntityIncrementId,
                                       customer_address_entity.parent_id  as customerAddressEntityParentId,
                                       customer_address_entity.created_at as customerAddressEntityCreatedAt,
                                       customer_address_entity.updated_at as customerAddressEntityUpdatedAt,
                                       customer_address_entity.is_active  as customerAddressEntityIsActive,
                                    
                                       customer_address_text.value_id as customerAddressTextValueId,
                                       customer_address_text.entity_type_id as customerAddressTextEntityTypeId,
                                       customer_address_text.attribute_id as customerAddressTextAttributeId,
                                       customer_address_text.entity_id as customerAddressTextEntityId,
                                       customer_address_text.value as customerAddressTextValue,
                                    
                                    
                                       customer_address_int.value_id as customerAddressIntValueId,
                                       customer_address_int.entity_type_id as customerAddressIntEntityTypeId,
                                       customer_address_int.attribute_id as customerAddressIntAttributeId,
                                       customer_address_int.entity_id as customerAddressIntEntityId,
                                       customer_address_int.value as customerAddressIntValue,
                                    
                                       customer_address_varchar.value_id as customerAddressVarcharValueId,
                                       customer_address_varchar.entity_type_id as customerAddressVarcharEntityTypeId,
                                       customer_address_varchar.attribute_id as customerAddressVarcharAttributeId,
                                       customer_address_varchar.entity_id as customerAddressVarcharEntityId,
                                       customer_address_varchar.value as customerAddressVarcharValue,
                                    
                                       customer_address_text_safety.value_id as customerAddressTextSafteyValueId,
                                       customer_address_text_safety.entity_type_id as customerAddressTextSafteyEntityTypeId,
                                       customer_address_text_safety.attribute_id as customerAddressTextSafteyAttributeId,
                                       customer_address_text_safety.entity_id as customerAddressTextSafteyEntityId,
                                       customer_address_text_safety.value as customerAddressTextSafteyValue
                                    
                                    
                                    from mage_customer_address_entity as customer_address_entity
                                    LEFT JOIN  mage_customer_address_entity_int as customer_address_int ON customer_address_entity.entity_id = customer_address_int.entity_id                                    
                                    LEFT JOIN  mage_customer_address_entity_text as customer_address_text ON customer_address_entity.entity_id = customer_address_text.entity_id                                    
                                    LEFT JOIN  mage_customer_address_entity_varchar as customer_address_varchar ON customer_address_entity.entity_id = customer_address_varchar.entity_id
                                    LEFT JOIN  mage_customer_address_entity_text_safety as customer_address_text_safety ON customer_address_entity.entity_id = customer_address_text_safety.entity_id                                    
                                    where customer_address_entity.parent_id = $customerEntity";
           //echo $customerAddressSql;
           $customerAddressResults = $this->sourceDBconn->query($customerAddressSql);
           //$customerChildDataResults = $this->sourceDBconn->query($customerChildDataSql);
           while ($customerAddressRow = mysqli_fetch_array($customerAddressResults)){
               if(!isset($this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]) && isset($customerAddressRow['customerAddressEntityEntityId'])) {
                   $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['entity_id'] = $customerAddressRow['customerAddressEntityEntityId'];
                   $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['entity_type_id'] = $customerAddressRow['customerAddressEntityTypeId'];
                   $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['attribute_set_id'] = $customerAddressRow['customerAddressEntityAttributeSetId'];
                   $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['increment_id'] = $customerAddressRow['customerAddressEntityIncrementId'];
                   $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['parent_id'] = $customerAddressRow['customerAddressEntityParentId'];
                   $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['created_at'] = $customerAddressRow['customerAddressEntityCreatedAt'];
                   $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['updated_at'] = $customerAddressRow['customerAddressEntityUpdatedAt'];
                   $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['is_active'] = $customerAddressRow['customerAddressEntityIsActive'];
               }
               if(!isset($this->customerData[$customerEntity]['customer_address_text'][$customerAddressRow['customerAddressEntityEntityId']]) && isset($customerAddressRow['customerAddressTextValueId'])) {
                  /* $this->customerData[$customerEntity]['customer_address_text'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextValueId']]['value_id'] = $customerAddressRow['customerAddressTextValueId'];
                   $this->customerData[$customerEntity]['customer_address_text'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextValueId']]['entity_type_id'] = $customerAddressRow['customerAddressTextEntityTypeId'];
                   $this->customerData[$customerEntity]['customer_address_text'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextValueId']]['attribute_id'] = $customerAddressRow['customerAddressTextAttributeId'];
                   $this->customerData[$customerEntity]['customer_address_text'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextValueId']]['entity_id'] = $customerAddressRow['customerAddressTextEntityId'];
                   $this->customerData[$customerEntity]['customer_address_text'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextValueId']]['value'] = $customerAddressRow['customerAddressTextValue'];*/
                  if($customerAddressRow['customerAddressTextAttributeId'] == 25){
                      $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['street'] = $customerAddressRow['customerAddressTextValue'];
                  }
               }

               if(!isset($this->customerData[$customerEntity]['customer_address_int'][$customerAddressRow['customerAddressEntityEntityId']]) && isset($customerAddressRow['customerAddressIntValueId'])) {
                   /*$this->customerData[$customerEntity]['customer_address_int'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressIntValueId']]['value_id'] = $customerAddressRow['customerAddressIntValueId'];
                   $this->customerData[$customerEntity]['customer_address_int'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressIntValueId']]['entity_type_id'] = $customerAddressRow['customerAddressIntEntityTypeId'];
                   $this->customerData[$customerEntity]['customer_address_int'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressIntValueId']]['attribute_id'] = $customerAddressRow['customerAddressIntAttributeId'];
                   $this->customerData[$customerEntity]['customer_address_int'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressIntValueId']]['entity_id'] = $customerAddressRow['customerAddressIntEntityId'];
                   $this->customerData[$customerEntity]['customer_address_int'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressIntValueId']]['value'] = $customerAddressRow['customerAddressIntValue'];*/
                   if($customerAddressRow['customerAddressIntAttributeId'] == 133){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['vat_request_success'] = $customerAddressRow['customerAddressIntValue'];
                   }elseif ($customerAddressRow['customerAddressIntAttributeId'] == 29){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['region_id'] = $customerAddressRow['customerAddressIntValue'];
                   }elseif ($customerAddressRow['vat_is_valid'] == 130){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['vat_is_valid'] = $customerAddressRow['customerAddressIntValue'];
                   }
               }

               if(!isset($this->customerData[$customerEntity]['customer_address_varchar'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressVarcharValueId']]) && isset($customerAddressRow['customerAddressVarcharValueId'])) {
                   /*$this->customerData[$customerEntity]['customer_address_varchar'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressVarcharValueId']]['value_id'] = $customerAddressRow['customerAddressVarcharValueId'];
                   $this->customerData[$customerEntity]['customer_address_varchar'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressVarcharValueId']]['entity_type_id'] = $customerAddressRow['customerAddressVarcharEntityTypeId'];
                   $this->customerData[$customerEntity]['customer_address_varchar'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressVarcharValueId']]['attribute_id'] = $customerAddressRow['customerAddressVarcharAttributeId'];
                   $this->customerData[$customerEntity]['customer_address_varchar'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressVarcharValueId']]['entity_id'] = $customerAddressRow['customerAddressVarcharEntityId'];
                   $this->customerData[$customerEntity]['customer_address_varchar'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressVarcharValueId']]['value'] = $customerAddressRow['customerAddressVarcharValue'];*/
                   if($customerAddressRow['customerAddressVarcharAttributeId'] == 19){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['prefix'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif($customerAddressRow['customerAddressVarcharAttributeId'] == 20){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['firstname'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 21){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['middlename'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 22){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['lastname'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 23){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['suffix'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 24){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['company'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 26){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['city'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 27){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['country_id'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 28){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['region'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 30){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['postcode'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 31){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['telephone'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 32){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['fax'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 129){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['vat_id'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 131){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['vat_request_id'] = $customerAddressRow['customerAddressVarcharValue'];
                   }elseif ($customerAddressRow['customerAddressVarcharAttributeId'] == 132){
                       $this->customerData[$customerEntity]['customer_address_entity'][$customerAddressRow['customerAddressEntityEntityId']]['vat_request_date'] = $customerAddressRow['customerAddressVarcharValue'];
                   }
               }

              /* if(!isset($this->customerData[$customerEntity]['customer_address_text_safety'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextSafteyValueId']]) && isset($customerAddressRow['customerAddressTextSafteyValueId'])) {
                   $this->customerData[$customerEntity]['customer_address_text_safety'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextSafteyValueId']]['value_id'] = $customerAddressRow['customerAddressTextSafteyValueId'];
                   $this->customerData[$customerEntity]['customer_address_text_safety'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextSafteyValueId']]['entity_type_id'] = $customerAddressRow['customerAddressTextSafteyEntityTypeId'];
                   $this->customerData[$customerEntity]['customer_address_text_safety'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextSafteyValueId']]['attribute_id'] = $customerAddressRow['customerAddressTextSafteyAttributeId'];
                   $this->customerData[$customerEntity]['customer_address_text_safety'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextSafteyValueId']]['entity_id'] = $customerAddressRow['customerAddressTextSafteyEntityId'];
                   $this->customerData[$customerEntity]['customer_address_text_safety'][$customerAddressRow['customerAddressEntityEntityId']][$customerAddressRow['customerAddressTextSafteyValueId']]['value'] = $customerAddressRow['customerAddressTextSafteyValue'];
               }*/
           }
           //echo "<pre>"; print_r($this->customerData);
       }



       public function ordersDataSync(){
           
		   $data = date('Y-m-d',strtotime("-1 days"));
		   
		   $fromDate = $data.' 00:00:00';
	       $toDate = $data.' 23:59:59';

           //$fromDate = '2019-07-09 00:00:00';
           //$toDate = '2019-07-09 23:59:59';

           $salesFlatOrderSql = "select * from mage_sales_flat_order where created_at >= '$fromDate' and created_at <= '$toDate' "; //increment_id=1000363515";
           //$salesFlatOrderSql = "select * from mage_sales_flat_order where entity_id = 1003224";
           $salesFlatOrderResults = $this->sourceDBconn->query($salesFlatOrderSql);

           $salesGridOrderSql = "select * from mage_sales_flat_order_grid where created_at >= '$fromDate' and created_at <= '$toDate' ";
           //$salesGridOrderSql = "select * from mage_sales_flat_order_grid where entity_id = 1003224";
           $salesGridOrderResults = $this->sourceDBconn->query($salesGridOrderSql);

           $orderGridArray = array();

           while ($salesGridOrderRow = mysqli_fetch_array($salesGridOrderResults)){
               $orderGridArray[$salesGridOrderRow['entity_id']] = $salesGridOrderRow;
           }


           while ($salesFlatOrderRow =  mysqli_fetch_array($salesFlatOrderResults)){
               $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order'] = $salesFlatOrderRow;
               if(isset($orderGridArray[$salesFlatOrderRow['entity_id']])) {
                   $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_grid'] = $orderGridArray[$salesFlatOrderRow['entity_id']];
               }
               $salesOrderDataSql = $this->getOrderDataDetails($salesFlatOrderRow);

               $salesOrderAddressSql = "select * from mage_sales_flat_order_address where parent_id = $salesFlatOrderRow[entity_id]";
               $salesOrderAddressResults = $this->sourceDBconn->query($salesOrderAddressSql);
               while ($salesOrderAddressRow = mysqli_fetch_array($salesOrderAddressResults)){
                   $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_address'][$salesOrderAddressRow['entity_id']] = $salesOrderAddressRow;
                   if($salesOrderAddressRow['address_type'] == 'billing'){
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order']['billingAddessToString'] = "$salesOrderAddressRow[street], $salesOrderAddressRow[city],
                                                 $salesOrderAddressRow[region], $salesOrderAddressRow[postcode]";
                   }elseif ($salesOrderAddressRow['address_type'] == 'shipping'){
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order']['shippingAddessToString'] = "$salesOrderAddressRow[street], $salesOrderAddressRow[city],
                                                 $salesOrderAddressRow[region], $salesOrderAddressRow[postcode]";
                   }
               }
              $this->getInvoiceDetails($salesFlatOrderRow['entity_id']);
              $this->getCreditMemo($salesFlatOrderRow['entity_id']);
              $this->getShipmentDetails($salesFlatOrderRow['entity_id']);
              $this->getOrderTaxDetails($salesFlatOrderRow['entity_id']);
           }


           //echo "<pre>"; print_r($this->orderData);
       }

       public function checkOrderExist($incrementId){
           $checkOrderExistSql = "select * from sales_order where increment_id = $incrementId";
           $this->dataSaveLog("------------------Is order Exists-----------------------");
           $this->dataSaveLog($incrementId);
           $results = $this->destinationDBconn->query($checkOrderExistSql);
           if(!mysqli_fetch_array($results)){
               return false;
           }
           return true;
       }

       public function saveOrderData(){
           if(empty($this->storeData)){
               $this->getStoresData();
           }
           if(empty($this->M2storeData)){
               $this->getM2StoresData();
           }
           try {
               $this->ordersDataSync();
               if (!empty($this->orderData)) {
                   foreach ($this->orderData as $orderID => $orderDataItem) {
                       if(!array_key_exists($orderDataItem['sales_flat_order']['store_id'],$this->M2storeData)){
                           $this->createLog("Store Id not avilable for this Order ---".$orderDataItem['sales_flat_order']['store_id'].'---------'.$orderDataItem['sales_flat_order']['increment_id']);
                           continue;
                       }
                       $incrementId = $orderDataItem['sales_flat_order']['increment_id'];
                       $orderExist = $this->checkOrderExist($incrementId);

                       if($orderExist != ''){
                           $this->createLog($incrementId."  Order exits");
                           continue;
                       }
                       $orderInsertId = '';
                       $savedOrderId = '';
                       $pamentInsertId = '';
                       $orderGridInsertId = '';
                       $customerName = '';

                       if($orderDataItem['sales_flat_order']['customer_is_guest'] == 0) {
                           $customerExistId = $this->checkCustomerExistM2($orderDataItem['sales_flat_order']['customer_email'], $orderDataItem['sales_flat_order']['store_id'],$orderDataItem['sales_flat_order']['customer_id'],$orderDataItem['sales_flat_order']['customer_id']);
                           //echo $customerExistId; 
                           if ($customerExistId == '') {
                               $customerEmail = $orderDataItem['sales_flat_order']['customer_email'];
                               $customerId = $orderDataItem['sales_flat_order']['customer_id'];
                               $storeId = $orderDataItem['sales_flat_order']['store_id'];
                               $this->customerDataSync($date = false, $email = $customerEmail, $storeId,$customerId);
                               $this->saveCustomerData($date = false, $orderDataItem['sales_flat_order']['customer_id']);
                               $customerExistId = $this->customerData[$orderDataItem['sales_flat_order']['customer_id']]['customer_entity']['m2_id'];
                               if(!isset($this->customerData[$orderDataItem['sales_flat_order']['customer_id']]['customer_entity']['m2_id']) && !$customerExistId){
                                   continue;
                               }
                           }
                       }

                       foreach ($orderDataItem as $orderdataType => $orderItem) {

                           if ($orderdataType == 'sales_flat_order') {
                               $this->showData("-----------------------sales_flat_order-----------------------");
                               $this->showData($orderItem);
                               # Need to think about customer-id here;
                               $orderDataSaveSql = "insert into sales_order (state,status,coupon_code,protect_code,shipping_description,is_virtual,
                                                        store_id,customer_id,base_discount_amount,base_discount_canceled, base_discount_invoiced,base_discount_refunded,base_grand_total,
                                                        base_shipping_amount,base_shipping_canceled,base_shipping_invoiced,
                                                        base_shipping_refunded,base_shipping_tax_amount,base_shipping_tax_refunded,
                                                        base_subtotal,base_subtotal_canceled,base_subtotal_invoiced,base_subtotal_refunded,
                                                        base_tax_amount,base_tax_canceled,base_tax_invoiced,base_tax_refunded,base_to_global_rate,                                                        
                                                        base_to_order_rate,base_total_canceled,base_total_invoiced,base_total_invoiced_cost,
                                                        base_total_offline_refunded,base_total_online_refunded,base_total_paid,base_total_qty_ordered,                                                        
                                                        base_total_refunded,discount_amount,discount_canceled,discount_invoiced,discount_refunded,
                                                        grand_total,shipping_amount,shipping_canceled,shipping_invoiced,shipping_refunded,shipping_tax_amount,                                                        
                                                        shipping_tax_refunded,store_to_base_rate,store_to_order_rate,subtotal,subtotal_canceled,subtotal_invoiced,                                                        
                                                        subtotal_refunded,tax_amount,tax_canceled,tax_invoiced,tax_refunded,total_canceled,total_invoiced,                                                        
                                                        total_online_refunded,total_paid,total_qty_ordered,total_refunded,can_ship_partially,can_ship_partially_item,                                                        
                                                        customer_is_guest,customer_note_notify,billing_address_id,customer_group_id,email_sent,send_email,                                                        
                                                        forced_shipment_with_invoice,payment_auth_expiration,quote_address_id,quote_id,shipping_address_id,adjustment_negative,
                                                        adjustment_positive,base_adjustment_negative,base_adjustment_positive,base_shipping_discount_amount,base_subtotal_incl_tax,
                                                        base_total_due,payment_authorization_amount,shipping_discount_amount,subtotal_incl_tax,total_due,weight,customer_dob,
                                                        increment_id,applied_rule_ids,base_currency_code,customer_email,customer_firstname,customer_lastname,customer_middlename,customer_prefix,
                                                        customer_suffix,customer_taxvat,discount_description,ext_customer_id,ext_order_id,global_currency_code,hold_before_state,hold_before_status,
                                                        order_currency_code,original_increment_id,relation_child_id,relation_child_real_id,relation_parent_id,relation_parent_real_id,remote_ip,
                                                        shipping_method,store_currency_code,store_name,x_forwarded_for,customer_note,created_at,updated_at,total_item_count,customer_gender,                                                        
                                                        discount_tax_compensation_amount,base_discount_tax_compensation_amount,shipping_discount_tax_compensation_amount,base_shipping_discount_tax_compensation_amnt,
                                                        discount_tax_compensation_invoiced,base_discount_tax_compensation_invoiced,discount_tax_compensation_refunded,base_discount_tax_compensation_refunded,
                                                        shipping_incl_tax,base_shipping_incl_tax,coupon_rule_name,paypal_ipn_customer_notified,gift_message_id,is_m1_order) values ";

                               $customerFirstName = addslashes($orderItem['customer_firstname']);
                               $customerMiddleName = addslashes($orderItem['customer_middlename']);
                               $customerLastName = addslashes($orderItem['customer_lastname']);
                               $customerNote = addslashes($orderItem['customer_note']);
                               $customerEmail = addslashes($orderItem['customer_email']);



                               $orderDataSaveSql .= "('$orderItem[state]','$orderItem[status]','$orderItem[coupon_code]','$orderItem[protect_code]','$orderItem[shipping_description]','$orderItem[is_virtual]',
                                                      '$orderItem[store_id]','$customerExistId','$orderItem[base_discount_amount]','$orderItem[base_discount_canceled]','$orderItem[base_discount_invoiced]',
                                                      '$orderItem[base_discount_refunded]','$orderItem[base_grand_total]','$orderItem[base_shipping_amount]','$orderItem[base_shipping_canceled]',
                                                      '$orderItem[base_shipping_invoiced]',
                                                      '$orderItem[base_shipping_refunded]','$orderItem[base_shipping_tax_amount]','$orderItem[base_shipping_tax_refunded]',
                                                      '$orderItem[base_subtotal]','$orderItem[base_subtotal_canceled]','$orderItem[base_subtotal_invoiced]','$orderItem[base_subtotal_refunded]',
                                                      '$orderItem[base_tax_amount]',
                                                      '$orderItem[base_tax_canceled]','$orderItem[base_tax_invoiced]','$orderItem[base_tax_refunded]','$orderItem[base_to_global_rate]','$orderItem[base_to_order_rate]',
                                                      '$orderItem[base_total_canceled]','$orderItem[base_total_invoiced]','$orderItem[base_total_invoiced_cost]','$orderItem[base_total_offline_refunded]',
                                                      '$orderItem[base_total_online_refunded]',
                                                      '$orderItem[base_total_paid]','$orderItem[base_total_qty_ordered]','$orderItem[base_total_refunded]','$orderItem[discount_amount]','$orderItem[discount_canceled]',
                                                      '$orderItem[discount_invoiced]','$orderItem[discount_refunded]','$orderItem[grand_total]','$orderItem[shipping_amount]','$orderItem[shipping_canceled]',
                                                      '$orderItem[shipping_invoiced]',
                                                      '$orderItem[shipping_refunded]','$orderItem[shipping_tax_amount]','$orderItem[shipping_tax_refunded]','$orderItem[store_to_base_rate]',
                                                      '$orderItem[store_to_order_rate]',
                                                      '$orderItem[subtotal]','$orderItem[subtotal_canceled]','$orderItem[subtotal_invoiced]','$orderItem[subtotal_refunded]','$orderItem[tax_amount]',
                                                      '$orderItem[tax_canceled]',
                                                      '$orderItem[tax_invoiced]','$orderItem[tax_refunded]','$orderItem[total_canceled]','$orderItem[total_invoiced]','$orderItem[total_online_refunded]',
                                                      '$orderItem[total_paid]',
                                                      '$orderItem[total_qty_ordered]','$orderItem[total_refunded]','$orderItem[can_ship_partially]','$orderItem[can_ship_partially_item]','$orderItem[customer_is_guest]',
                                                      '$orderItem[customer_note_notify]','$orderItem[billing_address_id]','$orderItem[customer_group_id]','$orderItem[email_sent]',NULL,
                                                      '$orderItem[forced_shipment_with_invoice]','$orderItem[payment_auth_expiration]','$orderItem[quote_address_id]','$orderItem[quote_id]','$orderItem[shipping_address_id]',
                                                      '$orderItem[adjustment_negative]',
                                                      '$orderItem[adjustment_positive]','$orderItem[base_adjustment_negative]','$orderItem[base_adjustment_positive]','$orderItem[base_shipping_discount_amount]',
                                                      '$orderItem[base_subtotal_incl_tax]',
                                                      '$orderItem[base_total_due]','$orderItem[payment_authorization_amount]','$orderItem[shipping_discount_amount]','$orderItem[subtotal_incl_tax]','$orderItem[total_due]',
                                                      '$orderItem[weight]','$orderItem[customer_dob]',
                                                      '$orderItem[increment_id]','$orderItem[applied_rule_ids]','$orderItem[base_currency_code]','$customerEmail','$customerFirstName',
                                                      '$customerLastName','$customerMiddleName',
                                                      '$orderItem[customer_prefix]','$orderItem[customer_suffix]','$orderItem[customer_taxvat]','$orderItem[discount_description]','$orderItem[ext_customer_id]',
                                                      '$orderItem[ext_order_id]','$orderItem[global_currency_code]','$orderItem[hold_before_state]',
                                                      '$orderItem[hold_before_status]','$orderItem[order_currency_code]','$orderItem[original_increment_id]','$orderItem[relation_child_id]',
                                                      '$orderItem[relation_child_real_id]','$orderItem[relation_parent_id]',
                                                      '$orderItem[relation_parent_real_id]','$orderItem[remote_ip]','$orderItem[shipping_method]','$orderItem[store_currency_code]','$orderItem[store_name]',
                                                      '$orderItem[x_forwarded_for]','$customerNote',
                                                      '$orderItem[created_at]','$orderItem[updated_at]','$orderItem[total_item_count]','$orderItem[customer_gender]',0.000,0.00,0.00,0.00,0.00,0.00,0.00,0.00,
                                                      '$orderItem[shipping_incl_tax]',
                                                      '$orderItem[base_shipping_incl_tax]','$orderItem[coupon_rule_name]','$orderItem[paypal_ipn_customer_notified]','$orderItem[gift_message_id]',1)";

                               $this->destinationDBconn->query($orderDataSaveSql);

                               $this->sqlQueries("-----------------Order_Entity-----------------------");
                               $this->sqlQueries($orderDataSaveSql);

                               $orderInsertId = '';
                               if ($this->destinationDBconn->error) {
                                   $this->createLog("-----------------Order_Entity_Error-----------------------");
                                   $this->createLog($this->destinationDBconn->error);
                                   $this->createLog($orderDataSaveSql);
                               } else {
                                   $orderInsertId = $this->destinationDBconn->insert_id;

                                   $foomanOrderSaveSql = "insert into fooman_totals_order (order_id,amount,base_amount,tax_amount,base_tax_amount,amount_invoiced,base_amount_invoiced,
                                                           amount_refunded,base_amount_refunded,type_id,code,label,creation_time,update_time) values 
                                                           ($orderInsertId,'$orderItem[fooman_surcharge_amount]','$orderItem[base_fooman_surcharge_amount]','$orderItem[fooman_surcharge_tax_amount]','$orderItem[base_fooman_surcharge_tax_amount]',
                                                           '$orderItem[fooman_surcharge_amount_invoiced]','$orderItem[base_fooman_surcharge_amount_invoiced]','$orderItem[fooman_surcharge_amount_refunded]','$orderItem[base_fooman_surcharge_amount_refunded]',
                                                           'migrated1','migrated','$orderItem[fooman_surcharge_description]','$orderItem[created_at]','$orderItem[created_at]')";

                                   $this->destinationDBconn->query($foomanOrderSaveSql);

                                   $this->sqlQueries("-----------------Order_Fooman-----------------------");
                                   $this->sqlQueries("M2 Order Id :-".$orderInsertId);
                                   $this->sqlQueries($foomanOrderSaveSql);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------Order_Fooman-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($foomanOrderSaveSql);
                                   }
                               }
                           }

                           if ($orderInsertId) {
                               if ($orderdataType == 'sales_flat_order_grid') {

                                   $this->showData("-----------------------sales_flat_order_grid-----------------------");
                                   $this->showData($orderItem);

                                   $shippingName       = addslashes($this->orderData[$orderID]['sales_flat_order_grid']['shipping_name']);
                                   $billingName        = addslashes($this->orderData[$orderID]['sales_flat_order_grid']['billing_name']);
                                   $billingAddress     = addslashes($this->orderData[$orderID]['sales_flat_order']['billingAddessToString']);
                                   $shippingAddress    = addslashes($this->orderData[$orderID]['sales_flat_order']['shippingAddessToString']);
                                   $shipingInformation = $this->orderData[$orderID]['sales_flat_order']['shipping_description'];
                                   $customerEmail      = addslashes($this->orderData[$orderID]['sales_flat_order']['customer_email']);
                                   $customer_group_id  = $this->orderData[$orderID]['sales_flat_order']['customer_group_id'];
                                   $subtotal           = $this->orderData[$orderID]['sales_flat_order']['subtotal'];
                                   $shipping_amount    = $this->orderData[$orderID]['sales_flat_order']['shipping_amount'];
                                   $payment_method     = $this->orderData[$orderID]['sales_flat_order']['payment_method'];
                                   $total_refunded     = $this->orderData[$orderID]['sales_flat_order']['total_refunded'];
                                   //$storeId            = $this->orderData[$orderID]['sales_flat_order']['total_refunded'];
                                   $customerName       = addslashes($this->orderData[$orderID]['sales_flat_order']['customer_firstname']." ".$this->orderData[$orderID]['sales_flat_order']['customer_middlename']." ".$this->orderData[$orderID]['sales_flat_order']['customer_lastname']);

                                   $orderDataGridSaveSql = "insert into sales_order_grid (entity_id,status,store_id,store_name,customer_id,base_grand_total,base_total_paid,grand_total,total_paid,
                                                                increment_id,base_currency_code,order_currency_code,shipping_name,billing_name,created_at,updated_at,billing_address,shipping_address,shipping_information,
                                                                customer_email,customer_group,subtotal,shipping_and_handling,customer_name,payment_method,total_refunded,signifyd_guarantee_status) values 
                                                                
                                                                ($orderInsertId,'$orderItem[status]','$orderItem[store_id]','$orderItem[store_name]','$orderItem[customer_id]','$orderItem[base_grand_total]',
                                                                 '$orderItem[base_total_paid]',
                                                                 '$orderItem[grand_total]',
                                                                 '$orderItem[total_paid]','$orderItem[increment_id]','$orderItem[base_currency_code]','$orderItem[order_currency_code]','$shippingName',
                                                                 '$billingName','$orderItem[created_at]','$orderItem[updated_at]','$billingAddress','$shippingAddress','$shipingInformation',
                                                                 '$customerEmail','$customer_group_id','$subtotal','$shipping_amount','$customerName','$payment_method',
                                                                 '$total_refunded',NULL)";

                                   $this->destinationDBconn->query($orderDataGridSaveSql);
                                   $this->sqlQueries("-------------------------sales_flat_order_grid---------------------------");
                                   $this->sqlQueries("M2 Order Id :-".$orderInsertId);
                                   $this->sqlQueries($orderDataGridSaveSql);


                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_order_grid-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($orderDataGridSaveSql);
                                   }
                                   //$orderGridInsertId = $this->destinationDBconn->insert_id;

                               } else if ($orderdataType == 'sales_flat_order_item') {

                                   $this->showData("----------------------------sales_flat_order_item----------------");
                                   $this->showData($orderItem);

                                   foreach ($orderItem as $orderItemDataKey => $orderItemData) {

                                       $orderItemSaveSql = "insert into sales_order_item (order_id,parent_item_id,quote_item_id,store_id,created_at,updated_at,product_id,product_type,product_options,weight,
                                                             is_virtual,sku,name,description,applied_rule_ids,additional_data,is_qty_decimal,no_discount,qty_backordered,qty_canceled,qty_invoiced,qty_ordered,                                                             
                                                             qty_refunded,qty_shipped,base_cost,price,base_price,original_price,base_original_price,tax_percent,tax_amount,base_tax_amount,tax_invoiced,                                                             
                                                             base_tax_invoiced,discount_percent,discount_amount,base_discount_amount,discount_invoiced,base_discount_invoiced,amount_refunded,base_amount_refunded,                                                             
                                                             row_total,base_row_total,row_invoiced,base_row_invoiced,row_weight,base_tax_before_discount,tax_before_discount,ext_order_item_id,locked_do_invoice,                                                             
                                                             locked_do_ship,price_incl_tax,base_price_incl_tax,row_total_incl_tax,base_row_total_incl_tax,discount_tax_compensation_amount,base_discount_tax_compensation_amount,
                                                             
                                                             discount_tax_compensation_invoiced,base_discount_tax_compensation_invoiced,discount_tax_compensation_refunded,base_discount_tax_compensation_refunded,                                                             
                                                             tax_canceled,discount_tax_compensation_canceled,tax_refunded,base_tax_refunded,discount_refunded,base_discount_refunded,free_shipping,weee_tax_applied,
                                                             
                                                             weee_tax_applied_amount,weee_tax_applied_row_amount,weee_tax_disposition,weee_tax_row_disposition,base_weee_tax_applied_amount,base_weee_tax_applied_row_amnt,
                                                             
                                                             base_weee_tax_disposition,base_weee_tax_row_disposition,gift_message_id,gift_message_available,sftpSentDate,subsidy,subsidy_vip,member_profit,serial_codes) values ";


                                       if ($orderItemData['parent_item_id'] == '') {

                                           $decription = addslashes($orderItemData['description']);
                                           $sku = addslashes($orderItemData['sku']);
                                           $name = addslashes($orderItemData['name']);

                                           $orderItemSaveSql .= "('$orderInsertId',NULL,'$orderItemData[quote_item_id]','$orderItemData[store_id]','$orderItemData[created_at]','$orderItemData[updated_at]',
                                                                     '$orderItemData[product_id]','$orderItemData[product_type]','$orderItemData[product_options]','$orderItemData[weight]','$orderItemData[is_virtual]',
                                                                     '$sku','$name','$decription','$orderItemData[applied_rule_ids]','$orderItemData[additional_data]',
                                                                     '$orderItemData[is_qty_decimal]',                                                                     
                                                                     '$orderItemData[no_discount]','$orderItemData[qty_backordered]','$orderItemData[qty_canceled]','$orderItemData[qty_invoiced]','$orderItemData[qty_ordered]',
                                                                     '$orderItemData[qty_refunded]','$orderItemData[qty_shipped]','$orderItemData[base_cost]',
                                                                     '$orderItemData[price]','$orderItemData[base_price]','$orderItemData[original_price]','$orderItemData[base_original_price]','$orderItemData[tax_percent]',
                                                                     '$orderItemData[tax_amount]','$orderItemData[base_tax_amount]',                                                                     
                                                                     '$orderItemData[tax_invoiced]','$orderItemData[base_tax_invoiced]','$orderItemData[discount_percent]','$orderItemData[discount_amount]',
                                                                     '$orderItemData[base_discount_amount]',                                                                     
                                                                     '$orderItemData[discount_invoiced]','$orderItemData[base_discount_invoiced]','$orderItemData[amount_refunded]','$orderItemData[base_amount_refunded]',
                                                                     '$orderItemData[row_total]','$orderItemData[base_row_total]','$orderItemData[row_invoiced]',                                                                     
                                                                     '$orderItemData[base_row_invoiced]','$orderItemData[row_weight]','$orderItemData[base_tax_before_discount]','$orderItemData[tax_before_discount]',
                                                                     '$orderItemData[ext_order_item_id]',
                                                                     '$orderItemData[locked_do_invoice]','$orderItemData[locked_do_ship]',                                                                     
                                                                     '$orderItemData[price_incl_tax]','$orderItemData[base_price_incl_tax]','$orderItemData[row_total_incl_tax]','$orderItemData[base_row_total_incl_tax]',
                                                                     '$orderItemData[hidden_tax_amount]',
                                                                     '$orderItemData[base_hidden_tax_amount]','$orderItemData[hidden_tax_invoiced]',                                                                     
                                                                     '$orderItemData[base_hidden_tax_invoiced]','$orderItemData[hidden_tax_refunded]','$orderItemData[base_hidden_tax_refunded]',                                                                     
                                                                     '$orderItemData[tax_canceled]','$orderItemData[hidden_tax_canceled]','$orderItemData[tax_refunded]','$orderItemData[base_tax_refunded]',
                                                                     '$orderItemData[discount_refunded]','$orderItemData[base_discount_refunded]','$orderItemData[free_shipping]',
                                                                     
                                                                     '$orderItemData[weee_tax_applied]','$orderItemData[weee_tax_applied_amount]','$orderItemData[weee_tax_applied_row_amount]','$orderItemData[weee_tax_disposition]',
                                                                     '$orderItemData[weee_tax_row_disposition]','$orderItemData[base_weee_tax_applied_amount]','$orderItemData[base_weee_tax_applied_row_amnt]',
                                                                     '$orderItemData[base_weee_tax_disposition]','$orderItemData[base_weee_tax_row_disposition]','$orderItemData[gift_message_id]',
                                                                     '$orderItemData[gift_message_available]',
                                                                     '$orderItemData[sftpSentDate]','$orderItemData[subsidy]','$orderItemData[subsidy_vip]','$orderItemData[member_profit]','$orderItemData[serial_codes]'
                                                                     )";

                                           $this->destinationDBconn->query($orderItemSaveSql);

                                           $this->sqlQueries("-------------------------sales_flat_order_item---------------------------");
                                           $this->sqlQueries("M2 Order Id :-".$orderInsertId);
                                           $this->sqlQueries($orderItemSaveSql);

                                           if ($this->destinationDBconn->error) {
                                               $this->createLog("-----------------sales_flat_order_item-----------------------");
                                               $this->createLog($this->destinationDBconn->error);
                                               $this->createLog($orderItemSaveSql);
                                           } else {
                                               $this->orderData[$orderID]['sales_flat_order_item'][$orderItemDataKey]['m2id'] = $this->destinationDBconn->insert_id;
                                           }

                                       } else {
                                           $decription = addslashes($orderItemData['description']);
                                           $sku = addslashes($orderItemData['sku']);
                                           $name = addslashes($orderItemData['name']);
                                           $parentItemId = $this->orderData[$orderID]['sales_flat_order_item'][$orderItemData['parent_item_id']]['m2id'];

                                           $orderItemSaveSql .= "('$orderInsertId','$parentItemId','$orderItemData[quote_item_id]','$orderItemData[store_id]','$orderItemData[created_at]','$orderItemData[updated_at]',
                                                                     '$orderItemData[product_id]','$orderItemData[product_type]','$orderItemData[product_options]','$orderItemData[weight]','$orderItemData[is_virtual]',
                                                                     '$sku','$name','$decription','$orderItemData[applied_rule_ids]','$orderItemData[additional_data]',
                                                                     '$orderItemData[is_qty_decimal]',                                                                     
                                                                     '$orderItemData[no_discount]','$orderItemData[qty_backordered]','$orderItemData[qty_canceled]','$orderItemData[qty_invoiced]','$orderItemData[qty_ordered]',
                                                                     '$orderItemData[qty_refunded]','$orderItemData[qty_shipped]','$orderItemData[base_cost]',
                                                                     '$orderItemData[price]','$orderItemData[base_price]','$orderItemData[original_price]','$orderItemData[base_original_price]','$orderItemData[tax_percent]',
                                                                     '$orderItemData[tax_amount]','$orderItemData[base_tax_amount]',                                                                     
                                                                     '$orderItemData[tax_invoiced]','$orderItemData[base_tax_invoiced]','$orderItemData[discount_percent]','$orderItemData[discount_amount]',
                                                                     '$orderItemData[base_discount_amount]',                                                                     
                                                                     '$orderItemData[discount_invoiced]','$orderItemData[base_discount_invoiced]','$orderItemData[amount_refunded]','$orderItemData[base_amount_refunded]',
                                                                     '$orderItemData[row_total]','$orderItemData[base_row_total]','$orderItemData[row_invoiced]',                                                                     
                                                                     '$orderItemData[base_row_invoiced]','$orderItemData[row_weight]','$orderItemData[base_tax_before_discount]','$orderItemData[tax_before_discount]',
                                                                     '$orderItemData[ext_order_item_id]',
                                                                     '$orderItemData[locked_do_invoice]','$orderItemData[locked_do_ship]',                                                                     
                                                                     '$orderItemData[price_incl_tax]','$orderItemData[base_price_incl_tax]','$orderItemData[row_total_incl_tax]','$orderItemData[base_row_total_incl_tax]',
                                                                     '$orderItemData[hidden_tax_amount]',
                                                                     '$orderItemData[base_hidden_tax_amount]','$orderItemData[hidden_tax_invoiced]',                                                                     
                                                                     '$orderItemData[base_hidden_tax_invoiced]','$orderItemData[hidden_tax_refunded]','$orderItemData[base_hidden_tax_refunded]',                                                                     
                                                                     '$orderItemData[tax_canceled]','$orderItemData[hidden_tax_canceled]','$orderItemData[tax_refunded]','$orderItemData[base_tax_refunded]',
                                                                     '$orderItemData[discount_refunded]','$orderItemData[base_discount_refunded]','$orderItemData[free_shipping]',
                                                                     
                                                                     '$orderItemData[weee_tax_applied]','$orderItemData[weee_tax_applied_amount]','$orderItemData[weee_tax_applied_row_amount]','$orderItemData[weee_tax_disposition]',
                                                                     '$orderItemData[weee_tax_row_disposition]','$orderItemData[base_weee_tax_applied_amount]','$orderItemData[base_weee_tax_applied_row_amnt]',
                                                                     '$orderItemData[base_weee_tax_disposition]','$orderItemData[base_weee_tax_row_disposition]','$orderItemData[gift_message_id]',
                                                                     '$orderItemData[gift_message_available]',
                                                                     '$orderItemData[sftpSentDate]','$orderItemData[subsidy]','$orderItemData[subsidy_vip]','$orderItemData[member_profit]','$orderItemData[serial_codes]'
                                                                     )";

                                           $this->destinationDBconn->query($orderItemSaveSql);

                                           $this->sqlQueries("-------------------------sales_flat_order_item---------------------------");
                                           $this->sqlQueries("M2 Order Id :-".$orderInsertId);
                                           $this->sqlQueries($orderItemSaveSql);

                                           if ($this->destinationDBconn->error) {
                                               $this->createLog("-----------------sales_flat_order_item-----------------------");
                                               $this->createLog($this->destinationDBconn->error);
                                               $this->createLog($orderItemSaveSql);
                                           } else {
                                               $this->orderData[$orderID]['sales_flat_order_item'][$orderItemDataKey]['m2id'] = $this->destinationDBconn->insert_id;
                                           }
                                       }
                                   }

                               } else if ($orderdataType == 'sales_flat_order_status_history') {

                                   $this->showData("---------------sales_flat_order_status_history-------------------");
                                   $this->showData($orderItem);

                                   $orderStatusHistorySql = "insert into sales_order_status_history (parent_id,is_customer_notified,is_visible_on_front,comment,
                                                                status,created_at,entity_name) values ";
                                   foreach ($orderItem as $statuHistoryKey => $statusItem) {
                                       $comment = addslashes($statusItem['comment']);
                                       $status = addslashes($statusItem['status']);
                                       $entity_name = addslashes($statusItem['entity_name']);
                                       $orderStatusHistorySql .= "($orderInsertId,'$statusItem[is_customer_notified]','$statusItem[is_visible_on_front]',
                                                                   '$comment','$status','$statusItem[created_at]','$statusItem[entity_name]'),";
                                   }
                                   $orderStatusHistorySqlTrim = rtrim($orderStatusHistorySql, ',');


                                   $this->destinationDBconn->query($orderStatusHistorySqlTrim);

                                   $this->sqlQueries("-------------------------sales_flat_order_status_history---------------------------");
                                   $this->sqlQueries("M2 Order Id :-".$orderInsertId);
                                   $this->sqlQueries($orderStatusHistorySqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_order_status_history-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($orderStatusHistorySqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_order_payment') {

                                   $this->showData("-----------------sales_flat_order_payment---------------------");
                                   $this->showData($orderItem);

                                   foreach ($orderItem as $paymentkey => $paymentItem) {

                                       $orderPaymentSql = "insert into sales_order_payment (parent_id,base_shipping_captured,shipping_captured,amount_refunded,base_amount_paid,amount_canceled,
                                                           base_amount_authorized,base_amount_paid_online,base_amount_refunded_online,base_shipping_amount,shipping_amount,amount_paid,                                                           
                                                           amount_authorized,base_amount_ordered,base_shipping_refunded,shipping_refunded,base_amount_refunded,amount_ordered,base_amount_canceled,                                                           
                                                           quote_payment_id,additional_data,cc_exp_month,cc_ss_start_year,echeck_bank_name,method,cc_debug_request_body,cc_secure_verify,                                                           
                                                           protection_eligibility,cc_approval,cc_last_4,cc_status_description,echeck_type,cc_debug_response_serialized,cc_ss_start_month,                                                           
                                                           echeck_account_type,last_trans_id,cc_cid_status,cc_owner,cc_type,po_number,cc_exp_year,cc_status,echeck_routing_number,account_status,                                                           
                                                           anet_trans_method,cc_debug_response_body,cc_ss_issue,echeck_account_name,cc_avs_status,cc_number_enc,cc_trans_id,address_status,additional_information) values 
                                                           
                                                           ($orderInsertId,'$paymentItem[base_shipping_captured]','$paymentItem[shipping_captured]','$paymentItem[amount_refunded]','$paymentItem[base_amount_paid]',
                                                           '$paymentItem[amount_canceled]','$paymentItem[base_amount_authorized]','$paymentItem[base_amount_paid_online]',
                                                           '$paymentItem[base_amount_refunded_online]','$paymentItem[base_shipping_amount]','$paymentItem[shipping_amount]','$paymentItem[amount_paid]',
                                                           '$paymentItem[amount_authorized]','$paymentItem[base_amount_ordered]','$paymentItem[base_shipping_refunded]','$paymentItem[shipping_refunded]',                                                           
                                                           '$paymentItem[base_amount_refunded]',
                                                           '$paymentItem[amount_ordered]','$paymentItem[base_amount_canceled]','$paymentItem[quote_payment_id]','$paymentItem[additional_data]',
                                                           '$paymentItem[cc_exp_month]',
                                                           '$paymentItem[cc_ss_start_year]','$paymentItem[echeck_bank_name]','$paymentItem[method]','$paymentItem[cc_debug_request_body]',                                                           
                                                           '$paymentItem[cc_secure_verify]','$paymentItem[protection_eligibility]','$paymentItem[cc_approval]','$paymentItem[cc_last4]',
                                                           '$paymentItem[cc_status_description]',
                                                           '$paymentItem[echeck_type]','$paymentItem[cc_debug_response_serialized]','$paymentItem[cc_ss_start_month]','$paymentItem[echeck_account_type]',                                                           
                                                           '$paymentItem[last_trans_id]','$paymentItem[cc_cid_status]','$paymentItem[cc_owner]','$paymentItem[cc_type]','$paymentItem[po_number]',
                                                           '$paymentItem[cc_exp_year]',
                                                           '$paymentItem[cc_status]','$paymentItem[echeck_routing_number]','$paymentItem[account_status]',                                                           
                                                           '$paymentItem[anet_trans_method]','$paymentItem[cc_debug_response_body]','$paymentItem[cc_ss_issue]','$paymentItem[echeck_account_name]',
                                                           '$paymentItem[cc_avs_status]',
                                                           '$paymentItem[cc_number_enc]','$paymentItem[cc_trans_id]','$paymentItem[address_status]','$paymentItem[additional_information]')";


                                       $this->destinationDBconn->query($orderPaymentSql);

                                       $this->sqlQueries("-------------------------sales_flat_order_payment---------------------------");
                                       $this->sqlQueries("M2 Order Id :-".$orderInsertId);
                                       $this->sqlQueries($orderPaymentSql);

                                       if ($this->destinationDBconn->error) {
                                           $this->createLog("-----------------sales_flat_order_payment-----------------------");
                                           $this->createLog($this->destinationDBconn->error);
                                           $this->createLog($orderPaymentSql);
                                       } else {
                                           $this->orderData[$orderID]['sales_flat_order_payment'][$paymentkey]['m2id'] = $this->destinationDBconn->insert_id;
                                       }
                                   }

                               } else if ($orderdataType == 'sales_payment_transaction') {

                                   $this->showData("-----------------sales_payment_transaction---------------------");
                                   $this->showData($orderItem);

                                   $orderPaymentTranscationSql = "insert into sales_payment_transaction (order_id,payment_id,txn_id,parent_txn_id,
                                                                                  txn_type,is_closed,additional_information,created_at) values ";

                                   $orderPaymentTranscationSqlArray = array();

                                   $nullValuePaymentParentId = 1;

                                   foreach ($orderItem as $transcationKey => $paymentTranscItem) {
                                       $paymentInsertId = $this->orderData[$orderID]['sales_flat_order_payment'][$paymentTranscItem['payment_id']]['m2id'];
                                       $additionalInformation = addslashes($paymentTranscItem['additional_information']);
                                       if($paymentInsertId != ''){
                                           $orderPaymentTranscationSqlArray[] = "('$orderInsertId','$paymentInsertId','$paymentTranscItem[txn_id]',
                                                                              '$paymentTranscItem[parent_txn_id]','$paymentTranscItem[txn_type]','$paymentTranscItem[is_closed]',
                                                                              '$additionalInformation','$paymentTranscItem[created_at]')";
                                       }else{
                                           $nullValuePaymentParentId = 0;
                                       }
                                   }

                                   $this->dataSaveLog($orderPaymentTranscationSqlArray);

                                   $orderPaymentTranscationSql .= implode(',',$orderPaymentTranscationSqlArray);


                                   $this->sqlQueries("-------------------------sales_payment_transaction---------------------------");
                                   $this->sqlQueries($orderPaymentTranscationSql);

                                   if($nullValuePaymentParentId == 1) {
                                       $this->destinationDBconn->query($orderPaymentTranscationSql);
                                       $this->destinationDBconn->insert_id;
                                   }else{
                                       $this->createLog("Parent Payment Id missed");
                                       $this->createLog($orderPaymentTranscationSql);
                                   }

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_payment_transaction-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($orderPaymentTranscationSql);
                                   }

                               } else if ($orderdataType == 'sales_flat_order_address') {

                                   $this->showData("-----------------sales_flat_order_address---------------------");
                                   $this->showData($orderItem);

                                   $salesAddressInsertSql = "insert into sales_order_address (parent_id,customer_address_id,quote_address_id,region_id,customer_id,
                                                                 fax,region,postcode,lastname,street,city,email,telephone,country_id,firstname,address_type,prefix,
                                                                 middlename,suffix,company,vat_id,vat_is_valid,vat_request_id,vat_request_date,vat_request_success) values";

                                   foreach ($orderItem as $addresskey => $addressItem) {

                                       $lastName = addslashes($addressItem['lastname']);
                                       $street = addslashes($addressItem['street']);
                                       $city = addslashes($addressItem['city']);
                                       $firstname = addslashes($addressItem['firstname']);
                                       $middlename = addslashes($addressItem['middlename']);
                                       $company = addslashes($addressItem['company']);
                                       $addressEmail = addslashes($addressItem['email']);

                                       $salesAddressInsertSql .= "($orderInsertId,'$addressItem[customer_address_id]','$addressItem[quote_address_id]','$addressItem[region_id]',
                                                                   '$addressItem[customer_id]','$addressItem[fax]',
                                                                   '$addressItem[region]','$addressItem[postcode]','$lastName','$street','$city',
                                                                   '$addressEmail','$addressItem[telephone]','$addressItem[country_id]','$firstname','$addressItem[address_type]',
                                                                   '$addressItem[prefix]','$middlename',
                                                                   '$addressItem[suffix]','$company','$addressItem[vat_id]','$addressItem[vat_is_valid]','$addressItem[vat_request_id]',
                                                                   '$addressItem[vat_request_date]','$addressItem[vat_request_success]'),";
                                   }

                                   $salesAddressInsertSqlTrim = rtrim($salesAddressInsertSql, ',');

                                   $this->sqlQueries("-------------------------sales_flat_order_address---------------------------");
                                   $this->sqlQueries($salesAddressInsertSqlTrim);

                                   $this->destinationDBconn->query($salesAddressInsertSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_order_address-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($salesAddressInsertSqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_order_invoice') {

                                   $this->showData("-----------------sales_flat_order_invoice---------------------");
                                   $this->showData($orderItem);

                                   foreach ($orderItem as $invoiceKey => $invoiceItem) {
                                       $description = addslashes($invoiceItem['discount_description']);
                                       $salesOrderInvoiceInsertSql = "insert into sales_invoice (store_id,base_grand_total,shipping_tax_amount,tax_amount,base_tax_amount,
                                                                          store_to_order_rate,base_shipping_tax_amount,base_discount_amount,base_to_order_rate,grand_total,                                                                          
                                                                          shipping_amount,subtotal_incl_tax,base_subtotal_incl_tax,store_to_base_rate,base_shipping_amount,                                                                          
                                                                          total_qty,base_to_global_rate,subtotal,base_subtotal,discount_amount,billing_address_id,is_used_for_refund,                                                                          
                                                                          order_id,email_sent,send_email,can_void_flag,state,shipping_address_id,store_currency_code,transaction_id,
                                                                          order_currency_code,base_currency_code,global_currency_code,increment_id,created_at,updated_at,discount_tax_compensation_amount,                                                                          
                                                                          base_discount_tax_compensation_amount,shipping_discount_tax_compensation_amount,base_shipping_discount_tax_compensation_amnt,
                                                                          shipping_incl_tax,base_shipping_incl_tax,base_total_refunded,discount_description,customer_note,customer_note_notify) values 
                                                                          
                                                                          ('$invoiceItem[store_id]','$invoiceItem[base_grand_total]','$invoiceItem[shipping_tax_amount]','$invoiceItem[tax_amount]',
                                                                          '$invoiceItem[base_tax_amount]','$invoiceItem[store_to_order_rate]','$invoiceItem[base_shipping_tax_amount]',
                                                                          '$invoiceItem[base_discount_amount]','$invoiceItem[base_to_order_rate]','$invoiceItem[grand_total]','$invoiceItem[shipping_amount]',
                                                                          '$invoiceItem[subtotal_incl_tax]','$invoiceItem[base_subtotal_incl_tax]','$invoiceItem[store_to_base_rate]',
                                                                          '$invoiceItem[base_shipping_amount]','$invoiceItem[total_qty]','$invoiceItem[base_to_global_rate]','$invoiceItem[subtotal]',
                                                                          '$invoiceItem[base_subtotal]',
                                                                          '$invoiceItem[discount_amount]','$invoiceItem[billing_address_id]',
                                                                          '$invoiceItem[is_used_for_refund]','$orderInsertId','$invoiceItem[email_sent]','NULL','$invoiceItem[can_void_flag]',
                                                                          '$invoiceItem[state]',
                                                                          '$invoiceItem[shipping_address_id]',
                                                                          '$invoiceItem[store_currency_code]','$invoiceItem[transaction_id]','$invoiceItem[order_currency_code]','$invoiceItem[base_currency_code]',
                                                                          '$invoiceItem[global_currency_code]','$invoiceItem[increment_id]','$invoiceItem[created_at]',
                                                                          '$invoiceItem[updated_at]','$invoiceItem[hidden_tax_amount]','$invoiceItem[base_hidden_tax_amount]','$invoiceItem[shipping_hidden_tax_amount]',
                                                                          '$invoiceItem[base_shipping_hidden_tax_amnt]','$invoiceItem[shipping_incl_tax]','$invoiceItem[base_shipping_incl_tax]',
                                                                          '$invoiceItem[base_total_refunded]','$description','NULL','NULL')";

                                       $this->destinationDBconn->query($salesOrderInvoiceInsertSql);

                                       $this->sqlQueries("-------------------------sales_flat_order_invoice---------------------------");
                                       $this->sqlQueries($salesOrderInvoiceInsertSql);

                                       if ($this->destinationDBconn->error) {
                                           $this->createLog("-----------------sales_flat_order_invoice-----------------------");
                                           $this->createLog($this->destinationDBconn->error);
                                           $this->createLog($salesOrderInvoiceInsertSql);
                                       } else {
                                           $this->orderData[$orderID]['sales_flat_order_invoice'][$invoiceKey]['m2id'] = $this->destinationDBconn->insert_id;
                                       }

                                   }

                               } else if ($orderdataType == 'sales_flat_order_invoice_grid') {
                                   $storeName          =  $this->orderData[$orderID]['sales_flat_order']['store_name'];
                                   $customerName       =  addslashes($this->orderData[$orderID]['sales_flat_order']['customer_firstname']." ".$this->orderData[$orderID]['sales_flat_order']['customer_middlename']." ".$this->orderData[$orderID]['sales_flat_order']['customer_lastname']);
                                   $customerEmail      =  addslashes($this->orderData[$orderID]['sales_flat_order']['customer_email']);
                                   $customerGroudpId   =  $this->orderData[$orderID]['sales_flat_order']['customer_group_id'];
                                   $paymentMethod      =  $this->orderData[$orderID]['sales_flat_order']['payment_method'];
                                   $billingAddress     =  addslashes($this->orderData[$orderID]['sales_flat_order']['billingAddessToString']);
                                   $shippingAddress    =  addslashes($this->orderData[$orderID]['sales_flat_order']['shippingAddessToString']);
                                   $shipingInformation =  addslashes($this->orderData[$orderID]['sales_flat_order']['shipping_description']);

                                   $salesOrderInvoiceGridSql = "insert into sales_invoice_grid (entity_id,increment_id,state,store_id,store_name,order_id,order_increment_id,
                                                                           order_created_at,customer_name,customer_email,customer_group_id,payment_method,store_currency_code,                                                                           
                                                                           order_currency_code,base_currency_code,global_currency_code,billing_name,billing_address,shipping_address,
                                                                           shipping_information,subtotal,shipping_and_handling,grand_total,base_grand_total,created_at,updated_at) values";

                                   foreach ($orderItem as $invoiceGridKey => $invoiceGridItem) {
                                       $invoiceInsertId = $this->orderData[$orderID]['sales_flat_order_invoice'][$invoiceGridKey]['m2id'];
                                       $subtotal = $this->orderData[$orderID]['sales_flat_order_invoice'][$invoiceGridKey]['subtotal'];
                                       $shippingHandling = $this->orderData[$orderID]['sales_flat_order_invoice'][$invoiceGridKey]['shipping_amount'];
                                       $billingName = addslashes($invoiceGridItem['billing_name']);

                                       $salesOrderInvoiceGridSql .= "('$invoiceInsertId','$invoiceGridItem[increment_id]','$invoiceGridItem[state]','$invoiceGridItem[store_id]','$storeName','$orderInsertId',
                                                                      '$invoiceGridItem[order_increment_id]','$invoiceGridItem[order_created_at]','$customerName','$customerEmail','$customerGroudpId',
                                                                      '$paymentMethod','$invoiceGridItem[store_currency_code]','$invoiceGridItem[order_currency_code]','$invoiceGridItem[base_currency_code]',
                                                                      '$invoiceGridItem[global_currency_code]','$billingName','$billingAddress','$shippingAddress','$shipingInformation',
                                                                      '$subtotal','$shippingHandling','$invoiceGridItem[grand_total]','$invoiceGridItem[base_grand_total]','$invoiceGridItem[created_at]',
                                                                      '$invoiceGridItem[updated_at]'),";
                                   }

                                   $salesOrderInvoiceGridSqlTrim = trim($salesOrderInvoiceGridSql, ',');

                                   $this->destinationDBconn->query($salesOrderInvoiceGridSqlTrim);

                                   $this->sqlQueries("-------------------------sales_flat_order_invoice_grid---------------------------");
                                   $this->sqlQueries($salesOrderInvoiceGridSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_order_invoice_grid-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($salesOrderInvoiceGridSqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_order_invoice_item') {

                                   $this->showData("-----------------------sales_flat_order_invoice_item--------------------");
                                   $this->showData($orderItem);

                                   $invoiceItemSaveSql = "insert into sales_invoice_item (parent_id,base_price,tax_amount,base_row_total,discount_amount,row_total,base_discount_amount,price_incl_tax,
                                                              base_tax_amount,base_price_incl_tax,qty,base_cost,price,base_row_total_incl_tax,row_total_incl_tax,product_id,order_item_id,additional_data,description,
                                                              sku,name,discount_tax_compensation_amount,base_discount_tax_compensation_amount,tax_ratio,weee_tax_applied,weee_tax_applied_amount,weee_tax_applied_row_amount,
                                                              weee_tax_disposition,weee_tax_row_disposition,base_weee_tax_applied_amount,base_weee_tax_applied_row_amnt,base_weee_tax_disposition,base_weee_tax_row_disposition) values ";

                                   $invoiceItemSaveSqlArray = array();

                                   foreach ($orderItem as $invoiceItemKey => $invoiceItemItem) {
                                       $description = addslashes($invoiceItemItem['description']);
                                       $name = addslashes($invoiceItemItem['name']);
                                       $sku = addslashes($invoiceItemItem['sku']);

                                       $parentId = $this->orderData[$orderID]['sales_flat_order_invoice'][$invoiceItemItem['parent_id']]['m2id'];
                                       $orderItemId = $this->orderData[$orderID]['sales_flat_order_item'][$invoiceItemItem['order_item_id']]['m2id'];

                                       $invoiceItemSaveSqlArray[] = "('$parentId','$invoiceItemItem[base_price]','$invoiceItemItem[tax_amount]','$invoiceItemItem[base_row_total]','$invoiceItemItem[discount_amount]',
                                                                    '$invoiceItemItem[row_total]',
                                                                    '$invoiceItemItem[base_discount_amount]',
                                                                    '$invoiceItemItem[price_incl_tax]','$invoiceItemItem[base_tax_amount]','$invoiceItemItem[base_price_incl_tax]','$invoiceItemItem[qty]',
                                                                    '$invoiceItemItem[base_cost]',
                                                                    '$invoiceItemItem[price]','$invoiceItemItem[base_row_total_incl_tax]','$invoiceItemItem[row_total_incl_tax]','$invoiceItemItem[product_id]',
                                                                    '$orderItemId',
                                                                    '$invoiceItemItem[additional_data]','$description','$sku','$name',
                                                                    '$invoiceItemItem[hidden_tax_amount]','$invoiceItemItem[base_hidden_tax_amount]','NULL','$invoiceItemItem[weee_tax_applied]',
                                                                    '$invoiceItemItem[weee_tax_applied_amount]',
                                                                    '$invoiceItemItem[weee_tax_applied_row_amount]','$invoiceItemItem[weee_tax_disposition]',
                                                                    '$invoiceItemItem[weee_tax_row_disposition]','$invoiceItemItem[base_weee_tax_applied_amount]',
                                                                    '$invoiceItemItem[base_weee_tax_applied_row_amnt]',
                                                                    '$invoiceItemItem[base_weee_tax_disposition]','$invoiceItemItem[base_weee_tax_row_disposition]')";

                                   }

                                   $invoiceItemSaveSql .= implode(',',$invoiceItemSaveSqlArray);

                                   //$invoiceItemSaveSqlTrim = rtrim($invoiceItemSaveSql, ',');
                                   $this->destinationDBconn->query($invoiceItemSaveSql);

                                   $this->sqlQueries("-------------------------sales_flat_order_invoice_item---------------------------");
                                   $this->sqlQueries($invoiceItemSaveSql);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_order_invoice_item-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($invoiceItemSaveSql);
                                   }

                               } else if ($orderdataType == 'sales_flat_order_invoice_comment') {

                                   $this->showData("-----------------------sales_flat_order_invoice_comment--------------------");
                                   $this->showData($orderItem);

                                   $salesOrderInvoiceCommentSql = "insert into sales_invoice_comment (parent_id,is_customer_notified,is_visible_on_front,
                                                                   comment,created_at) values ";

                                   foreach ($orderItem as $invoiceCommentKey => $invoiceItemComment) {
                                       if($invoiceCommentKey) {
                                           $parentId = $this->orderData[$orderID]['sales_flat_order_invoice'][$invoiceItemComment['parent_id']]['m2id'];
                                           if($parentId) {
                                               $comment = addslashes($invoiceItemComment['comment']);
                                               $salesOrderInvoiceCommentSql .= "('$parentId','$invoiceItemComment[is_customer_notified]','$invoiceItemComment[is_visible_on_front]',
                                                                                 '$comment','$invoiceItemComment[created_at]'),";
                                           }
                                       }
                                   }

                                   $salesOrderInvoiceCommentSqlTrim = rtrim($salesOrderInvoiceCommentSql, ',');
                                   $this->destinationDBconn->query($salesOrderInvoiceCommentSqlTrim);

                                   $this->dataSaveLog("-------------------------sales_flat_order_invoice_comment---------------------------");
                                   $this->sqlQueries($salesOrderInvoiceCommentSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_order_invoice_comment-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($salesOrderInvoiceCommentSqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_order_invoice_fooman') {
                                   $this->showData("-----------------------sales_flat_order_invoice_fooman--------------------");
                                   $this->showData($orderItem);

                                   $foomanLabel = $this->orderData[$orderID]['sales_flat_order']['fooman_surcharge_description'];
                                   $foomanInvoiceSaveSql = "insert into fooman_totals_invoice (order_id,invoice_id,amount,base_amount,tax_amount,base_tax_amount,type_id,code,label,creation_time,update_time) values ";

                                   foreach ($orderItem as $foomanInvoicekey => $foomanInvoiceItem) {
                                       $invoiceId = $this->orderData[$orderID]['sales_flat_order_invoice'][$foomanInvoicekey]['m2id'];
                                       $foomanInvoiceSaveSql .= "('$orderInsertId','$invoiceId','$foomanInvoiceItem[fooman_surcharge_amount]',
                                                                  '$foomanInvoiceItem[base_fooman_surcharge_amount]','$foomanInvoiceItem[fooman_surcharge_tax_amount]',
                                                                  '$foomanInvoiceItem[base_fooman_surcharge_tax_amount]','migrated1','migrated','$foomanLabel',
                                                                  '$foomanInvoiceItem[created_at]','$foomanInvoiceItem[updated_at]'),";
                                   }
                                   $foomanInvoiceSaveSqlTrim = rtrim($foomanInvoiceSaveSql, ',');
                                   $this->destinationDBconn->query($foomanInvoiceSaveSqlTrim);

                                   $this->sqlQueries("-------------------------sales_flat_order_invoice_fooman---------------------------");
                                   $this->sqlQueries($foomanInvoiceSaveSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_order_invoice_fooman-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($foomanInvoiceSaveSqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_creditmemo') {

                                   $this->showData("-----------------------sales_flat_creditmemo--------------------");
                                   $this->showData($orderItem);

                                   foreach ($orderItem as $creditmemoKey => $creditmemoItem) {
                                       $description = addslashes($creditmemoItem['discount_description']);
                                       $salesCreditmemoSql = "insert into sales_creditmemo (store_id,adjustment_positive,base_shipping_tax_amount,store_to_order_rate,base_discount_amount,base_to_order_rate,
                                                              grand_total,base_adjustment_negative,base_subtotal_incl_tax,shipping_amount,subtotal_incl_tax,adjustment_negative,base_shipping_amount,store_to_base_rate,                                                              
                                                              base_to_global_rate,base_adjustment,base_subtotal,discount_amount,subtotal,adjustment,base_grand_total,base_adjustment_positive,base_tax_amount,shipping_tax_amount,                                                              
                                                              tax_amount,order_id,email_sent,send_email,creditmemo_status,state,shipping_address_id,billing_address_id,invoice_id,store_currency_code,order_currency_code,base_currency_code,
                                                              global_currency_code,transaction_id,increment_id,created_at,updated_at,discount_tax_compensation_amount,base_discount_tax_compensation_amount,shipping_discount_tax_compensation_amount,
                                                              base_shipping_discount_tax_compensation_amnt,shipping_incl_tax,base_shipping_incl_tax,discount_description,customer_note,customer_note_notify,is_m1_order) values
                                                              
                                                              ('$creditmemoItem[store_id]','$creditmemoItem[adjustment_positive]','$creditmemoItem[base_shipping_tax_amount]','$creditmemoItem[store_to_order_rate]',
                                                              '$creditmemoItem[base_discount_amount]',
                                                              '$creditmemoItem[base_to_order_rate]','$creditmemoItem[grand_total]','$creditmemoItem[base_adjustment_negative]',
                                                              '$creditmemoItem[base_subtotal_incl_tax]','$creditmemoItem[shipping_amount]','$creditmemoItem[subtotal_incl_tax]','$creditmemoItem[adjustment_negative]',
                                                              '$creditmemoItem[base_shipping_amount]','$creditmemoItem[store_to_base_rate]','$creditmemoItem[base_to_global_rate]','$creditmemoItem[base_adjustment]',
                                                              '$creditmemoItem[base_subtotal]','$creditmemoItem[discount_amount]','$creditmemoItem[subtotal]','$creditmemoItem[adjustment]',
                                                              '$creditmemoItem[base_grand_total]',
                                                              '$creditmemoItem[base_adjustment_positive]','$creditmemoItem[base_tax_amount]','$creditmemoItem[shipping_tax_amount]',
                                                              '$creditmemoItem[tax_amount]','$orderInsertId','$creditmemoItem[email_sent]','NULL','$creditmemoItem[creditmemo_status]','$creditmemoItem[state]',
                                                              '$creditmemoItem[shipping_address_id]',
                                                              '$creditmemoItem[billing_address_id]',
                                                              '$creditmemoItem[invoice_id]','$creditmemoItem[store_currency_code]','$creditmemoItem[order_currency_code]','$creditmemoItem[base_currency_code]',
                                                              '$creditmemoItem[global_currency_code]',
                                                              '$creditmemoItem[transaction_id]','$creditmemoItem[increment_id]','$creditmemoItem[created_at]',
                                                              '$creditmemoItem[updated_at]','$creditmemoItem[hidden_tax_amount]','$creditmemoItem[base_hidden_tax_amount]','$creditmemoItem[shipping_hidden_tax_amount]',
                                                              '$creditmemoItem[base_shipping_hidden_tax_amnt]','$creditmemoItem[shipping_incl_tax]','$creditmemoItem[base_shipping_incl_tax]',
                                                              '$description','NULL','NULL',1)";
                                       $this->destinationDBconn->query($salesCreditmemoSql);

                                       $this->sqlQueries("-------------------------sales_flat_creditmemo---------------------------");
                                       $this->sqlQueries($salesCreditmemoSql);

                                       if ($this->destinationDBconn->error) {
                                           $this->createLog("-----------------sales_flat_creditmemo-----------------------");
                                           $this->createLog($this->destinationDBconn->error);
                                           $this->createLog($salesCreditmemoSql);
                                       } else {
                                           $this->orderData[$orderID]['sales_flat_creditmemo'][$creditmemoKey]['m2id'] = $this->destinationDBconn->insert_id;
                                       }
                                   }
                               } else if ($orderdataType == 'sales_flat_creditmemo_grid') {
                                   $this->showData("-----------------------sales_flat_creditmemo_grid--------------------");
                                   $this->showData($orderItem);

                                   $orderCreatedAt     = $this->orderData[$orderID]['sales_flat_order']['created_at'];
                                   //$billingName        = $this->orderData[$orderID]['sales_flat_order_invoice_grid']['billing_name'];
                                   $orderStatus        = $this->orderData[$orderID]['sales_flat_order']['status'];
                                   $billingAddress     = addslashes($this->orderData[$orderID]['sales_flat_order']['billingAddessToString']);
                                   $shippingAddress    = addslashes($this->orderData[$orderID]['sales_flat_order']['shippingAddessToString']);
                                   $customerName       = addslashes($this->orderData[$orderID]['sales_flat_order']['customer_firstname']." ".$this->orderData[$orderID]['sales_flat_order']['customer_middlename']." ".$this->orderData[$orderID]['sales_flat_order']['customer_lastname']);
                                   $customerEmail      = addslashes($this->orderData[$orderID]['sales_flat_order']['customer_email']);
                                   $customerGroudpId   = $this->orderData[$orderID]['sales_flat_order']['customer_group_id'];
                                   $paymentMethod      = $this->orderData[$orderID]['sales_flat_order']['payment_method'];
                                   $shipingInformation = addslashes($this->orderData[$orderID]['sales_flat_order']['shipping_description']);

                                   $salesCreditmemoGridSql = "insert into sales_creditmemo_grid (entity_id,increment_id,created_at,updated_at,order_id,order_increment_id,order_created_at,
                                                                       billing_name,state,base_grand_total,order_status,store_id,billing_address,shipping_address,customer_name,customer_email,
                                                                       customer_group_id,payment_method,shipping_information,subtotal,shipping_and_handling,adjustment_positive,adjustment_negative,
                                                                       order_base_grand_total) values ";

                                   foreach ($orderItem as $creditmemoGridKey => $creditmemoGridItem) {
                                       $creditmemoInsertId = $this->orderData[$orderID]['sales_flat_creditmemo'][$creditmemoGridKey]['m2id'];
                                       $billingName = addslashes($creditmemoGridItem['billing_name']);
                                       $salesCreditmemoGridSql .= "('$creditmemoInsertId','$creditmemoGridItem[increment_id]','$creditmemoGridItem[created_at]','$creditmemoGridItem[created_at]',
                                                                    '$orderInsertId','$creditmemoGridItem[order_increment_id]',
                                                                    '$creditmemoGridItem[order_created_at]','$billingName','$creditmemoGridItem[state]','$creditmemoGridItem[base_grand_total]',
                                                                    '$orderStatus',
                                                                    '$creditmemoGridItem[store_id]',
                                                                    '$billingAddress','$shippingAddress','$customerName','$customerEmail','$customerGroudpId','$paymentMethod','$shipingInformation',
                                                                    '$creditmemoGridItem[subtotal]','$shipingInformation','NULL','NULL',
                                                                    '$creditmemoGridItem[order_base_grand_total]'),";

                                   }
                                   $salesCreditmemoGridSqlTrim = rtrim($salesCreditmemoGridSql, ',');
                                   $this->destinationDBconn->query($salesCreditmemoGridSqlTrim);

                                   $this->sqlQueries("-------------------------sales_flat_creditmemo_grid---------------------------");
                                   $this->sqlQueries($salesCreditmemoGridSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_creditmemo_grid-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($salesCreditmemoGridSqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_creditmemo_comment') {

                                   $this->showData("-----------------------sales_flat_creditmemo_comment--------------------");
                                   $this->showData($orderItem);

                                   $creditmemoCommentSaveSql = "insert into sales_creditmemo_comment (parent_id,is_customer_notified,is_visible_on_front,comment,created_at) values ";

                                   foreach ($orderItem as $credimemoCommentKey => $creditmemoCommentItem) {
                                       if($credimemoCommentKey) {
                                           $comment = addslashes($creditmemoCommentItem['comment']);
                                           $parentId = $this->orderData[$orderID]['sales_flat_creditmemo'][$creditmemoCommentItem['parent_id']]['m2id'];
                                           $creditmemoCommentSaveSql .= "('$parentId','$creditmemoCommentItem[is_customer_notified]','$creditmemoCommentItem[is_visible_on_front]',
                                                                          '$comment','$creditmemoCommentItem[created_at]'),";
                                       }
                                   }

                                   $creditmemoCommentSaveSqlTrim = rtrim($creditmemoCommentSaveSql, ',');
                                   $this->destinationDBconn->query($creditmemoCommentSaveSqlTrim);

                                   $this->sqlQueries("-------------------------sales_flat_creditmemo_comment---------------------------");
                                   $this->sqlQueries($creditmemoCommentSaveSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_creditmemo_comment-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($creditmemoCommentSaveSqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_creditmemo_item') {

                                   $this->showData("-----------------------sales_flat_creditmemo_item--------------------");
                                   $this->showData($orderItem);

                                   $credimemoItemsSaveSql = "insert into sales_creditmemo_item (parent_id,base_price,tax_amount,base_row_total,discount_amount,row_total,base_discount_amount,
                                                                price_incl_tax,base_tax_amount,base_price_incl_tax,qty,base_cost,price,base_row_total_incl_tax,row_total_incl_tax,product_id,
                                                                
                                                                order_item_id,additional_data,description,sku,name,discount_tax_compensation_amount,base_discount_tax_compensation_amount,
                                                                
                                                                tax_ratio,weee_tax_applied,weee_tax_applied_amount,weee_tax_applied_row_amount,weee_tax_disposition,weee_tax_row_disposition,
                                                                base_weee_tax_applied_amount,base_weee_tax_applied_row_amnt,base_weee_tax_disposition,base_weee_tax_row_disposition) values ";

                                   $credimemoItemsSaveSqlArray = array();

                                   foreach ($orderItem as $creditmemoKey => $creditMemoItemItem) {
                                       $orderItemId = $this->orderData[$orderID]['sales_flat_order_item'][$creditMemoItemItem['order_item_id']]['m2id'];
                                       $parentId    = $this->orderData [$orderID]['sales_flat_creditmemo'][$creditMemoItemItem['parent_id']]['m2id'];
                                       $description = addslashes($creditMemoItemItem['description']);
                                       $sku = addslashes($creditMemoItemItem['sku']);
                                       $name = addslashes($creditMemoItemItem['name']);

                                       $credimemoItemsSaveSqlArray[] = "('$parentId','$creditMemoItemItem[base_price]','$creditMemoItemItem[tax_amount]','$creditMemoItemItem[base_row_total]',
                                                                      '$creditMemoItemItem[discount_amount]',
                                                                      '$creditMemoItemItem[row_total]',
                                                                      '$creditMemoItemItem[base_discount_amount]','$creditMemoItemItem[price_incl_tax]','$creditMemoItemItem[base_tax_amount]',
                                                                      '$creditMemoItemItem[base_price_incl_tax]',
                                                                      '$creditMemoItemItem[qty]','$creditMemoItemItem[base_cost]',
                                                                      '$creditMemoItemItem[price]','$creditMemoItemItem[base_row_total_incl_tax]','$creditMemoItemItem[row_total_incl_tax]',
                                                                      '$creditMemoItemItem[product_id]',
                                                                      '$orderItemId','$creditMemoItemItem[additional_data]',
                                                                      '$description','$sku','$name','$creditMemoItemItem[hidden_tax_amount]','$creditMemoItemItem[base_hidden_tax_amount]',
                                                                      'NULL','$creditMemoItemItem[weee_tax_applied]','$creditMemoItemItem[weee_tax_applied_amount]',
                                                                      '$creditMemoItemItem[weee_tax_applied_row_amount]','$creditMemoItemItem[weee_tax_disposition]',
                                                                      '$creditMemoItemItem[weee_tax_row_disposition]','$creditMemoItemItem[base_weee_tax_applied_amount]',
                                                                      '$creditMemoItemItem[base_weee_tax_applied_row_amnt]',
                                                                      '$creditMemoItemItem[base_weee_tax_disposition]','$creditMemoItemItem[base_weee_tax_row_disposition]')";
                                   }

                                   $credimemoItemsSaveSql .= implode(',',$credimemoItemsSaveSqlArray);


                                   //$credimemoItemsSaveSqlTrim = rtrim($credimemoItemsSaveSql, ',');

                                   $this->destinationDBconn->query($credimemoItemsSaveSql);

                                   $this->sqlQueries("-------------------------sales_flat_creditmemo_item---------------------------");
                                   $this->sqlQueries($credimemoItemsSaveSql);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_creditmemo_item-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($credimemoItemsSaveSql);
                                   }

                               } else if ($orderdataType == 'sales_flat_creditmemo_fooman') {

                                   $this->showData("-----------------------sales_flat_creditmemo_fooman--------------------");
                                   $this->showData($orderItem);

                                   $foomanLabel = $this->orderData[$orderID]['sales_flat_order']['fooman_surcharge_description'];
                                   $creditmemoFoomanSaveSql = "insert into fooman_totals_creditmemo (order_id,creditmemo_id,amount,base_amount,tax_amount,base_tax_amount,type_id,
                                                                   code,label,creation_time,update_time) values ";
                                   foreach ($orderItem as $creditmemoFoomanKey => $creditmemoFoomanItem) {
                                       $creditmemoId = $this->orderData[$orderID]['sales_flat_creditmemo'][$creditmemoFoomanKey]['m2id'];
                                       $creditmemoFoomanSaveSql .= "('$orderInsertId','$creditmemoId','$creditmemoFoomanItem[fooman_surcharge_amount]','$creditmemoFoomanItem[base_fooman_surcharge_amount]',
                                                                     '$creditmemoFoomanItem[fooman_surcharge_tax_amount]',
                                                                     '$creditmemoFoomanItem[base_fooman_surcharge_tax_amount]','migrated1','migrated','$foomanLabel','$creditmemoFoomanItem[created_at]',
                                                                     '$creditmemoFoomanItem[updated_at]'),";
                                   }
                                   $creditmemoFoomanSaveSqlTrim = rtrim($creditmemoFoomanSaveSql, ',');
                                   $this->destinationDBconn->query($creditmemoFoomanSaveSqlTrim);

                                   $this->sqlQueries("-------------------------sales_flat_creditmemo_fooman---------------------------");
                                   $this->sqlQueries($creditmemoFoomanSaveSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_creditmemo_fooman-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($creditmemoFoomanSaveSqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_shipment') {

                                   $this->showData("-----------------------sales_flat_shipment--------------------");
                                   $this->showData($orderItem);

                                   foreach ($orderItem as $shipmentkey => $shipmentItem) {
                                       $shipmentSaveSql = "insert into sales_shipment (store_id,total_weight,total_qty,email_sent,send_email,order_id,customer_id,shipping_address_id,billing_address_id,
                                                               shipment_status,increment_id,created_at,updated_at,packages,shipping_label,customer_note,customer_note_notify) values 
                                                             
                                                               ('$shipmentItem[store_id]','$shipmentItem[total_weight]','$shipmentItem[total_qty]','$shipmentItem[email_sent]','NULL','$orderInsertId','$shipmentItem[customer_id]',
                                                               '$shipmentItem[shipping_address_id]',
                                                               '$shipmentItem[billing_address_id]','$shipmentItem[shipment_status]','$shipmentItem[increment_id]','$shipmentItem[created_at]','$shipmentItem[updated_at]',
                                                               '$shipmentItem[packages]','$shipmentItem[shipping_label]','NULL','NULL')";

                                       $this->destinationDBconn->query($shipmentSaveSql);

                                       $this->sqlQueries("-------------------------sales_flat_shipment---------------------------");
                                       $this->sqlQueries($shipmentSaveSql);

                                       if ($this->destinationDBconn->error) {
                                           $this->createLog("-----------------sales_flat_shipment-----------------------");
                                           $this->createLog($this->destinationDBconn->error);
                                           $this->createLog($shipmentSaveSql);
                                       } else {
                                           $this->orderData[$orderID]['sales_flat_shipment'][$shipmentkey]['m2id'] = $this->destinationDBconn->insert_id;
                                       }
                                   }

                               } else if ($orderdataType == 'sales_flat_shipment_grid') {

                                   $this->showData("-----------------------sales_flat_shipment_grid--------------------");
                                   $this->showData($orderItem);

                                   $orderCreatedAt     = $this->orderData[$orderID]['sales_flat_order']['created_at'];
                                   //$billingName        = $this->orderData[$orderID]['sales_flat_order_grid']['billing_name'];
                                   $orderStatus        = $this->orderData[$orderID]['sales_flat_order']['status'];
                                   $billingAddress     = addslashes($this->orderData[$orderID]['sales_flat_order']['billingAddessToString']);
                                   $shippingAddress    = addslashes($this->orderData[$orderID]['sales_flat_order']['shippingAddessToString']);
                                   $customerName       = addslashes($this->orderData[$orderID]['sales_flat_order']['customer_firstname']." ".$this->orderData[$orderID]['sales_flat_order']['customer_middlename']." ".$this->orderData[$orderID]['sales_flat_order']['customer_lastname']);
                                   $customerEmail      = addslashes($this->orderData[$orderID]['sales_flat_order']['customer_email']);
                                   $customerGroudpId   = $this->orderData[$orderID]['sales_flat_order']['customer_group_id'];
                                   $paymentMethod      = $this->orderData[$orderID]['sales_flat_order']['payment_method'];
                                   $shipingInformation = addslashes($this->orderData[$orderID]['sales_flat_order']['shipping_description']);
                                   $shippingName       = addslashes($this->orderData[$orderID]['sales_flat_order_grid']['shipping_name']);
                                   $billingName        = addslashes($this->orderData[$orderID]['sales_flat_order_grid']['billing_name']);

                                   $shipmentGridSaveSql = "insert into sales_shipment_grid (entity_id,increment_id,store_id,order_increment_id,order_id,order_created_at,customer_name,total_qty,shipment_status,
                                                                    order_status,billing_address,shipping_address,billing_name,shipping_name,customer_email,customer_group_id,payment_method,shipping_information,
                                                                    created_at,updated_at) values ";

                                   foreach ($orderItem as $shipmentGridkey => $shipmentGridItem) {
                                       $shipmemtInsertId = $this->orderData[$orderID]['sales_flat_shipment'][$shipmentGridkey]['m2id'];
                                       $shipmentGridSaveSql .= "('$shipmemtInsertId','$shipmentGridItem[increment_id]','$shipmentGridItem[store_id]','$shipmentGridItem[order_increment_id]','$orderInsertId',
                                                                 '$shipmentGridItem[order_created_at]',
                                                                 '$customerName','$shipmentGridItem[total_qty]',
                                                                 '$shipmentGridItem[shipment_status]','$orderStatus','$billingAddress','$shippingAddress','$billingName','$shippingName','$customerEmail',
                                                                 '$customerGroudpId','$paymentMethod','$shipingInformation','$shipmentGridItem[created_at]','$shipmentGridItem[created_at]'),";
                                   }

                                   $shipmentGridSaveSqlTrim = rtrim($shipmentGridSaveSql, ',');
                                   $this->destinationDBconn->query($shipmentGridSaveSqlTrim);

                                   $this->sqlQueries("-------------------------sales_flat_shipment_grid---------------------------");
                                   $this->sqlQueries($shipmentGridSaveSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_shipment_grid-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($shipmentGridSaveSqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_shipment_item') {

                                   $this->showData("-----------------------sales_flat_shipment_item--------------------");
                                   $this->showData($orderItem);

                                   $shipmentItemSaveSql = "insert into sales_shipment_item (parent_id,row_total,price,weight,qty,product_id,order_item_id,additional_data,description,name,sku) values ";

                                   $shipmentItemSaveSqlArray = array();

                                   foreach ($orderItem as $shipmentItemKey => $shipmentItemItem) {
                                       $orderItemId = $this->orderData[$orderID]['sales_flat_order_item'][$shipmentItemItem['order_item_id']]['m2id'];
                                       $parentId    = $this->orderData[$orderID]['sales_flat_shipment'][$shipmentItemItem['parent_id']]['m2id'];
                                       $description = addslashes($shipmentItemItem['description']);
                                       $name = addslashes($shipmentItemItem['name']);
                                       $sku = addslashes($shipmentItemItem['sku']);

                                       $shipmentItemSaveSqlArray[] = "('$parentId','$shipmentItemItem[row_total]','$shipmentItemItem[price]','$shipmentItemItem[weight]','$shipmentItemItem[qty]','$shipmentItemItem[product_id]',
                                                                      '$orderItemId',
                                                                      '$shipmentItemItem[additional_data]','$description','$name','$sku')";
                                   }

                                   $shipmentItemSaveSql .= implode(',',$shipmentItemSaveSqlArray);

                                   $shipmentItemSaveSqlTrim = rtrim($shipmentItemSaveSql, ',');
                                   $this->destinationDBconn->query($shipmentItemSaveSqlTrim);

                                   $this->sqlQueries("-------------------------sales_flat_shipment_item---------------------------");
                                   $this->sqlQueries($shipmentItemSaveSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_shipment_item-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($shipmentItemSaveSqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_shipment_track') {

                                   $this->showData("-----------------------sales_flat_shipment_track--------------------");
                                   $this->showData($orderItem);

                                   $shipmenttrackSaveSql = "insert into sales_shipment_track (parent_id,weight,qty,order_id,track_number,description,title,carrier_code,created_at,updated_at) values ";
                                   foreach ($orderItem as $shipmentTrackKey => $shipmentTrackItem) {
                                       $parentId = $this->orderData[$orderID]['sales_flat_shipment'][$shipmentTrackItem['parent_id']]['m2id'];
                                       $description = addslashes($shipmentTrackItem['description']);
                                       $title = addslashes($shipmentTrackItem['title']);
                                       $shipmenttrackSaveSql .= "('$parentId','$shipmentTrackItem[weight]','$shipmentTrackItem[qty]','$shipmentTrackItem[order_id]','$shipmentTrackItem[track_number]',
                                                                  '$description','$title',
                                                                  '$shipmentTrackItem[carrier_code]','$shipmentTrackItem[created_at]','$shipmentTrackItem[updated_at]'),";
                                   }
                                   $shipmentTrackSqlTrim = rtrim($shipmenttrackSaveSql, ',');
                                   $this->destinationDBconn->query($shipmentTrackSqlTrim);

                                   $this->sqlQueries("-------------------------sales_flat_shipment_track---------------------------");
                                   $this->sqlQueries($shipmentTrackSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_shipment_track-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($shipmentTrackSqlTrim);
                                   }

                               } else if ($orderdataType == 'sales_flat_shipment_comment') {

                                   $this->showData("-----------------------sales_flat_shipment_comment--------------------");
                                   $this->showData($orderItem);

                                   $shipmentCommentSaveSql = "insert into sales_shipment_comment (parent_id,is_customer_notified,is_visible_on_front,comment,created_at) values ";

                                   foreach ($orderItem as $shipmentCommentkey => $shipmentCommentItem) {
                                       $parentId = $this->orderData[$orderID]['sales_flat_shipment'][$shipmentCommentItem['parent_id']]['m2id'];
                                       $comment = addslashes($shipmentCommentItem['comment']);
                                       $shipmentCommentSaveSql .= "('$parentId','$shipmentCommentItem[is_customer_notified]','$shipmentCommentItem[is_visible_on_front]',
                                                                    '$comment','$shipmentCommentItem[created_at]'),";
                                   }

                                   $shipmentCommentSaveSqlRaw = rtrim($shipmentCommentSaveSql, ',');
                                   $this->destinationDBconn->query($shipmentCommentSaveSqlRaw);

                                   $this->sqlQueries("-------------------------sales_flat_shipment_comment---------------------------");
                                   $this->sqlQueries($shipmentCommentSaveSqlRaw);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_flat_shipment_comment-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($shipmentCommentSaveSqlRaw);
                                   }

                               } else if ($orderdataType == 'sales_order_tax') {

                                   $this->showData("-----------------------sales_order_tax--------------------");
                                   $this->showData($orderItem);

                                   foreach ($orderItem as $orderTaxkey => $orderTaxItem) {
                                       $title = addslashes($orderTaxItem['title']);
                                       $orderTaxSaveSql = "insert into sales_order_tax (order_id,code,title,percent,amount,priority,position,base_amount,process,base_real_amount) values 
                                                               ($orderInsertId,'$orderTaxItem[code]','$title','$orderTaxItem[percent]','$orderTaxItem[amount]','$orderTaxItem[priority]',
                                                               '$orderTaxItem[position]','$orderTaxItem[base_amount]','$orderTaxItem[process]','$orderTaxItem[base_real_amount]')";

                                       $this->destinationDBconn->query($orderTaxSaveSql);

                                       $this->sqlQueries("-------------------------sales_order_tax---------------------------");
                                       $this->sqlQueries($orderTaxSaveSql);

                                       if ($this->destinationDBconn->error) {
                                           $this->createLog("-----------------sales_order_tax-----------------------");
                                           $this->createLog($this->destinationDBconn->error);
                                           $this->createLog($orderTaxSaveSql);
                                       } else {
                                           $this->orderData[$orderID]['sales_order_tax'][$orderTaxkey]['m2id'] = $this->destinationDBconn->insert_id;
                                       }
                                   }

                               } else if ($orderdataType == 'sales_order_tax_item') {

                                   $this->showData("-----------------------sales_order_tax_item--------------------");
                                   $this->showData($orderItem);

                                   $taxItemSaveSql = "insert into sales_order_tax_item (tax_id,item_id,tax_percent,amount,base_amount,real_amount,real_base_amount,
                                                          associated_item_id,taxable_item_type) values ";

                                   foreach ($orderItem as $taxItemKey => $taxItem) {
                                       $taxId  = $this->orderData[$orderID]['sales_order_tax'][$taxItem['tax_id']]['m2id'];
                                       $itemId = $this->orderData[$orderID]['sales_flat_order_item'][$taxItem['item_id']]['m2id'];
                                       if($taxId && $itemId) {
                                           $taxItemSaveSql .= "('$taxId','$itemId','$taxItem[tax_percent]',0.00,0.00,0.00,0.00,NULL,'product'),";
                                       }
                                   }
                                   $taxItemSaveSqlTrim = rtrim($taxItemSaveSql, ',');
                                   $this->destinationDBconn->query($taxItemSaveSqlTrim);

                                   $this->sqlQueries("-------------------------sales_order_tax_item---------------------------");
                                   $this->sqlQueries($taxItemSaveSqlTrim);

                                   if ($this->destinationDBconn->error) {
                                       $this->createLog("-----------------sales_order_tax_item-----------------------");
                                       $this->createLog($this->destinationDBconn->error);
                                       $this->createLog($taxItemSaveSqlTrim);
                                   }
                               }

                           }
                           //}

                       }

                   }
               }
           }catch (\Exception $e){
               $this->createLog($e->getMessage());
           }
       }


       public function getOrderTaxDetails($salesOrderEntityId){
           try{
              $salesOrderTaxSql = "
                                    select 
                                      sales_order_tax.tax_id as salesOrderTaxTaxId,
                                      sales_order_tax.order_id as salesOrderTaxOrderId,
                                      sales_order_tax.code as salesOrderTaxCode,
                                      sales_order_tax.title	as salesOrderTaxTitle,
                                      sales_order_tax.percent as salesOrderTaxPercent,
                                      sales_order_tax.amount as salesOrderTaxAmount,
                                      sales_order_tax.priority as salesOrderTaxPriority,
                                      sales_order_tax.percent as salesOrderTaxPercentage,
                                      sales_order_tax.position as salesOrderTaxPosition,
                                      sales_order_tax.base_amount as salesOrderTaxBaseAmount,
                                      sales_order_tax.process as salesOrderTaxProcess,
                                      sales_order_tax.base_real_amount as salesOrderTaxBaseRealAmount,
                                      sales_order_tax.hidden as salesOrderTaxHidden,
                                      
                                      sales_order_tax_item.tax_item_id as salesOrderTaxItemTaxItemId,
                                      sales_order_tax_item.tax_id as salesOrderTaxItemTaxId,
                                      sales_order_tax_item.item_id as salesOrderTaxItemItemId,
                                      sales_order_tax_item.tax_percent as salesOrderTaxItemTaxPercentage
                                      
                                      
                                    FROM mage_sales_order_tax as sales_order_tax
                                    LEFT JOIN mage_sales_order_tax_item as sales_order_tax_item ON sales_order_tax.tax_id = sales_order_tax_item.tax_id
                                    WHERE sales_order_tax.order_id = $salesOrderEntityId;
                                 ";
                     $salesOrderTaxResults = $this->sourceDBconn->query($salesOrderTaxSql);
                     while ($salesOrderTaxRow = mysqli_fetch_array($salesOrderTaxResults)){
                         if(!isset($this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]) && $salesOrderTaxRow['salesOrderTaxTaxId']){
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['tax_id'] = $salesOrderTaxRow['salesOrderTaxTaxId'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['order_id'] = $salesOrderTaxRow['salesOrderTaxOrderId'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['code'] = $salesOrderTaxRow['salesOrderTaxCode'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['title'] = $salesOrderTaxRow['salesOrderTaxTitle'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['percent'] = $salesOrderTaxRow['salesOrderTaxPercent'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['amount'] = $salesOrderTaxRow['salesOrderTaxAmount'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['priority'] = $salesOrderTaxRow['salesOrderTaxPriority'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['position'] = $salesOrderTaxRow['salesOrderTaxPosition'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['base_amount'] = $salesOrderTaxRow['salesOrderTaxBaseAmount'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['process'] = $salesOrderTaxRow['salesOrderTaxProcess'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['base_real_amount'] = $salesOrderTaxRow['salesOrderTaxBaseRealAmount'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax'][$salesOrderTaxRow['salesOrderTaxTaxId']]['hidden'] = $salesOrderTaxRow['salesOrderTaxHidden'];
                         }
                         if(!isset($this->orderData[$salesOrderEntityId]['sales_order_tax_item'][$salesOrderTaxRow['salesOrderTaxTaxId']][$salesOrderTaxRow['salesOrderTaxItemTaxItemId']]) && $salesOrderTaxRow['salesOrderTaxItemTaxItemId']){
                             $this->orderData[$salesOrderEntityId]['sales_order_tax_item'][$salesOrderTaxRow['salesOrderTaxItemTaxItemId']]['tax_item_id'] = $salesOrderTaxRow['salesOrderTaxItemTaxItemId'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax_item'][$salesOrderTaxRow['salesOrderTaxItemTaxItemId']]['tax_id'] = $salesOrderTaxRow['salesOrderTaxItemTaxId'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax_item'][$salesOrderTaxRow['salesOrderTaxItemTaxItemId']]['item_id'] = $salesOrderTaxRow['salesOrderTaxItemItemId'];
                             $this->orderData[$salesOrderEntityId]['sales_order_tax_item'][$salesOrderTaxRow['salesOrderTaxItemTaxItemId']]['tax_percent'] = $salesOrderTaxRow['salesOrderTaxItemTaxPercentage'];

                         }
                     }
           }catch (\Exception $e){
               $this->createLog($e->getMessage());
           }
       }

       public function getShipmentDetails($salesOrderEntityId){
           try{
              $salesShipmentSql = "
                                   select 
                                    sales_shipment.entity_id as salesShipmentEntityId,
                                    sales_shipment.store_id as salesShipmentStoreId,
                                    sales_shipment.total_weight as salesShipmentTotalWeight,
                                    sales_shipment.total_qty as salesShipmentTotalQty,
                                    sales_shipment.email_sent as salesShipmentEmailSent,
                                    sales_shipment.order_id as salesShipmentOrderId,
                                    sales_shipment.customer_id as salesShipmentCustomerId,
                                    sales_shipment.shipping_address_id as salesShipmentShippingAddressId,
                                    sales_shipment.billing_address_id as salesShipmentBillingAddressId,
                                    sales_shipment.shipment_status as salesShipmentShipmentStatus,
                                    sales_shipment.increment_id	as salesShipmentInctementId,
                                    sales_shipment.created_at as salesShipmentCreatedAt,
                                    sales_shipment.updated_at as salesShipmentUpdatedAt,
                                    sales_shipment.packages	as salesShipmentPackage,
                                    sales_shipment.shipping_label as salesShipmentShippingLabel,
                                    
                                    sales_shipment_grid.order_increment_id as salesShipmentGridOrderIncrementId,
                                    sales_shipment_grid.order_created_at as salesShipmentGridOrderCreatedAt,
                                    sales_shipment_grid.shipping_name as salesShipmentGridShippingName,      
                                    
                                    sales_shipment_item.entity_id as salesShipmentItemEntityid,                                                          
                                    sales_shipment_item.parent_id as salesShipmentItemParentId,                                                                 
                                    sales_shipment_item.row_total as salesShipmentItemRowTotal,                                                                
                                    sales_shipment_item.price as salesShipmentItemPrice,                                                                
                                    sales_shipment_item.weight as salesShipmentItemWeight,                                                                
                                    sales_shipment_item.qty as salesShipmentItemQty,                                                                
                                    sales_shipment_item.product_id as salesShipmentItemProductId,                                                                
                                    sales_shipment_item.order_item_id as salesShipmentItemOrdeItemId,                                                                
                                    sales_shipment_item.additional_data as salesShipmentItemAdditionalData,                                                                
                                    sales_shipment_item.description as salesShipmentItemDescription,                                                                
                                    sales_shipment_item.name as salesShipmentItemName,                                                                 
                                    sales_shipment_item.sku as salesShipmentItemSku,
                                    
                                    sales_shipment_comment.entity_id as salesShipmentCommentEntityId,                                                                                                    
                                    sales_shipment_comment.parent_id as salesShipmentCommentParentId,                                                                                                    
                                    sales_shipment_comment.is_customer_notified as salesShipmentCommentIsCustomerNotified,                                                                                                    
                                    sales_shipment_comment.is_visible_on_front as salesShipmentCommentIsVisibleOnFront,                                                                                                    
                                    sales_shipment_comment.comment as salesShipmentCommentComment,                                                                                                    
                                    sales_shipment_comment.created_at as salesShipmentCommentCretedAt,    
                                    
                                    sales_shipment_track.entity_id as salesShipmentTrackEntityid,                                                                                              
                                    sales_shipment_track.parent_id as salesShipmentTrackParentId,                                                                                                
                                    sales_shipment_track.weight	as salesShipmentTrackWeight,                                                                                                
                                    sales_shipment_track.qty as salesShipmentTrackQty,                                                                                                
                                    sales_shipment_track.order_id as salesShipmentTrackOrdeId,                                                                                                
                                    sales_shipment_track.track_number as salesShipmentTrackTrackNumber,                                                                                                
                                    sales_shipment_track.description as salesShipmentTrackDescription,                                                                                                
                                    sales_shipment_track.title as salesShipmentTrackTitle,                                                                                                
                                    sales_shipment_track.carrier_code as salesShipmentTrackCarrierCode,                                                                                                
                                    sales_shipment_track.created_at as salesShipmentTrackCreatedAt,                                                                                                
                                    sales_shipment_track.updated_at as salesShipmentTrackUpdatedAt                                                                                             
                                                                                                                                    
                                    
                                    
                                    FROM mage_sales_flat_shipment as sales_shipment
                                    LEFT JOIN mage_sales_flat_shipment_grid as sales_shipment_grid ON sales_shipment.order_id = sales_shipment_grid.order_id
                                    LEFT JOIN mage_sales_flat_shipment_item as sales_shipment_item ON sales_shipment.entity_id = sales_shipment_item.parent_id
                                    LEFT JOIN mage_sales_flat_shipment_comment as sales_shipment_comment ON sales_shipment.entity_id = sales_shipment_comment.parent_id
                                    LEFT JOIN mage_sales_flat_shipment_track as sales_shipment_track ON sales_shipment.entity_id = sales_shipment_track.parent_id
                                    WHERE sales_shipment.order_id = $salesOrderEntityId
                                   ";
              $salesShipmentResults = $this->sourceDBconn->query($salesShipmentSql);
              while ($salesShipmentRow = mysqli_fetch_array($salesShipmentResults)){
                  //echo "<pre>"; print_r($salesShipmentRow);
                  if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']])){
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['entity_id'] = $salesShipmentRow['salesShipmentEntityId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['store_id'] = $salesShipmentRow['salesShipmentStoreId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['total_weight'] = $salesShipmentRow['salesShipmentTotalWeight'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['total_qty'] = $salesShipmentRow['salesShipmentTotalQty'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['email_sent'] = $salesShipmentRow['salesShipmentEmailSent'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['order_id'] = $salesShipmentRow['salesShipmentOrderId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['customer_id'] = $salesShipmentRow['salesShipmentCustomerId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['shipping_address_id'] = $salesShipmentRow['salesShipmentShippingAddressId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['billing_address_id'] = $salesShipmentRow['salesShipmentBillingAddressId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['shipment_status'] = $salesShipmentRow['salesShipmentShipmentStatus'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['increment_id'] = $salesShipmentRow['salesShipmentInctementId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['created_at'] = $salesShipmentRow['salesShipmentCreatedAt'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['updated_at'] = $salesShipmentRow['salesShipmentUpdatedAt'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['packages'] = $salesShipmentRow['salesShipmentPackage'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment'][$salesShipmentRow['salesShipmentEntityId']]['shipping_label'] = $salesShipmentRow['salesShipmentShippingLabel'];
                  }
                  if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']])){
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']]['entity_id'] = $salesShipmentRow['salesShipmentEntityId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']]['store_id'] = $salesShipmentRow['salesShipmentStoreId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']]['total_qty'] = $salesShipmentRow['salesShipmentTotalQty'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']]['order_id'] = $salesShipmentRow['salesShipmentOrderId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']]['shipment_status'] = $salesShipmentRow['salesShipmentShipmentStatus'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']]['increment_id'] = $salesShipmentRow['salesShipmentInctementId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']]['order_increment_id'] = $salesShipmentRow['salesShipmentGridOrderIncrementId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']]['created_at'] = $salesShipmentRow['salesShipmentCreatedAt'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']]['order_created_at'] = $salesShipmentRow['salesShipmentGridOrderCreatedAt'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_grid'][$salesShipmentRow['salesShipmentEntityId']]['shipping_name'] = $salesShipmentRow['salesShipmentGridShippingName'];
                  }
                  if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]) && $salesShipmentRow['salesShipmentItemEntityid']){
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['entity_id'] = $salesShipmentRow['salesShipmentItemEntityid'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['parent_id'] = $salesShipmentRow['salesShipmentItemParentId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['row_total'] = $salesShipmentRow['salesShipmentItemRowTotal'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['price'] = $salesShipmentRow['salesShipmentItemPrice'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['weight'] = $salesShipmentRow['salesShipmentItemWeight'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['qty'] = $salesShipmentRow['salesShipmentItemQty'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['product_id'] = $salesShipmentRow['salesShipmentItemProductId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['order_item_id'] = $salesShipmentRow['salesShipmentItemOrdeItemId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['additional_data'] = $salesShipmentRow['salesShipmentItemAdditionalData'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['description'] = $salesShipmentRow['salesShipmentItemDescription'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['name'] = $salesShipmentRow['salesShipmentItemName'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_item'][$salesShipmentRow['salesShipmentItemEntityid']]['sku'] = $salesShipmentRow['salesShipmentItemSku'];
                  }
                  if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_shipment_comment'][$salesShipmentRow['salesShipmentCommentEntityId']]) && $salesShipmentRow['salesShipmentCommentEntityId']){
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_comment'][$salesShipmentRow['salesShipmentCommentEntityId']]['entity_id'] = $salesShipmentRow['salesShipmentCommentEntityId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_comment'][$salesShipmentRow['salesShipmentCommentEntityId']]['parent_id'] = $salesShipmentRow['salesShipmentCommentParentId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_comment'][$salesShipmentRow['salesShipmentCommentEntityId']]['is_customer_notified'] = $salesShipmentRow['salesShipmentCommentIsCustomerNotified'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_comment'][$salesShipmentRow['salesShipmentCommentEntityId']]['is_visible_on_front'] = $salesShipmentRow['salesShipmentCommentIsVisibleOnFront'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_comment'][$salesShipmentRow['salesShipmentCommentEntityId']]['comment'] = $salesShipmentRow['salesShipmentCommentComment'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_comment'][$salesShipmentRow['salesShipmentCommentEntityId']]['created_at'] = $salesShipmentRow['salesShipmentCommentCretedAt'];
                  }
                  if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]) && $salesShipmentRow['salesShipmentTrackEntityid']){
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['entity_id'] = $salesShipmentRow['salesShipmentTrackEntityid'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['parent_id'] = $salesShipmentRow['salesShipmentTrackParentId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['weight'] = $salesShipmentRow['salesShipmentTrackWeight'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['qty'] = $salesShipmentRow['salesShipmentTrackQty'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['order_id'] = $salesShipmentRow['salesShipmentTrackOrdeId'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['track_number'] = $salesShipmentRow['salesShipmentTrackTrackNumber'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['description'] = $salesShipmentRow['salesShipmentTrackDescription'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['title'] = $salesShipmentRow['salesShipmentTrackTitle'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['carrier_code'] = $salesShipmentRow['salesShipmentTrackCarrierCode'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['created_at'] = $salesShipmentRow['salesShipmentTrackCreatedAt'];
                      $this->orderData[$salesOrderEntityId]['sales_flat_shipment_track'][$salesShipmentRow['salesShipmentTrackEntityid']]['updated_at'] = $salesShipmentRow['salesShipmentTrackUpdatedAt'];
                  }
              }
              //echo "<pre>"; print_r($this->orderData);

           }catch (\Exception $e){
               $this->createLog($e->getMessage());
           }
       }

       public function getCreditMemo($salesOrderEntityId){
           try{

               $salesCreditmemoSql = " 
                              select 
                              sales_creditmemo.entity_id as salesCreditmemoEntityId,
                          sales_creditmemo.store_id as salesCreditmemoStoreId,
                          sales_creditmemo.adjustment_positive as salesCreditmemoAdjustmentPositive,
                          sales_creditmemo.base_shipping_tax_amount as salesCreditmemoBaseShippingTaxAmount,
                          sales_creditmemo.store_to_order_rate as salesCreditmemoStoreToOrderRate,
                          sales_creditmemo.base_discount_amount as salesCreditmemoBaseDiscountAmount,
                          sales_creditmemo.base_to_order_rate as salesCreditmemoBaseToOrderRate,
                          sales_creditmemo.grand_total as salesCreditmemoGradTotal,
                          sales_creditmemo.base_adjustment_negative as salesCreditmemoBaseAdjustmentNegative,
                          sales_creditmemo.base_subtotal_incl_tax as salesCreditmemoBaseTotalInclTax,
                          sales_creditmemo.shipping_amount as salesCreditmemoShippingAmount,
                          sales_creditmemo.subtotal_incl_tax as salesCreditmemoSubtotalInclTax,
                          sales_creditmemo.adjustment_negative as salesCreditmemoAdjustmentNegative,
                          sales_creditmemo.base_shipping_amount as salesCreditmemoBaseShippingAmount,
                          sales_creditmemo.store_to_base_rate as salesCreditmemoStoreToBaseRate,
                          sales_creditmemo.base_to_global_rate as salesCreditmemoBaseToGlobalRate,
                          sales_creditmemo.base_adjustment as salesCreditmemoBaseAdjustment,
                          sales_creditmemo.base_subtotal as salesCreditmemoBaseSubTotal,
                          sales_creditmemo.discount_amount as salesCreditmemoDiscountAmount,
                          sales_creditmemo.subtotal as salesCreditmemoSubtotal,
                          sales_creditmemo.adjustment as salesCreditmemoAdjustment,
                          sales_creditmemo.base_grand_total as salesCreditmemoBaseGrandTotal,
                          sales_creditmemo.base_adjustment_positive  as salesCreditmemoBaseAdjustmentPositive,
                          sales_creditmemo.base_tax_amount as salesCreditmemoBaseTaxAmount,
                          sales_creditmemo.shipping_tax_amount as salesCreditmemoShippingTaxAmount, 
                          sales_creditmemo.tax_amount as salesCreditmemoTaxAmount,
                          sales_creditmemo.order_id as salesCreditmemoOrderId,
                          sales_creditmemo.email_sent as salesCreditmemoEmailSent,
                          sales_creditmemo.creditmemo_status as salesCreditmemoCreditmemoStatus,
                          sales_creditmemo.state as salesCreditmemoState,
                          sales_creditmemo.shipping_address_id as salesCreditmemoShippingAddressId,
                          sales_creditmemo.billing_address_id as salesCreditmemoBillingAddressId,
                          sales_creditmemo.invoice_id  as salesCreditmemoInvoiceId,
                          sales_creditmemo.store_currency_code as salesCreditmemoStoreCurrencyCode,
                          sales_creditmemo.order_currency_code as salesCreditmemoOrderCurrencyCode,
                          sales_creditmemo.base_currency_code as salesCreditmemoBaseCurrencyCode, 
                          sales_creditmemo.global_currency_code as salesCreditmemoGlobalCurrecnyCode,
                          sales_creditmemo.transaction_id as salesCreditmemoTransactionId,
                          sales_creditmemo.increment_id as salesCreditmemoIncrementId,
                          sales_creditmemo.created_at as salesCreditmemoCreatedAt,
                          sales_creditmemo.updated_at as salesCreditmemoUpdatedAt,
                          sales_creditmemo.hidden_tax_amount as salesCreditmemoHiddenTaxAmount,
                          sales_creditmemo.base_hidden_tax_amount as salesCreditmemoBaseHiddenTaxAmount,
                          sales_creditmemo.shipping_hidden_tax_amount as salesCreditmemoShippingHiddenTaxAmount,
                          sales_creditmemo.base_shipping_hidden_tax_amnt as salesCreditmemoBaseShippingHiddenTaxAmnt,
                          sales_creditmemo.shipping_incl_tax as salesCreditmemoShippingInclTax,
                          sales_creditmemo.base_shipping_incl_tax as salesCreditmemoBaseShippingInclTax,
                          sales_creditmemo.discount_description as salesCreditmemoDiscountDescription,
                          sales_creditmemo.fooman_surcharge_amount as salesCreditmemoFoomanSurchargeAmount,
                          sales_creditmemo.base_fooman_surcharge_tax_amount as salesCreditmemoBaseFoomanSurchargeTaxAmount,
                          sales_creditmemo.base_fooman_surcharge_amount as salesCreditmemoBaseFoomanSurchargeAmount,
                          sales_creditmemo.fooman_surcharge_tax_amount  as salesCreditmemoFoomanSurchargeTaxAmount,
                          
                          sales_creditmemo_item.entity_id as salesCreditmemoItemEntityId,
                          sales_creditmemo_item.parent_id as salesCreditmemoItemParentId, 
                          sales_creditmemo_item.base_price as salesCreditmemoItemBasePrice,
                          sales_creditmemo_item.tax_amount as salesCreditmemoItemTaxAmount,
                          sales_creditmemo_item.base_row_total as salesCreditmemoItemBaseRowTotal,
                          sales_creditmemo_item.discount_amount as salesCreditmemoItemDiscountAmount,
                          sales_creditmemo_item.row_total as salesCreditmemoItemRowTotal,
                          sales_creditmemo_item.base_discount_amount as salesCreditmemoItemBaseDiscountAmount,
                          sales_creditmemo_item.price_incl_tax as salesCreditmemoItemPriceInclTax,
                          sales_creditmemo_item.base_tax_amount as salesCreditmemoItemBaseTaxAmount,
                          sales_creditmemo_item.base_price_incl_tax as salesCreditmemoItemBasePriceInclTax,
                          sales_creditmemo_item.qty as salesCreditmemoItemQty,
                          sales_creditmemo_item.base_cost as salesCreditmemoItemBaseCost,
                          sales_creditmemo_item.price as salesCreditmemoItemPrice,
                          sales_creditmemo_item.base_row_total_incl_tax as salesCreditmemoItemBaseRowToalInclTax,
                          sales_creditmemo_item.row_total_incl_tax as salesCreditmemoItemRowTotalInclTax,
                          sales_creditmemo_item.product_id as salesCreditmemoItemProductId,
                          sales_creditmemo_item.order_item_id as salesCreditmemoItemOrderItemId,
                          sales_creditmemo_item.additional_data as salesCreditmemoItemAdditionalData,
                          sales_creditmemo_item.description as salesCreditmemoItemDescription,
                          sales_creditmemo_item.sku as salesCreditmemoItemSku,
                          sales_creditmemo_item.name as salesCreditmemoItemName,
                          sales_creditmemo_item.hidden_tax_amount as salesCreditmemoItemHiddenTaxAmount,
                          sales_creditmemo_item.base_hidden_tax_amount as salesCreditmemoItemBaseHiddenTaxAmount,
                          sales_creditmemo_item.weee_tax_disposition as salesCreditmemoItemWeeTaxDisposition,
                          sales_creditmemo_item.weee_tax_row_disposition as salesCreditmemoItemWeeTaxRowDisposition,
                          sales_creditmemo_item.base_weee_tax_disposition as salesCreditmemoItemBaseWeeTaxDisposition,
                          sales_creditmemo_item.base_weee_tax_row_disposition as salesCreditmemoItemBaseWeeTaxRowDisposition,
                          sales_creditmemo_item.weee_tax_applied as salesCreditmemoItemWeeTaxApplied,
                          sales_creditmemo_item.base_weee_tax_applied_amount as salesCreditmemoItemBaseWeeTaxAppliedAmount,                          
                          sales_creditmemo_item.base_weee_tax_applied_row_amnt as salesCreditmemoItemBaseWeeTaxAppliedRowAmount,
                          sales_creditmemo_item.weee_tax_applied_amount as salesCreditmemoItemWeeTaxAppliedAmount,
                          sales_creditmemo_item.weee_tax_applied_row_amount as salesCreditmemoItemWeeTaxAppliedRowAmount,                          
                          
                          sales_creditmemo_comment.entity_id as salesCreditmemoCommentEntityId,
                          sales_creditmemo_comment.parent_id as salesCreditmemoCommentParentId,
                          sales_creditmemo_comment.is_customer_notified as salesCreditmemoIsCustomerNotified,
                          sales_creditmemo_comment.is_visible_on_front as salesCreditmemoIsVisibleOnFront,
                          sales_creditmemo_comment.comment	as salesCreditmemoCommentComment,
                          sales_creditmemo_comment.created_at as salesCreditmemoCommentCreatedAt,
                          
                          sales_creditmemo_grid.order_increment_id as salesCreditmemoGridOrderIncrementId,
                          sales_creditmemo_grid.order_created_at as salesCreditmemoGridOrderCreatedAt,
                          sales_creditmemo_grid.billing_name as salesCreditmemoGridBillingName
                          
                        
                          FROM mage_sales_flat_creditmemo as sales_creditmemo
                          LEFT JOIN mage_sales_flat_creditmemo_item as sales_creditmemo_item ON sales_creditmemo.entity_id = sales_creditmemo_item.parent_id
                          LEFT JOIN mage_sales_flat_invoice_comment as sales_creditmemo_comment ON sales_creditmemo.entity_id = sales_creditmemo_comment.parent_id
                          LEFT JOIN mage_sales_flat_invoice_grid as sales_creditmemo_grid ON sales_creditmemo.order_id = sales_creditmemo_grid.order_id
                          WHERE sales_creditmemo.order_id = $salesOrderEntityId";
               $salesCreditmemoResults = $this->sourceDBconn->query($salesCreditmemoSql);
               while ($salesCreditmemoRow = mysqli_fetch_array($salesCreditmemoResults)){
                   //echo "<pre>"; print_r($salesCreditmemoRow);
                   if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']])){
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['entity_id'] = $salesCreditmemoRow['salesCreditmemoEntityId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['store_id'] = $salesCreditmemoRow['salesCreditmemoStoreId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['adjustment_positive'] = $salesCreditmemoRow['salesCreditmemoAdjustmentPositive'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_shipping_tax_amount'] = $salesCreditmemoRow['salesCreditmemoBaseShippingTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['store_to_order_rate'] = $salesCreditmemoRow['salesCreditmemoStoreToOrderRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_discount_amount'] = $salesCreditmemoRow['salesCreditmemoBaseDiscountAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_to_order_rate'] = $salesCreditmemoRow['salesCreditmemoBaseToOrderRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['grand_total'] = $salesCreditmemoRow['salesCreditmemoGradTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_adjustment_negative'] = $salesCreditmemoRow['salesCreditmemoBaseAdjustmentNegative'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_subtotal_incl_tax'] = $salesCreditmemoRow['salesCreditmemoBaseTotalInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['shipping_amount'] = $salesCreditmemoRow['salesCreditmemoShippingAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['subtotal_incl_tax'] = $salesCreditmemoRow['salesCreditmemoSubtotalInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['adjustment_negative'] = $salesCreditmemoRow['salesCreditmemoAdjustmentNegative'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_shipping_amount'] = $salesCreditmemoRow['salesCreditmemoBaseShippingAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['store_to_base_rate'] = $salesCreditmemoRow['salesCreditmemoStoreToBaseRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_to_global_rate'] = $salesCreditmemoRow['salesCreditmemoBaseToGlobalRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_adjustment'] = $salesCreditmemoRow['salesCreditmemoBaseAdjustment'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_subtotal'] = $salesCreditmemoRow['salesCreditmemoBaseSubTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['discount_amount'] = $salesCreditmemoRow['salesCreditmemoDiscountAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['subtotal'] = $salesCreditmemoRow['salesCreditmemoSubtotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['adjustment'] = $salesCreditmemoRow['salesCreditmemoAdjustment'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_grand_total'] = $salesCreditmemoRow['salesCreditmemoBaseGrandTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_adjustment_positive'] = $salesCreditmemoRow['salesCreditmemoBaseAdjustmentPositive'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_tax_amount'] = $salesCreditmemoRow['salesCreditmemoBaseTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['shipping_tax_amount'] = $salesCreditmemoRow['salesCreditmemoShippingTaxAmount'];

                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['tax_amount'] = $salesCreditmemoRow['salesCreditmemoTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['order_id'] = $salesCreditmemoRow['salesCreditmemoOrderId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['email_sent'] = $salesCreditmemoRow['salesCreditmemoEmailSent'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['creditmemo_status'] = $salesCreditmemoRow['salesCreditmemoCreditmemoStatus'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['state'] = $salesCreditmemoRow['salesCreditmemoState'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['shipping_address_id'] = $salesCreditmemoRow['salesCreditmemoShippingAddressId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['billing_address_id'] = $salesCreditmemoRow['salesCreditmemoBillingAddressId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['invoice_id'] = $salesCreditmemoRow['salesCreditmemoInvoiceId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['store_currency_code'] = $salesCreditmemoRow['salesCreditmemoStoreCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['order_currency_code'] = $salesCreditmemoRow['salesCreditmemoOrderCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_currency_code'] = $salesCreditmemoRow['salesCreditmemoBaseCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['global_currency_code'] = $salesCreditmemoRow['salesCreditmemoGlobalCurrecnyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['transaction_id'] = $salesCreditmemoRow['salesCreditmemoTransactionId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['increment_id'] = $salesCreditmemoRow['salesCreditmemoIncrementId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['created_at'] = $salesCreditmemoRow['salesCreditmemoCreatedAt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['updated_at'] = $salesCreditmemoRow['salesCreditmemoUpdatedAt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['hidden_tax_amount'] = $salesCreditmemoRow['salesCreditmemoHiddenTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_hidden_tax_amount'] = $salesCreditmemoRow['salesCreditmemoBaseHiddenTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['shipping_hidden_tax_amount'] = $salesCreditmemoRow['salesCreditmemoShippingHiddenTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_shipping_hidden_tax_amnt'] = $salesCreditmemoRow['salesCreditmemoBaseShippingHiddenTaxAmnt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['shipping_incl_tax'] = $salesCreditmemoRow['salesCreditmemoShippingInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_shipping_incl_tax'] = $salesCreditmemoRow['salesCreditmemoBaseShippingInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo'][$salesCreditmemoRow['salesCreditmemoEntityId']]['discount_description'] = $salesCreditmemoRow['salesCreditmemoDiscountDescription'];

                       if($salesCreditmemoRow['salesCreditmemoEntityId']) {
                           $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_fooman'][$salesCreditmemoRow['salesCreditmemoEntityId']]['fooman_surcharge_amount'] = $salesCreditmemoRow['salesCreditmemoFoomanSurchargeAmount'];
                           $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_fooman'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_fooman_surcharge_tax_amount'] = $salesCreditmemoRow['salesCreditmemoBaseFoomanSurchargeTaxAmount'];
                           $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_fooman'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_fooman_surcharge_amount'] = $salesCreditmemoRow['salesCreditmemoBaseFoomanSurchargeAmount'];
                           $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_fooman'][$salesCreditmemoRow['salesCreditmemoEntityId']]['fooman_surcharge_tax_amount'] = $salesCreditmemoRow['salesCreditmemoFoomanSurchargeTaxAmount'];
                           $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_fooman'][$salesCreditmemoRow['salesCreditmemoEntityId']]['created_at'] = $salesCreditmemoRow['salesCreditmemoCreatedAt'];
                           $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_fooman'][$salesCreditmemoRow['salesCreditmemoEntityId']]['updated_at'] = $salesCreditmemoRow['salesCreditmemoUpdatedAt'];
                       }
                   }
                   if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']])){
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['entity_id'] = $salesCreditmemoRow['salesCreditmemoEntityId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['store_id'] = $salesCreditmemoRow['salesCreditmemoStoreId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['store_to_order_rate'] = $salesCreditmemoRow['salesCreditmemoStoreToOrderRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_to_order_rate'] = $salesCreditmemoRow['salesCreditmemoBaseToOrderRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['grand_total'] = $salesCreditmemoRow['salesCreditmemoGradTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['store_to_base_rate'] = $salesCreditmemoRow['salesCreditmemoStoreToBaseRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_to_global_rate'] = $salesCreditmemoRow['salesCreditmemoBaseToGlobalRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_grand_total'] = $salesCreditmemoRow['salesCreditmemoBaseGrandTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['order_id'] = $salesCreditmemoRow['salesCreditmemoOrderId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['creditmemo_status'] = $salesCreditmemoRow['salesCreditmemoCreditmemoStatus'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['state'] = $salesCreditmemoRow['salesCreditmemoState'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['invoice_id'] = $salesCreditmemoRow['salesCreditmemoInvoiceId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['store_currency_code'] = $salesCreditmemoRow['salesCreditmemoStoreCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['order_currency_code'] = $salesCreditmemoRow['salesCreditmemoOrderCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['base_currency_code'] = $salesCreditmemoRow['salesCreditmemoBaseCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['global_currency_code'] = $salesCreditmemoRow['salesCreditmemoGlobalCurrecnyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['increment_id'] = $salesCreditmemoRow['salesCreditmemoIncrementId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['order_increment_id'] = $salesCreditmemoRow['salesCreditmemoGridOrderIncrementId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['created_at'] = $salesCreditmemoRow['salesCreditmemoCreatedAt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['order_created_at'] = $salesCreditmemoRow['salesCreditmemoGridOrderCreatedAt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['billing_name'] = $salesCreditmemoRow['salesCreditmemoGridBillingName'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['order_base_grand_total'] = $salesCreditmemoRow['salesCreditmemoBaseGrandTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_grid'][$salesCreditmemoRow['salesCreditmemoEntityId']]['subtotal'] = $salesCreditmemoRow['salesCreditmemoSubtotal'];
                   }

                   if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]) && $salesCreditmemoRow['salesCreditmemoItemEntityId']){
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['entity_id'] = $salesCreditmemoRow['salesCreditmemoItemEntityId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['parent_id'] = $salesCreditmemoRow['salesCreditmemoItemParentId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_price'] = $salesCreditmemoRow['salesCreditmemoItemBasePrice'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['tax_amount'] = $salesCreditmemoRow['salesCreditmemoItemTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_row_total'] = $salesCreditmemoRow['salesCreditmemoItemBaseRowTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['discount_amount'] = $salesCreditmemoRow['salesCreditmemoItemDiscountAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['row_total'] = $salesCreditmemoRow['salesCreditmemoItemRowTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_discount_amount'] = $salesCreditmemoRow['salesCreditmemoItemBaseDiscountAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['price_incl_tax'] = $salesCreditmemoRow['salesCreditmemoItemPriceInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_tax_amount'] = $salesCreditmemoRow['salesCreditmemoItemBaseTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_price_incl_tax'] = $salesCreditmemoRow['salesCreditmemoItemBasePriceInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['qty'] = $salesCreditmemoRow['salesCreditmemoItemQty'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_cost'] = $salesCreditmemoRow['salesCreditmemoItemBaseCost'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['price'] = $salesCreditmemoRow['salesCreditmemoItemPrice'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_row_total_incl_tax'] = $salesCreditmemoRow['salesCreditmemoItemBaseRowToalInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['row_total_incl_tax'] = $salesCreditmemoRow['salesCreditmemoItemRowTotalInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['product_id'] = $salesCreditmemoRow['salesCreditmemoItemProductId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['order_item_id'] = $salesCreditmemoRow['salesCreditmemoItemOrderItemId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['additional_data'] = $salesCreditmemoRow['salesCreditmemoItemAdditionalData'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['description'] = $salesCreditmemoRow['salesCreditmemoItemDescription'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['sku'] = $salesCreditmemoRow['salesCreditmemoItemSku'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['name'] = $salesCreditmemoRow['salesCreditmemoItemName'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['hidden_tax_amount'] = $salesCreditmemoRow['salesCreditmemoItemHiddenTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_hidden_tax_amount'] = $salesCreditmemoRow['salesCreditmemoItemBaseHiddenTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['weee_tax_disposition'] = $salesCreditmemoRow['salesCreditmemoItemWeeTaxDisposition'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['weee_tax_row_disposition'] = $salesCreditmemoRow['salesCreditmemoItemWeeTaxRowDisposition'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_weee_tax_disposition'] = $salesCreditmemoRow['salesCreditmemoItemBaseWeeTaxDisposition'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_weee_tax_row_disposition'] = $salesCreditmemoRow['salesCreditmemoItemBaseWeeTaxRowDisposition'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['weee_tax_applied'] = json_encode(unserialize($salesCreditmemoRow['salesCreditmemoItemWeeTaxApplied']));
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_weee_tax_applied_amount'] = $salesCreditmemoRow['salesCreditmemoItemBaseWeeTaxAppliedAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['base_weee_tax_applied_row_amnt'] = $salesCreditmemoRow['salesCreditmemoItemBaseWeeTaxAppliedRowAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['weee_tax_applied_amount'] = $salesCreditmemoRow['salesCreditmemoItemWeeTaxAppliedAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_item'][$salesCreditmemoRow['salesCreditmemoItemEntityId']]['weee_tax_applied_row_amount'] = $salesCreditmemoRow['salesCreditmemoItemWeeTaxAppliedRowAmount'];
                   }

                   if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_comment'][$salesCreditmemoRow['salesCreditmemoCommentEntityId']]) && $salesCreditmemoRow['salesCreditmemoCommentEntityId']){
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_comment'][$salesCreditmemoRow['salesCreditmemoCommentEntityId']]['entity_id'] = $salesCreditmemoRow['salesCreditmemoCommentEntityId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_comment'][$salesCreditmemoRow['salesCreditmemoCommentEntityId']]['parent_id'] = $salesCreditmemoRow['salesCreditmemoCommentParentId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_comment'][$salesCreditmemoRow['salesCreditmemoCommentEntityId']]['is_customer_notified'] = $salesCreditmemoRow['salesCreditmemoIsCustomerNotified'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_comment'][$salesCreditmemoRow['salesCreditmemoCommentEntityId']]['is_visible_on_front'] = $salesCreditmemoRow['salesCreditmemoIsVisibleOnFront'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_comment'][$salesCreditmemoRow['salesCreditmemoCommentEntityId']]['comment'] = $salesCreditmemoRow['salesCreditmemoCommentComment'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_creditmemo_comment'][$salesCreditmemoRow['salesCreditmemoCommentEntityId']]['created_at'] = $salesCreditmemoRow['salesCreditmemoCommentCreatedAt'];
                   }
               }

               //echo "<pre>"; print_r($this->orderData);
           }catch (\Exception $e){
               $this->createLog($e->getMessage());
           }
       }

       public function getInvoiceDetails($salesOrderEntityId){
           try{
               $salesInvoiceDataSql = " select
                          sales_invoice.entity_id as salesInvoiceEntityId,
                          sales_invoice.store_id as salesInvoiceStoreId,
                          sales_invoice.base_grand_total as salesInvoiceBaseGrandTotal,
                          sales_invoice.shipping_tax_amount as salesInvoiceShippingTaxAmount,
                          sales_invoice.tax_amount as salesInvoiceTaxAmount,
                          sales_invoice.base_tax_amount as salesInvoiceBaseTaxAmount,
                          sales_invoice.store_to_order_rate as salesInvoiceStoreToOrderRate,
                          sales_invoice.base_shipping_tax_amount as salesInvoiceBaseShippingTaxAmount,
                          sales_invoice.base_discount_amount as salesInvoiceBaseDiscountAmount,
                          sales_invoice.base_to_order_rate as salesInvoiceBaseToOrderRate,
                          sales_invoice.grand_total as salesInvoiceGradTotal,
                          sales_invoice.shipping_amount as salesInvoiceShippingAmount,
                          sales_invoice.subtotal_incl_tax as salesInvoiceSubtotalInclTax,
                          sales_invoice.base_subtotal_incl_tax as salesInvoiceBaseTotalInclTax,
                          sales_invoice.store_to_base_rate as salesInvoiceStoreToBaseRate,
                          sales_invoice.base_shipping_amount as salesInvoiceBaseShippingAmount,
                          sales_invoice.total_qty as salesInvoiceTotalQty,
                          sales_invoice.base_to_global_rate as salesInvoiceBaseToGlobalRate,
                          sales_invoice.subtotal as salesInvoiceSubtotal,
                          sales_invoice.base_subtotal as salesInvoiceBaseSubTotal,
                          sales_invoice.discount_amount as salesInvoiceDiscountAmount,
                          sales_invoice.billing_address_id as salesInvoiceBillingAddressId,
                          sales_invoice.is_used_for_refund as salesInvoiceIsUsedForRedund,
                          sales_invoice.order_id as salesInvoiceOrderId,
                          sales_invoice.email_sent as salesInvoiceEmailSent,
                          sales_invoice.can_void_flag as salesInvoiceCanVoidFlag,
                          sales_invoice.state as salesInvoiceState,
                          sales_invoice.shipping_address_id as salesInvoiceShippingAddressId,
                          sales_invoice.store_currency_code as salesInvoiceStoreCurrencyCode,
                          sales_invoice.transaction_id as salesInvoiceTransactionId,
                          sales_invoice.order_currency_code as salesInvoiceOrderCurrencyCode,
                          sales_invoice.base_currency_code as salesInvoiceBaseCurrencyCode, 
                          sales_invoice.global_currency_code as salesInvoiceGlobalCurrecnyCode,
                          sales_invoice.increment_id as salesInvoiceIncrementId,
                          sales_invoice.created_at as salesInvoiceCreatedAt,
                          sales_invoice.updated_at as salesInvoiceUpdatedAt,
                          sales_invoice.hidden_tax_amount as salesInvoiceHiddenTaxAmount,
                          sales_invoice.base_hidden_tax_amount as salesInvoiceBaseHiddenTaxAmount,
                          sales_invoice.shipping_hidden_tax_amount as salesInvoiceShippingHiddenTaxAmount,
                          sales_invoice.base_shipping_hidden_tax_amnt as salesInvoiceBaseShippingHiddenTaxAmnt,
                          sales_invoice.shipping_incl_tax as salesInvoiceShippingInclTax,
                          sales_invoice.base_shipping_incl_tax as salesInvoiceBaseShippingInclTax,
                          sales_invoice.base_total_refunded as salesInvoiceBaseTotalRefunded,
                          sales_invoice.discount_description as salesInvoiceDiscountDescription,
                          sales_invoice.fooman_surcharge_amount as salesInvoiceFoomanSurchargeAmount,
                          sales_invoice.base_fooman_surcharge_tax_amount as salesInvoiceBaseFoomanSurchargeTaxAmount,
                          sales_invoice.base_fooman_surcharge_amount as salesInvoiceBaseFoomanSurchargeAmount,
                          sales_invoice.fooman_surcharge_tax_amount	as salesInvoiceFoomanSurchargeTaxAmount,
                          
                          sales_invoice_item.entity_id as salesInvoiceItemEntityId,
                          sales_invoice_item.parent_id as salesInvoiceItemParentId, 
                          sales_invoice_item.base_price as salesInvoiceItemBasePrice,
                          sales_invoice_item.tax_amount as salesInvoiceItemTaxAmount,
                          sales_invoice_item.base_row_total as salesInvoiceItemBaseRowTotal,
                          sales_invoice_item.discount_amount as salesInvoiceItemDiscountAmount,
                          sales_invoice_item.row_total as salesInvoiceItemRowTotal,
                          sales_invoice_item.base_discount_amount as salesInvoiceItemBaseDiscountAmount,
                          sales_invoice_item.price_incl_tax as salesInvoiceItemPriceInclTax,
                          sales_invoice_item.base_tax_amount as salesInvoiceItemBaseTaxAmount,
                          sales_invoice_item.base_price_incl_tax as salesInvoiceItemBasePriceInclTax,
                          sales_invoice_item.qty as salesInvoiceItemQty,
                          sales_invoice_item.base_cost as salesInvoiceItemBaseCost,
                          sales_invoice_item.price as salesInvoiceItemPrice,
                          sales_invoice_item.base_row_total_incl_tax as salesInvoiceItemBaseRowToalInclTax,
                          sales_invoice_item.row_total_incl_tax as salesInvoiceItemRowTotalInclTax,
                          sales_invoice_item.product_id as salesInvoiceItemProductId,
                          sales_invoice_item.order_item_id as salesInvoiceItemOrderItemId,
                          sales_invoice_item.additional_data as salesInvoiceItemAdditionalData,
                          sales_invoice_item.description as salesInvoiceItemDescription,
                          sales_invoice_item.sku as salesInvoiceItemSku,
                          sales_invoice_item.name as salesInvoiceItemName,
                          sales_invoice_item.hidden_tax_amount as salesInvoiceItemHiddenTaxAmount,
                          sales_invoice_item.base_hidden_tax_amount as salesInvoiceItemBaseHiddenTaxAmount,
                          sales_invoice_item.base_weee_tax_applied_amount as salesInvoiceItemBaseWeeTaxAppliedAmount,
                          sales_invoice_item.base_weee_tax_applied_row_amnt as salesInvoiceItemBaseWeeTaxAppliedRowAmount,
                          sales_invoice_item.weee_tax_applied_amount as salesInvoiceItemWeeTaxAppliedAmount,
                          sales_invoice_item.weee_tax_applied_row_amount as salesInvoiceItemWeeTaxAppliedRowAmount,
                          sales_invoice_item.weee_tax_applied as salesInvoiceItemWeeTaxApplied,
                          sales_invoice_item.weee_tax_disposition as salesInvoiceItemWeeTaxDisposition,
                          sales_invoice_item.weee_tax_row_disposition as salesInvoiceItemWeeTaxRowDisposition,
                          sales_invoice_item.base_weee_tax_disposition as salesInvoiceItemBaseWeeTaxDisposition,
                          sales_invoice_item.base_weee_tax_row_disposition as salesInvoiceItemBaseWeeTaxRowDisposition,
                          
                          sales_invoice_comment.entity_id as salesInvoiceCommentEntityId,
                          sales_invoice_comment.parent_id as salesInvoiceCommentParentId,
                          sales_invoice_comment.is_customer_notified as salesInvoiceIsCustomerNotified,
                          sales_invoice_comment.is_visible_on_front as salesInvoiceIsVisibleOnFront,
                          sales_invoice_comment.comment	as salesInvoiceCommentComment,
                          sales_invoice_comment.created_at as salesInvoiceCommentCreatedAt,
                          
                          sales_invoice_grid.order_increment_id as salesInvoiceGridOrderIncrementId,
                          sales_invoice_grid.order_created_at as salesInvoiceGridOrderCreatedAt,
                          sales_invoice_grid.billing_name as salesInvoiceGridBillingName
                          
                        
                          FROM mage_sales_flat_invoice as sales_invoice
                          LEFT JOIN mage_sales_flat_invoice_item as sales_invoice_item ON sales_invoice.entity_id = sales_invoice_item.parent_id
                          LEFT JOIN mage_sales_flat_invoice_comment as sales_invoice_comment ON sales_invoice.entity_id = sales_invoice_comment.parent_id
                          LEFT JOIN mage_sales_flat_invoice_grid as sales_invoice_grid ON sales_invoice.order_id = sales_invoice_grid.order_id
                          WHERE sales_invoice.order_id = $salesOrderEntityId
                      ";
               //echo $salesInvoiceDataSql; 
               $salesInvoiceDataDetails = $this->sourceDBconn->query($salesInvoiceDataSql);
               while ($salesInvoiceDataRow = mysqli_fetch_array($salesInvoiceDataDetails)){
                   //echo "<pre>"; print_r($salesInvoiceDataRow); 
                   if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']])) {
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['entity_id'] = $salesInvoiceDataRow['salesInvoiceEntityId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['store_id'] = $salesInvoiceDataRow['salesInvoiceStoreId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_grand_total'] = $salesInvoiceDataRow['salesInvoiceBaseGrandTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['shipping_tax_amount'] = $salesInvoiceDataRow['salesInvoiceShippingTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['tax_amount'] = $salesInvoiceDataRow['salesInvoiceTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_tax_amount'] = $salesInvoiceDataRow['salesInvoiceBaseTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['store_to_order_rate'] = $salesInvoiceDataRow['salesInvoiceStoreToOrderRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_shipping_tax_amount'] = $salesInvoiceDataRow['salesInvoiceBaseShippingTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_discount_amount'] = $salesInvoiceDataRow['salesInvoiceBaseDiscountAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_to_order_rate'] = $salesInvoiceDataRow['salesInvoiceBaseToOrderRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['grand_total'] = $salesInvoiceDataRow['salesInvoiceGradTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['shipping_amount'] = $salesInvoiceDataRow['salesInvoiceShippingAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['subtotal_incl_tax'] = $salesInvoiceDataRow['salesInvoiceSubtotalInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_subtotal_incl_tax'] = $salesInvoiceDataRow['salesInvoiceBaseTotalInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['store_to_base_rate'] = $salesInvoiceDataRow['salesInvoiceStoreToBaseRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_shipping_amount'] = $salesInvoiceDataRow['salesInvoiceBaseShippingAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['total_qty'] = $salesInvoiceDataRow['salesInvoiceTotalQty'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_to_global_rate'] = $salesInvoiceDataRow['salesInvoiceBaseToGlobalRate'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['subtotal'] = $salesInvoiceDataRow['salesInvoiceSubtotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_subtotal'] = $salesInvoiceDataRow['salesInvoiceBaseSubTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['discount_amount'] = $salesInvoiceDataRow['salesInvoiceDiscountAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['billing_address_id'] = $salesInvoiceDataRow['salesInvoiceBillingAddressId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['is_used_for_refund'] = $salesInvoiceDataRow['salesInvoiceIsUsedForRedund'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['order_id'] = $salesInvoiceDataRow['salesInvoiceOrderId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['email_sent'] = $salesInvoiceDataRow['salesInvoiceEmailSent'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['can_void_flag'] = $salesInvoiceDataRow['salesInvoiceCanVoidFlag'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['state'] = $salesInvoiceDataRow['salesInvoiceState'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['shipping_address_id'] = $salesInvoiceDataRow['salesInvoiceShippingAddressId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['store_currency_code'] = $salesInvoiceDataRow['salesInvoiceStoreCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['transaction_id'] = $salesInvoiceDataRow['salesInvoiceTransactionId'];
                       //$this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['store_currency_code'] = $salesInvoiceDataRow['salesInvoiceOrderCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['order_currency_code'] = $salesInvoiceDataRow['salesInvoiceOrderCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_currency_code'] = $salesInvoiceDataRow['salesInvoiceBaseCurrencyCode'];
                       //$this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['store_currency_code'] = $salesInvoiceDataRow['salesInvoiceIncrementId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['global_currency_code'] = $salesInvoiceDataRow['salesInvoiceGlobalCurrecnyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['increment_id'] = $salesInvoiceDataRow['salesInvoiceIncrementId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['created_at'] = $salesInvoiceDataRow['salesInvoiceCreatedAt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['updated_at'] = $salesInvoiceDataRow['salesInvoiceUpdatedAt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['hidden_tax_amount'] = $salesInvoiceDataRow['salesInvoiceHiddenTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_hidden_tax_amount'] = $salesInvoiceDataRow['salesInvoiceBaseHiddenTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['shipping_hidden_tax_amount'] = $salesInvoiceDataRow['salesInvoiceShippingHiddenTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_shipping_hidden_tax_amnt'] = $salesInvoiceDataRow['salesInvoiceBaseShippingHiddenTaxAmnt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['shipping_incl_tax'] = $salesInvoiceDataRow['salesInvoiceShippingInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_shipping_incl_tax'] = $salesInvoiceDataRow['salesInvoiceBaseShippingInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_total_refunded'] = $salesInvoiceDataRow['salesInvoiceBaseTotalRefunded'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice'][$salesInvoiceDataRow['salesInvoiceEntityId']]['discount_description'] = $salesInvoiceDataRow['salesInvoiceDiscountDescription'];

                       if($salesInvoiceDataRow['salesInvoiceEntityId']) {
                           $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_fooman'][$salesInvoiceDataRow['salesInvoiceEntityId']]['fooman_surcharge_amount'] = $salesInvoiceDataRow['salesInvoiceFoomanSurchargeAmount'];
                           $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_fooman'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_fooman_surcharge_tax_amount'] = $salesInvoiceDataRow['salesInvoiceBaseFoomanSurchargeTaxAmount'];
                           $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_fooman'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_fooman_surcharge_amount'] = $salesInvoiceDataRow['salesInvoiceBaseFoomanSurchargeAmount'];
                           $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_fooman'][$salesInvoiceDataRow['salesInvoiceEntityId']]['fooman_surcharge_tax_amount'] = $salesInvoiceDataRow['salesInvoiceFoomanSurchargeTaxAmount'];
                           $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_fooman'][$salesInvoiceDataRow['salesInvoiceEntityId']]['created_at'] = $salesInvoiceDataRow['salesInvoiceCreatedAt'];
                           $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_fooman'][$salesInvoiceDataRow['salesInvoiceEntityId']]['updated_at'] = $salesInvoiceDataRow['salesInvoiceUpdatedAt'];
                       }
                   }

                   if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']])){
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['entity_id'] = $salesInvoiceDataRow['salesInvoiceEntityId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['store_id'] = $salesInvoiceDataRow['salesInvoiceStoreId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_grand_total'] = $salesInvoiceDataRow['salesInvoiceBaseGrandTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['grand_total'] = $salesInvoiceDataRow['salesInvoiceGradTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['order_id'] = $salesInvoiceDataRow['salesInvoiceOrderId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['state'] = $salesInvoiceDataRow['salesInvoiceState'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['store_currency_code'] = $salesInvoiceDataRow['salesInvoiceStoreCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['order_currency_code'] = $salesInvoiceDataRow['salesInvoiceOrderCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['base_currency_code'] = $salesInvoiceDataRow['salesInvoiceBaseCurrencyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['global_currency_code'] = $salesInvoiceDataRow['salesInvoiceGlobalCurrecnyCode'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['increment_id'] = $salesInvoiceDataRow['salesInvoiceIncrementId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['order_increment_id'] = $salesInvoiceDataRow['salesInvoiceGridOrderIncrementId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['created_at'] = $salesInvoiceDataRow['salesInvoiceCreatedAt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['updated_at'] = $salesInvoiceDataRow['salesInvoiceUpdatedAt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['order_created_at'] = $salesInvoiceDataRow['salesInvoiceGridOrderCreatedAt'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_grid'][$salesInvoiceDataRow['salesInvoiceEntityId']]['billing_name'] = $salesInvoiceDataRow['salesInvoiceGridBillingName'];
                   }
                   if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceEntityId']][$salesInvoiceDataRow['salesInvoiceItemEntityId']]) && $salesInvoiceDataRow['salesInvoiceItemEntityId']){
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['entity_id'] = $salesInvoiceDataRow['salesInvoiceItemEntityId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['parent_id'] = $salesInvoiceDataRow['salesInvoiceItemParentId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_price'] = $salesInvoiceDataRow['salesInvoiceItemBasePrice'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['tax_amount'] = $salesInvoiceDataRow['salesInvoiceItemTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_row_total'] = $salesInvoiceDataRow['salesInvoiceItemBaseRowTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['discount_amount'] = $salesInvoiceDataRow['salesInvoiceItemDiscountAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['row_total'] = $salesInvoiceDataRow['salesInvoiceItemRowTotal'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_discount_amount'] = $salesInvoiceDataRow['salesInvoiceItemBaseDiscountAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['price_incl_tax'] = $salesInvoiceDataRow['salesInvoiceItemPriceInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_tax_amount'] = $salesInvoiceDataRow['salesInvoiceItemBaseTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_price_incl_tax'] = $salesInvoiceDataRow['salesInvoiceItemBasePriceInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['qty'] = $salesInvoiceDataRow['salesInvoiceItemQty'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_cost'] = $salesInvoiceDataRow['salesInvoiceItemBaseCost'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['price'] = $salesInvoiceDataRow['salesInvoiceItemPrice'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_row_total_incl_tax'] = $salesInvoiceDataRow['salesInvoiceItemBaseRowToalInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['row_total_incl_tax'] = $salesInvoiceDataRow['salesInvoiceItemRowTotalInclTax'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['product_id'] = $salesInvoiceDataRow['salesInvoiceItemProductId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['order_item_id'] = $salesInvoiceDataRow['salesInvoiceItemOrderItemId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['additional_data'] = $salesInvoiceDataRow['salesInvoiceItemAdditionalData'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['description'] = $salesInvoiceDataRow['salesInvoiceItemDescription'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['sku'] = $salesInvoiceDataRow['salesInvoiceItemSku'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['name'] = $salesInvoiceDataRow['salesInvoiceItemName'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['hidden_tax_amount'] = $salesInvoiceDataRow['salesInvoiceItemHiddenTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_hidden_tax_amount'] = $salesInvoiceDataRow['salesInvoiceItemBaseHiddenTaxAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_weee_tax_applied_amount'] = $salesInvoiceDataRow['salesInvoiceItemBaseWeeTaxAppliedAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_weee_tax_applied_row_amnt'] = $salesInvoiceDataRow['salesInvoiceItemBaseWeeTaxAppliedRowAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['weee_tax_applied_amount'] = $salesInvoiceDataRow['salesInvoiceItemWeeTaxAppliedAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['weee_tax_applied_row_amount'] = $salesInvoiceDataRow['salesInvoiceItemWeeTaxAppliedRowAmount'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['weee_tax_applied'] = json_encode(unserialize($salesInvoiceDataRow['salesInvoiceItemWeeTaxApplied']));
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['weee_tax_disposition'] = $salesInvoiceDataRow['salesInvoiceItemWeeTaxDisposition'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['weee_tax_row_disposition'] = $salesInvoiceDataRow['salesInvoiceItemWeeTaxRowDisposition'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_weee_tax_disposition'] = $salesInvoiceDataRow['salesInvoiceItemBaseWeeTaxDisposition'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_item'][$salesInvoiceDataRow['salesInvoiceItemEntityId']]['base_weee_tax_row_disposition'] = $salesInvoiceDataRow['salesInvoiceItemBaseWeeTaxRowDisposition'];
                   }
                   if(!isset($this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_comment'][$salesInvoiceDataRow['salesInvoiceEntityId']][$salesInvoiceDataRow['salesInvoiceCommentEntityId']]) && $salesInvoiceDataRow['salesInvoiceCommentEntityId']){
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_comment'][$salesInvoiceDataRow['salesInvoiceEntityId']][$salesInvoiceDataRow['salesInvoiceCommentEntityId']]['entity_id'] = $salesInvoiceDataRow['salesInvoiceCommentEntityId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_comment'][$salesInvoiceDataRow['salesInvoiceEntityId']][$salesInvoiceDataRow['salesInvoiceCommentEntityId']]['parent_id'] = $salesInvoiceDataRow['salesInvoiceCommentParentId'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_comment'][$salesInvoiceDataRow['salesInvoiceEntityId']][$salesInvoiceDataRow['salesInvoiceCommentEntityId']]['is_customer_notified'] = $salesInvoiceDataRow['salesInvoiceIsCustomerNotified'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_comment'][$salesInvoiceDataRow['salesInvoiceEntityId']][$salesInvoiceDataRow['salesInvoiceCommentEntityId']]['is_visible_on_front'] = $salesInvoiceDataRow['salesInvoiceIsVisibleOnFront'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_comment'][$salesInvoiceDataRow['salesInvoiceEntityId']][$salesInvoiceDataRow['salesInvoiceCommentEntityId']]['comment'] = $salesInvoiceDataRow['salesInvoiceCommentComment'];
                       $this->orderData[$salesOrderEntityId]['sales_flat_order_invoice_comment'][$salesInvoiceDataRow['salesInvoiceEntityId']][$salesInvoiceDataRow['salesInvoiceCommentEntityId']]['created_at'] = $salesInvoiceDataRow['salesInvoiceCommentCreatedAt'];
                   }

               }
               //echo "<pre>"; print_r($this->orderData);
           }catch (\Exception $e){
               $this->createLog($e->getMessage());
           }
       }

       public function getOrderDataDetails($salesFlatOrderRow){
           try {
               $sql = "  select
                         sales_order_item.item_id as orderItemItemId, 
                         sales_order_item.order_id as orderItemOrdeId,
                         sales_order_item.sftpSentDate as orderItemSentDate, 
                         sales_order_item.parent_item_id as orderItemParentItem,
                         sales_order_item.quote_item_id as orderItemQuoteItemId, 
                         sales_order_item.store_id as orderItemStoreId,
                         sales_order_item.created_at as orderItemCreateAt,  
                         sales_order_item.updated_at as orderItemupdatedAt,
                         sales_order_item.product_id as orderItemProductId,  
                         sales_order_item.product_type as orderItemProductType,
                         sales_order_item.product_options as orderItemOptions,  
                         sales_order_item.weight as orderItemWeight,
                         sales_order_item.is_virtual as orderItemIsVirtual,  
                         sales_order_item.sku as orderItemSku,
                         sales_order_item.name as orderItemName,  
                         sales_order_item.serial_code_type as orderItemSerialCodeType,
                         sales_order_item.serial_codes as orderItemSerialCodes,  
                         sales_order_item.serial_code_ids as orderItemSerialCodeIds,
                         sales_order_item.serial_codes_issued as orderItemSerialcodeIssued,  
                         sales_order_item.serial_code_pool as orderItemSerialCodePool,
                         sales_order_item.description as orderItemDesciption,  
                         sales_order_item.applied_rule_ids as orderItemAppliedRuleIds,
                         sales_order_item.additional_data as orderItemAdditionalData, 
                         sales_order_item.free_shipping as orderItemFreeShipping,
                         sales_order_item.is_qty_decimal as orderItemIsQtyDecimal,  
                         sales_order_item.no_discount as orderItemNoDiscount,
                         sales_order_item.qty_backordered as orderItemQtyBackOrdered,  
                         sales_order_item.qty_canceled as orderItemQtyCanceled,
                         sales_order_item.qty_invoiced as orderItemQtyInvoiced,  
                         sales_order_item.qty_ordered as orderItemQtyOrdered,
                         sales_order_item.qty_refunded as orderItemQtyRefunded, 
                         sales_order_item.qty_shipped as orderItemQtyShipped,
                         sales_order_item.base_cost as orderItemBaseCost,  
                         sales_order_item.price as orderItemPrice,
                         sales_order_item.base_price as orderItemBasePrice,  
                         sales_order_item.original_price as orderItemOrginalPrice,
                         sales_order_item.base_original_price as orderItemBaseOriginalPrice,  
                         sales_order_item.tax_percent as orderItemTaxPercent,
                         sales_order_item.xero_rate as orderItemXeroRate, 
                         sales_order_item.tax_amount as orderItemTaxAmount,
                         sales_order_item.base_tax_amount as orderItemBaseTaxAmount,  
                         sales_order_item.tax_invoiced as orderItemTaxInvoiced,
                         sales_order_item.base_tax_invoiced as orderItemBaseTaxInvoiced,  
                         sales_order_item.discount_percent as orderItemDiscountPercent,
                         sales_order_item.discount_amount as orderItemDiscountAmount,  
                         sales_order_item.base_discount_amount as orderItemBaseDiscountAmount,
                         sales_order_item.discount_invoiced as orderItemDiscountInvoiced,  
                         sales_order_item.base_discount_invoiced as orderItemBaseDiscountInvoiced,
                         sales_order_item.amount_refunded as orderItemAmountRefunded,  
                         sales_order_item.base_amount_refunded as orderItemBaseAmountRefunded,
                         sales_order_item.row_total as orderItemRowTotal, 
                         sales_order_item.base_row_total as orderItemBaseRowTotal,
                         sales_order_item.row_invoiced as orderItemRowInvoiced,  
                         sales_order_item.base_row_invoiced as orderItemBaseRowInvoiced,
                         sales_order_item.row_weight as orderItemRowWeight, 
                         sales_order_item.base_tax_before_discount as orderItemBaseTaxBeforeDiscount,
                         sales_order_item.tax_before_discount as orderItemTaxBeforeDiscount,  
                         sales_order_item.ext_order_item_id as orderItemExtOrderItemId,
                         sales_order_item.locked_do_invoice as orderItemLockedDoInvoice, 
                         sales_order_item.locked_do_ship as orderItemLokecDoship,
                         sales_order_item.price_incl_tax as orderItemPriceInclTax, 
                         sales_order_item.base_price_incl_tax as orderItemBasePriceInclTax,
                         sales_order_item.row_total_incl_tax as orderItemRowTotalInclTax,  
                         sales_order_item.base_row_total_incl_tax as orderItemBaseRowToalInclTax,
                         sales_order_item.hidden_tax_amount as orderItemHiddenTaxAmount,  
                         sales_order_item.base_hidden_tax_amount as orderItemBaseHiddenTaxAmount,
                         sales_order_item.hidden_tax_invoiced as orderItemHiddenTaxInvoiced,  
                         sales_order_item.base_hidden_tax_invoiced as orderItemBaseHiddenTaxInvoiced,
                         sales_order_item.hidden_tax_refunded as orderItemHiddenTaxRefunded,  
                         sales_order_item.base_hidden_tax_refunded as orderItemBaseHiddenTaxRefunded,
                         sales_order_item.is_nominal as orderItemIsNominal, 
                         sales_order_item.tax_canceled as orderItemTaxCancled,
                         sales_order_item.hidden_tax_canceled as orderItemHiddenTaxCancled,  
                         sales_order_item.tax_refunded as orderItemTaxRefunded,
                         sales_order_item.gift_message_id as orderItemGiftMessageId,  
                         sales_order_item.gift_message_available as orderItemGiftMessageAvialble,
                         sales_order_item.base_weee_tax_applied_amount as orderItemBaseWeeTaxAppliedAmount,  
                         sales_order_item.base_weee_tax_applied_row_amnt as orderItemBaseWeeTaxApplied,
                         sales_order_item.weee_tax_applied_amount as orderItemWeeTaxAppliedAmount,  
                         sales_order_item.weee_tax_applied_row_amount as orderItemWeeTaxAppliedRowAmount,
                         sales_order_item.weee_tax_applied as orderItemWeeTaxApplied, 
                         sales_order_item.weee_tax_disposition as orderItemWeeTaxDisposition,
                         sales_order_item.weee_tax_row_disposition as orderItemWeeTaxRowDisposition,  
                         sales_order_item.base_weee_tax_disposition as orderItemWeeTaxDisposition,
                         sales_order_item.base_weee_tax_row_disposition as orderItemWeeTaxRowDisposition,  
                         sales_order_item.base_tax_refunded as orderItemBaseTaxRefunded,
                         sales_order_item.discount_refunded as orderItemDiscountRefunded,  
                         sales_order_item.base_discount_refunded as orderItemBaseDiscountRefunded,  
                         sales_order_item.subsidy as orderItemSubsidy,
                         sales_order_item.subsidy_vip as orderItemSunsidyVIP, 
                         sales_order_item.member_profit as orderItemMemberProfit,
                         sales_order_history.entity_id as orderHistotyEntityid, 
                         sales_order_history.parent_id as orderHistotyParentid,
                         sales_order_history.is_customer_notified as orderHistotyIsCustomerNotified, 
                         sales_order_history.is_visible_on_front as orderHistotyIsVisibleOnFront,
                         sales_order_history.comment as orderHistoryComment, 
                         sales_order_history.status as orderHistoryStatus, 
                         sales_order_history.created_at as orderHistoryCreatedAt,
                         sales_order_history.entity_name as orderHistoryEnityName, 
                         
                         sales_order_payment.entity_id as orderPaymentEntityId,
                         sales_order_payment.parent_id as orderPaymentParentId, 
                         sales_order_payment.base_shipping_captured as orderPaymentbaseShippingCaptured,
                         sales_order_payment.shipping_captured as orderPaymentShippingCaptured,
                         sales_order_payment.amount_refunded as orderPaymentAmountRefunded,
                         sales_order_payment.base_amount_paid as orderPaymentBaseAmountPaid,
                         sales_order_payment.amount_canceled as orderPaymentOrderCancled,
                         sales_order_payment.base_amount_authorized as orderPaymentBaseAmountAuthorized,
                         sales_order_payment.base_amount_paid_online as orderPaymentBaseAmountPaidOnline,
                         sales_order_payment.base_amount_refunded_online as orderPaymentBaseAmountRefundedOnline,
                         sales_order_payment.base_shipping_amount as orderPaymentBaseShippingAmount,
                         sales_order_payment.shipping_amount as orderPaymentShippingAmount,
                         sales_order_payment.amount_paid as orderPaymentAmountPaid,
                         sales_order_payment.amount_authorized as orderPaymentAmountAuthorized,
                         sales_order_payment.base_amount_ordered as orderPaymentBaseAmountOrdered, 
                         sales_order_payment.base_shipping_refunded as orderPaymentBaseShippingRefunded,
                         sales_order_payment.shipping_refunded as orderPaymentShipingRefunded, 
                         sales_order_payment.base_amount_refunded as orderPaymentBaseAmountRefunded,
                         sales_order_payment.amount_ordered as orderPaymentAmountOrdered, 
                         sales_order_payment.base_amount_canceled as orderPaymentBaseAmountCanceled,
                         sales_order_payment.quote_payment_id as orderPaymentQuotepayment, 
                         sales_order_payment.additional_data as orderPaymentAdditionalData,
                         sales_order_payment.cc_exp_month as orderPaymentCxExpMonth, 
                         sales_order_payment.cc_ss_start_year as orderPaymentCcSsStartYear,
                         sales_order_payment.echeck_bank_name as orderPaymentEcheckBankName, 
                         sales_order_payment.method as orderPaymentMethod,
                         sales_order_payment.cc_debug_request_body as orderPaymentCcDebugRequestBody, 
                         sales_order_payment.cc_secure_verify as orderPaymentCcSecureVerify,
                         sales_order_payment.protection_eligibility as orderPaymentProtectionEligibiliy, 
                         sales_order_payment.cc_approval as orderPaymentCcApproval,
                         sales_order_payment.cc_last4 as orderPaymentCcLast4, 
                         sales_order_payment.cc_status_description as orderPaymentCcStatusDescription,
                         sales_order_payment.echeck_type as orderPaymentEcheckType, 
                         sales_order_payment.cc_debug_response_serialized as orderPaymentCcDebugResponseSerialized,
                         sales_order_payment.cc_ss_start_month as orderPaymentCcSsStartMonth, 
                         sales_order_payment.echeck_account_type as orderPaymentEcheckAccountType,
                         sales_order_payment.last_trans_id as orderPaymentLastTransId, 
                         sales_order_payment.cc_cid_status as orderPaymentCcCidStatus,
                         sales_order_payment.cc_owner as orderPaymentCcOwner, 
                         sales_order_payment.cc_type as orderPaymentCcType,
                         sales_order_payment.po_number as orderPaymentPoNumber, 
                         sales_order_payment.cc_exp_year as orderPaymentCcExpyear,
                         sales_order_payment.cc_status as orderPaymentCcStatus, 
                         sales_order_payment.echeck_routing_number as orderPaymentEcheckRoutingNumber,
                         sales_order_payment.account_status as orderPaymentAccountStatus, 
                         sales_order_payment.anet_trans_method as orderPaymentAnetTransMethod,
                         sales_order_payment.cc_debug_response_body as orderPaymentCcDebugResponseBody, 
                         sales_order_payment.cc_ss_issue as orderPaymentCcSsIssue,
                         sales_order_payment.echeck_account_name as orderPaymentEcheckAccountName, 
                         sales_order_payment.cc_avs_status as orderPaymentCcAvsStatus,
                         sales_order_payment.cc_number_enc as orderPaymentCcNumberEnc, 
                         sales_order_payment.cc_trans_id as orderPaymentCcTransId,
                         sales_order_payment.paybox_request_number as orderPaymentPayboxRequestNumber, 
                         sales_order_payment.address_status as orderPaymentAddressStatus,
                         sales_order_payment.additional_information as orderPaymentAdditionalInformation, 
                         sales_order_payment.appmerce_response_code as orderPaymentAppmerceResponseCode,
                         sales_order_payment.appmerce_access_code as orderPaymentAppmerceAccessCode, 
                         
                          
                         sales_order_payment_trasc.transaction_id as orderPamenTrancTransactionId,
                         sales_order_payment_trasc.parent_id as orderPamenTrancParentId, 
                         sales_order_payment_trasc.order_id as orderPamenTrancOrderId,
                         sales_order_payment_trasc.payment_id as orderPamenTrancPaymentId, 
                         sales_order_payment_trasc.txn_id as orderPamenTrancTxnId,
                         sales_order_payment_trasc.parent_txn_id as orderPamenTrancParentTxnId, 
                         sales_order_payment_trasc.txn_type as orderPamenTrancTxnType,
                         sales_order_payment_trasc.is_closed as orderPamenTrancIsClosed, 
                         sales_order_payment_trasc.additional_information as orderPamenTrancAdditionalInformation,
                         sales_order_payment_trasc.created_at as orderPamenTrancCreatedAt
                
                 from mage_sales_flat_order_item as sales_order_item
                 LEFT JOIN  mage_sales_flat_order_status_history as sales_order_history ON sales_order_item.order_id = sales_order_history.parent_id
                 LEFT JOIN mage_sales_flat_order_payment as sales_order_payment ON sales_order_item.order_id = sales_order_payment.parent_id
                 LEFT JOIN mage_sales_payment_transaction as sales_order_payment_trasc on  sales_order_item.order_id = sales_order_payment_trasc.order_id
                 where sales_order_item.order_id = $salesFlatOrderRow[entity_id]";

               $salesOrderDataResults = $this->sourceDBconn->query($sql);
               while ($salesOrderDataRow = mysqli_fetch_array($salesOrderDataResults)) {
                   if (!isset($this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]) && $salesOrderDataRow['orderItemItemId']) {
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['item_id'] = $salesOrderDataRow['orderItemItemId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['order_id'] = $salesOrderDataRow['orderItemOrdeId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['sftpSentDate'] = $salesOrderDataRow['orderItemSentDate'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['parent_item_id'] = $salesOrderDataRow['orderItemParentItem'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['quote_item_id'] = $salesOrderDataRow['orderItemQuoteItemId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['store_id'] = $salesOrderDataRow['orderItemStoreId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['created_at'] = $salesOrderDataRow['orderItemCreateAt'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['updated_at'] = $salesOrderDataRow['orderItemupdatedAt'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['product_id'] = $salesOrderDataRow['orderItemProductId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['product_type'] = $salesOrderDataRow['orderItemProductType'];

                       $unseralizedProductOptions = json_encode(unserialize($salesOrderDataRow['orderItemOptions']));

                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['product_options'] = $unseralizedProductOptions; //$salesOrderDataRow['orderItemOptions'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['weight'] = $salesOrderDataRow['orderItemWeight'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['is_virtual'] = $salesOrderDataRow['orderItemIsVirtual'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['sku'] = $salesOrderDataRow['orderItemSku'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['name'] = $salesOrderDataRow['orderItemName'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['description'] = $salesOrderDataRow['orderItemDesciption'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['serial_code_type'] = $salesOrderDataRow['orderItemSerialCodeType'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['serial_codes'] = $salesOrderDataRow['orderItemSerialCodes'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['serial_code_ids'] = $salesOrderDataRow['orderItemSerialCodeIds'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['serial_codes_issued'] = $salesOrderDataRow['orderItemSerialcodeIssued'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['serial_code_pool'] = $salesOrderDataRow['orderItemSerialCodePool'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['applied_rule_ids'] = $salesOrderDataRow['orderItemAppliedRuleIds'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['additional_data'] = $salesOrderDataRow['orderItemAdditionalData'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['free_shipping'] = $salesOrderDataRow['orderItemFreeShipping'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['is_qty_decimal'] = $salesOrderDataRow['orderItemIsQtyDecimal'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['no_discount'] = $salesOrderDataRow['orderItemNoDiscount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['qty_backordered'] = $salesOrderDataRow['orderItemQtyBackOrdered'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['qty_canceled'] = $salesOrderDataRow['orderItemQtyCanceled'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['qty_invoiced'] = $salesOrderDataRow['orderItemQtyInvoiced'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['qty_ordered'] = $salesOrderDataRow['orderItemQtyOrdered'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['qty_refunded'] = $salesOrderDataRow['orderItemQtyRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['qty_shipped'] = $salesOrderDataRow['orderItemQtyShipped'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_cost'] = $salesOrderDataRow['orderItemBaseCost'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['price'] = $salesOrderDataRow['orderItemPrice'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_price'] = $salesOrderDataRow['orderItemBasePrice'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['original_price'] = $salesOrderDataRow['orderItemOrginalPrice'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_original_price'] = $salesOrderDataRow['orderItemBaseOriginalPrice'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['tax_percent'] = $salesOrderDataRow['orderItemTaxPercent'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['xero_rate'] = $salesOrderDataRow['orderItemXeroRate'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['tax_amount'] = $salesOrderDataRow['orderItemTaxAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_tax_amount'] = $salesOrderDataRow['orderItemBaseTaxAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['tax_invoiced'] = $salesOrderDataRow['orderItemTaxInvoiced'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_tax_invoiced'] = $salesOrderDataRow['orderItemBaseTaxInvoiced'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['discount_percent'] = $salesOrderDataRow['orderItemDiscountPercent'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['discount_amount'] = $salesOrderDataRow['orderItemDiscountAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_discount_amount'] = $salesOrderDataRow['orderItemBaseDiscountAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['discount_invoiced'] = $salesOrderDataRow['orderItemDiscountInvoiced'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_discount_invoiced'] = $salesOrderDataRow['orderItemBaseDiscountInvoiced'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['amount_refunded'] = $salesOrderDataRow['orderItemAmountRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_amount_refunded'] = $salesOrderDataRow['orderItemBaseAmountRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['row_total'] = $salesOrderDataRow['orderItemRowTotal'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_row_total'] = $salesOrderDataRow['orderItemBaseRowTotal'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['row_invoiced'] = $salesOrderDataRow['orderItemRowInvoiced'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_row_invoiced'] = $salesOrderDataRow['orderItemBaseRowInvoiced'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['row_weight'] = $salesOrderDataRow['orderItemRowWeight'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_tax_before_discount'] = $salesOrderDataRow['orderItemBaseTaxBeforeDiscount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['tax_before_discount'] = $salesOrderDataRow['orderItemTaxBeforeDiscount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['ext_order_item_id'] = $salesOrderDataRow['orderItemExtOrderItemId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['locked_do_invoice'] = $salesOrderDataRow['orderItemLockedDoInvoice'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['locked_do_ship'] = $salesOrderDataRow['orderItemLokecDoship'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['price_incl_tax'] = $salesOrderDataRow['orderItemPriceInclTax'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_price_incl_tax'] = $salesOrderDataRow['orderItemBasePriceInclTax'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['row_total_incl_tax'] = $salesOrderDataRow['orderItemRowTotalInclTax'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_row_total_incl_tax'] = $salesOrderDataRow['orderItemBaseRowToalInclTax'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['hidden_tax_amount'] = $salesOrderDataRow['orderItemHiddenTaxAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_hidden_tax_amount'] = $salesOrderDataRow['orderItemBaseHiddenTaxAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['hidden_tax_invoiced'] = $salesOrderDataRow['orderItemHiddenTaxInvoiced'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_hidden_tax_invoiced'] = $salesOrderDataRow['orderItemBaseHiddenTaxInvoiced'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['hidden_tax_refunded'] = $salesOrderDataRow['orderItemHiddenTaxRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_hidden_tax_refunded'] = $salesOrderDataRow['orderItemBaseHiddenTaxRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['is_nominal'] = $salesOrderDataRow['orderItemIsNominal'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['tax_canceled'] = $salesOrderDataRow['orderItemTaxCancled'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['hidden_tax_canceled'] = $salesOrderDataRow['orderItemHiddenTaxCancled'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['tax_refunded'] = $salesOrderDataRow['orderItemTaxRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['gift_message_id'] = $salesOrderDataRow['orderItemGiftMessageId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['gift_message_available'] = $salesOrderDataRow['orderItemGiftMessageAvialble'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_weee_tax_applied_amount'] = $salesOrderDataRow['orderItemBaseWeeTaxAppliedAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_weee_tax_applied_row_amnt'] = $salesOrderDataRow['orderItemBaseWeeTaxApplied'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['weee_tax_applied_amount'] = $salesOrderDataRow['orderItemWeeTaxAppliedAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['weee_tax_applied_row_amount'] = $salesOrderDataRow['orderItemWeeTaxAppliedRowAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['weee_tax_applied'] = json_encode(unserialize($salesOrderDataRow['orderItemWeeTaxApplied']));
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['weee_tax_disposition'] = $salesOrderDataRow['orderItemWeeTaxDisposition'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['weee_tax_row_disposition'] = $salesOrderDataRow['orderItemWeeTaxRowDisposition'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_weee_tax_disposition'] = $salesOrderDataRow['orderItemWeeTaxDisposition'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_weee_tax_row_disposition'] = $salesOrderDataRow['orderItemWeeTaxRowDisposition'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_tax_refunded'] = $salesOrderDataRow['orderItemBaseTaxRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['discount_refunded'] = $salesOrderDataRow['orderItemDiscountRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['base_discount_refunded'] = $salesOrderDataRow['orderItemBaseDiscountRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['subsidy'] = $salesOrderDataRow['orderItemSubsidy'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['subsidy_vip'] = $salesOrderDataRow['orderItemSunsidyVIP'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_item'][$salesOrderDataRow['orderItemItemId']]['member_profit'] = $salesOrderDataRow['orderItemMemberProfit'];
                   }
                   if (!isset($this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_status_history'][$salesOrderDataRow['orderItemItemId']][$salesOrderDataRow['orderHistotyEntityid']]) && $salesOrderDataRow['orderHistotyEntityid']) {
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_status_history'][$salesOrderDataRow['orderHistotyEntityid']]['entity_id'] = $salesOrderDataRow['orderHistotyEntityid'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_status_history'][$salesOrderDataRow['orderHistotyEntityid']]['parent_id'] = $salesOrderDataRow['orderHistotyParentid'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_status_history'][$salesOrderDataRow['orderHistotyEntityid']]['is_customer_notified'] = $salesOrderDataRow['orderHistotyIsCustomerNotified'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_status_history'][$salesOrderDataRow['orderHistotyEntityid']]['is_visible_on_front'] = $salesOrderDataRow['orderHistotyIsVisibleOnFront'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_status_history'][$salesOrderDataRow['orderHistotyEntityid']]['comment'] = $salesOrderDataRow['orderHistoryComment'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_status_history'][$salesOrderDataRow['orderHistotyEntityid']]['status'] = $salesOrderDataRow['orderHistoryStatus'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_status_history'][$salesOrderDataRow['orderHistotyEntityid']]['created_at'] = $salesOrderDataRow['orderHistoryCreatedAt'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_status_history'][$salesOrderDataRow['orderHistotyEntityid']]['entity_name'] = $salesOrderDataRow['orderHistoryEnityName'];
                   }

                   if (!isset($this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderItemItemId']][$salesOrderDataRow['orderPaymentEntityId']])) {

                       $paymentMethod = $salesOrderDataRow['orderPaymentMethod'];
                       if($salesOrderDataRow['orderPaymentMethod'] == 'gene_braintree_paypal'){
                           $paymentMethod = 'braintree_paypal';
                           $unseralizedAdditionalInformation = unserialize($salesOrderDataRow['orderPaymentAdditionalInformation']);
                           $unseralizedAdditionalInformation['method_title'] = "PayPal (Braintree)";
                           $unseralizedAdditionalInformation['device_data']['correlation_id'] = $unseralizedAdditionalInformation['authorization_id'];
                           $unseralizedAdditionalInformation['payerEmail'] = $unseralizedAdditionalInformation['paypal_email'];
                           $unseralizedAdditionalInformation['paymentId'] = $unseralizedAdditionalInformation['payment_id'];
                           unset($unseralizedAdditionalInformation['paypal_email']);
                           unset($unseralizedAdditionalInformation['payment_id']);
                           unset($unseralizedAdditionalInformation['authorization_id']);
                           $additionalInformation = json_encode($unseralizedAdditionalInformation);

                       }else if($salesOrderDataRow['orderPaymentMethod'] == 'gene_braintree_creditcard') {
                           $paymentMethod = 'braintree';
                           $unseralizedArray = unserialize($salesOrderDataRow['orderPaymentAdditionalInformation']);
                           $unseralizedArray['method_title'] = "Credit Card (Braintree)";
                           if(isset($unseralizedArray['avsPostalCodeResponseCode'])){
                               $unseralizedArray['cc_number'] = "xxxx-".$salesOrderDataRow['orderPaymentCcLast4'];
                               $unseralizedArray['cc_type'] =  $salesOrderDataRow['orderPaymentCcType'];
                           }
                           $additionalInformation = json_encode($unseralizedArray);
                       }else{
                           $additionalInformation = json_encode(unserialize($salesOrderDataRow['orderPaymentAdditionalInformation']));
                       }
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order']['payment_method'] = $salesOrderDataRow['orderPaymentMethod'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['entity_id'] = $salesOrderDataRow['orderPaymentEntityId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['parent_id'] = $salesOrderDataRow['orderPaymentParentId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['base_shipping_captured'] = $salesOrderDataRow['orderPaymentbaseShippingCaptured'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['shipping_captured'] = $salesOrderDataRow['orderPaymentShippingCaptured'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['amount_refunded'] = $salesOrderDataRow['orderPaymentAmountRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['base_amount_paid'] = $salesOrderDataRow['orderPaymentBaseAmountPaid'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['amount_canceled'] = $salesOrderDataRow['orderPaymentOrderCancled'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['base_amount_authorized'] = $salesOrderDataRow['orderPaymentBaseAmountAuthorized'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['base_amount_paid_online'] = $salesOrderDataRow['orderPaymentBaseAmountPaidOnline'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['base_amount_refunded_online'] = $salesOrderDataRow['orderPaymentBaseAmountRefundedOnline'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['base_shipping_amount'] = $salesOrderDataRow['orderPaymentBaseShippingAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['shipping_amount'] = $salesOrderDataRow['orderPaymentShippingAmount'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['amount_paid'] = $salesOrderDataRow['orderPaymentAmountPaid'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['amount_authorized'] = $salesOrderDataRow['orderPaymentAmountAuthorized'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['base_amount_ordered'] = $salesOrderDataRow['orderPaymentBaseAmountOrdered'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['base_shipping_refunded'] = $salesOrderDataRow['orderPaymentBaseShippingRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['shipping_refunded'] = $salesOrderDataRow['orderPaymentShipingRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['base_amount_refunded'] = $salesOrderDataRow['orderPaymentBaseAmountRefunded'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['amount_ordered'] = $salesOrderDataRow['orderPaymentAmountOrdered'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['base_amount_canceled'] = $salesOrderDataRow['orderPaymentBaseAmountCanceled'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['quote_payment_id'] = $salesOrderDataRow['orderPaymentQuotepayment'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['additional_data'] = $salesOrderDataRow['orderPaymentAdditionalData'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_exp_month'] = $salesOrderDataRow['orderPaymentCxExpMonth'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_ss_start_year'] = $salesOrderDataRow['orderPaymentCcSsStartYear'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['echeck_bank_name'] = $salesOrderDataRow['orderPaymentEcheckBankName'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['method'] = $paymentMethod;
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_debug_request_body'] = $salesOrderDataRow['orderPaymentCcDebugRequestBody'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_secure_verify'] = $salesOrderDataRow['orderPaymentCcSecureVerify'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['protection_eligibility'] = $salesOrderDataRow['orderPaymentProtectionEligibiliy'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_approval'] = $salesOrderDataRow['orderPaymentCcApproval'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_last4'] = $salesOrderDataRow['orderPaymentCcLast4'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_status_description'] = $salesOrderDataRow['orderPaymentCcStatusDescription'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['echeck_type'] = $salesOrderDataRow['orderPaymentEcheckType'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_debug_response_serialized'] = $salesOrderDataRow['orderPaymentCcDebugResponseSerialized'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_ss_start_month'] = $salesOrderDataRow['orderPaymentCcSsStartMonth'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['last_trans_id'] = $salesOrderDataRow['orderPaymentEcheckAccountType'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['echeck_account_type'] = $salesOrderDataRow['orderPaymentLastTransId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_cid_status'] = $salesOrderDataRow['orderPaymentCcCidStatus'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_owner'] = $salesOrderDataRow['orderPaymentCcOwner'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_type'] = $salesOrderDataRow['orderPaymentCcType'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['po_number'] = $salesOrderDataRow['orderPaymentPoNumber'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_exp_year'] = $salesOrderDataRow['orderPaymentCcExpyear'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_status'] = $salesOrderDataRow['orderPaymentCcStatus'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['echeck_routing_number'] = $salesOrderDataRow['orderPaymentEcheckRoutingNumber'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['account_status'] = $salesOrderDataRow['orderPaymentAccountStatus'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['anet_trans_method'] = $salesOrderDataRow['orderPaymentAnetTransMethod'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_debug_response_body'] = $salesOrderDataRow['orderPaymentCcDebugResponseBody'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_ss_issue'] = $salesOrderDataRow['orderPaymentCcSsIssue'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['echeck_account_name'] = $salesOrderDataRow['orderPaymentEcheckAccountName'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_avs_status'] = $salesOrderDataRow['orderPaymentCcAvsStatus'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_number_enc'] = $salesOrderDataRow['orderPaymentCcNumberEnc'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['cc_trans_id'] = $salesOrderDataRow['orderPaymentCcTransId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['paybox_request_number'] = $salesOrderDataRow['orderPaymentPayboxRequestNumber'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['address_status'] = $salesOrderDataRow['orderPaymentAddressStatus'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['additional_information'] = addslashes($additionalInformation);
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['appmerce_response_code'] = $salesOrderDataRow['orderPaymentAppmerceResponseCode'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_flat_order_payment'][$salesOrderDataRow['orderPaymentEntityId']]['appmerce_access_code'] = $salesOrderDataRow['orderPaymentAppmerceAccessCode'];
                   }
                   if (!isset($this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderItemItemId']][$salesOrderDataRow['orderPamenTrancTransactionId']]) && $salesOrderDataRow['orderPamenTrancTransactionId']) {
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderPamenTrancTransactionId']]['transaction_id'] = $salesOrderDataRow['orderPamenTrancTransactionId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderPamenTrancTransactionId']]['parent_id'] = $salesOrderDataRow['orderPamenTrancParentId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderPamenTrancTransactionId']]['order_id'] = $salesOrderDataRow['orderPamenTrancOrderId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderPamenTrancTransactionId']]['payment_id'] = $salesOrderDataRow['orderPamenTrancPaymentId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderPamenTrancTransactionId']]['txn_id'] = $salesOrderDataRow['orderPamenTrancTxnId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderPamenTrancTransactionId']]['parent_txn_id'] = $salesOrderDataRow['orderPamenTrancParentTxnId'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderPamenTrancTransactionId']]['txn_type'] = $salesOrderDataRow['orderPamenTrancTxnType'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderPamenTrancTransactionId']]['is_closed'] = $salesOrderDataRow['orderPamenTrancIsClosed'];
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderPamenTrancTransactionId']]['additional_information'] = addslashes(json_encode(unserialize($salesOrderDataRow['orderPamenTrancAdditionalInformation'])));
                       $this->orderData[$salesFlatOrderRow['entity_id']]['sales_payment_transaction'][$salesOrderDataRow['orderPamenTrancTransactionId']]['created_at'] = $salesOrderDataRow['orderPamenTrancCreatedAt'];
                   }
               }
           }catch (\Exception $e){
               $this->createLog($e->getMessage());
           }
       }
   }

