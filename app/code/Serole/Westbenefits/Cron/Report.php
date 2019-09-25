<?php

   namespace Serole\Westbenefits\Cron;

   use Magento\Store\Model\Store;

   class Report{

       protected $helpData;

       protected $sqlConnection;

       protected $store;

       public function __construct(\Serole\HelpData\Helper\Data $helpData,
                                   \Magento\Store\Model\Store $store,
                                   \Magento\Framework\App\ResourceConnection $sqlConnection
                                  ){
           $this->helpData = $helpData;
           $this->sqlConnection = $sqlConnection;
           $this->store = $store;
       }

       public function execute(){
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Westbenefits-cronjob-execute.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           try {
               $report_dir = $this->helpData->getMediaBaseDir() . "/reports/";
               $qry_from_date = date('Y-m-d', strtotime("-1 week"));
               //$qry_from_date = date('Y-m-d',strtotime("-1 year"));
               $qry_to_date = date('Y-m-d', time());

               $to_emails = array('linda.cheong@wanews.com.au', 'operations@neatideas.com.au');
               $to_names = array('Linda', 'NeatIdeas', 'Blueraspberry');

               $this->abandonedCartReport($report_dir, $qry_from_date, $qry_to_date, $to_emails, $to_names);
               $this->inactiveUserReport($report_dir, $qry_from_date, $qry_to_date, $to_emails, $to_names);
           }catch (\Exception $e){
               $logger->info($e->getMessage());
           }
       }

       public function abandonedCartReport($report_dir, $qry_from_date, $qry_to_date, $to_emails, $to_names){

           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Westbenefits-cronjob-abandonedCartReport.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           try {

               $storeId = $this->getStoreId('westbenefits_en');

               $sql = "SELECT
				quote.entity_id 	AS cart_id,
				CONCAT_WS(' ', cust_fname.value, cust_lname.value) AS customer_name,
				cust_email.email 	AS customer_email,
				itm.qty 			AS qty,
				itm.sku 			AS sku,
				itm.name 			AS product,
				quote.created_at 	AS creation_date
				
				FROM sales_quote AS quote
				
				INNER JOIN customer_entity AS cust_email 
					ON cust_email.entity_id = quote.customer_id
					
				INNER JOIN customer_entity_varchar AS cust_fname
					ON cust_fname.entity_id = quote.customer_id 
					AND cust_fname.attribute_id = 5
					
				INNER JOIN customer_entity_varchar AS cust_lname
					ON cust_lname.entity_id = quote.customer_id 
					AND cust_lname.attribute_id = 7
					
				JOIN sales_quote_item itm on 
					itm.quote_id = quote.entity_id

				WHERE (quote.items_count != '0') 
					AND (quote.is_active = '1')
					AND (quote.store_id = $storeId)
					AND (date(quote.created_at) >= '$qry_from_date')
					AND (date(quote.created_at) <  '$qry_to_date')
				";

               $columnHeaders = ['Cart Id',
                   'Customer Name',
                   'Customer Email',
                   'Qty',
                   'SKU',
                   'Product Name',
                   'Created At'
               ];

               $time = time();
               $filename = 'Abandoned_Carts_' . substr($qry_from_date, 0, 10) . ' ' . substr($qry_to_date, 0, 10) . ' ' . $time . '.csv';
               $attachFileName = substr($qry_from_date, 0, 10) . ' ' . substr($qry_to_date, 0, 10) . ' ' . $time . '.csv';
               $filepath = $report_dir . $filename;

               $this->_generateCSV($columnHeaders, $sql, $filepath);

               $toDetails['name'] = $to_emails;
               $toDetails['email'] = $to_emails;

               $fromDetail['name'] = $this->helpData->getConfigValues('trans_email/ident_sales/name');
               $fromDetail['email'] = $this->helpData->getConfigValues('trans_email/ident_sales/email');

               $templateParams = array();

               $this->helpData->sendCsvAttachentMail(  $templateId = 7,
                                                       $fromDetail,
                                                       $toDetails,
                                                       $storeId = '',
                                                       $templateParams,
                                                       $filePath = $filepath,
                                                       $requestMethodName = "Westbenefits-CronJob-abandonedCartReport",
                                                       $fileType = 'application/csv',
                                                       $attachFileName
                                                   );
               //$this->_sendEmail($filepath, $filename, $to_emails, $to_names, 'Inactive Users');
           }catch (\Exception $e){
               $logger->info($e->getMessage());
           }
       }

       protected function _generateCSV($columnHeaders, $sql, $filepath){
           $fp = fopen($filepath, 'w');
           fputcsv($fp, $columnHeaders);
           $conn = $this->sqlConnection->getConnection();
           $rows = $conn->query($sql);
           while($row = $rows->fetch()) {
               fputcsv($fp, $row);
           }
           fclose($fp);
       }

       public function inactiveUserReport($report_dir, $qry_from_date, $qry_to_date, $to_emails, $to_names){

           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Westbenefits-cronjob-inactiveUserReport.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           try {

               $storeId = $this->getStoreId('westbenefits_en');

               $sql = "SELECT
				CONCAT_WS(' ', cust_fname.value, cust_lname.value) AS customer_name,
				cust.email 			AS customer_email,
				cust.created_at 	AS creation_date
				
				FROM  customer_entity AS cust
					
				INNER JOIN customer_entity_varchar AS cust_fname
					ON cust_fname.entity_id = cust.entity_id 
					AND cust_fname.attribute_id = 5
					
				INNER JOIN customer_entity_varchar AS cust_lname
					ON cust_lname.entity_id = cust.entity_id 
					AND cust_lname.attribute_id = 7
					
				WHERE (cust.is_active = '1')
					AND (cust.store_id = $storeId)
					AND (date(cust.created_at) >= '$qry_from_date')
					AND (date(cust.created_at) <  '$qry_to_date')
					AND NOT EXISTS (
						SELECT * FROM sales_quote qte
						WHERE qte.customer_id = cust.entity_id
					)

				";

               $columnHeaders = ['Customer Name',
                   'Customer Email',
                   'Created At'
               ];

               $time = time();
               $filename = 'Inactive_Users_' . substr($qry_from_date, 0, 10) . ' ' . substr($qry_to_date, 0, 10) . ' ' . $time . '.csv';
               $filepath = $report_dir . $filename;

               $this->_generateCSV($columnHeaders, $sql, $filepath);

               $toDetails['name'] = $to_emails;
               $toDetails['email'] = $to_emails;

               $fromDetail['name'] = $this->helpData->getConfigValues('trans_email/ident_sales/name');
               $fromDetail['email'] = $this->helpData->getConfigValues('trans_email/ident_sales/email');

               $templateParams = array();

               $this->helpData->sendCsvAttachentMail($templateId = 7,
                   $fromDetail,
                   $toDetails,
                   $storeId = '',
                   $templateParams,
                   $filePath = $filepath,
                   $requestMethodName = "Westbenefits-CronJob-abandonedCartReport"
               );

               //$this->_sendEmail($filepath, $filename, $to_emails, $to_names, 'Inactive Users');

           }catch (\Exception $e){
               $logger->info($e->getMessage());
           }
       }

       public function getStoreId($code){
           $storeData = $this->store->load($code);
           return $storeData->getStoreId();
       }
   }