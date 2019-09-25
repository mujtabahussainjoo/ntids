<?php

namespace Serole\OvernightUpload\Cron;

class OvernightUpload
{

    protected $helperData;

    protected $objectManager;

    protected $partnerObj;

    protected $eav;

    protected $store;

    protected $varienIoSftp;

    protected $varienIoFile;

    protected $varienIoFtp;

    protected $connection;

    protected $productObj;

    protected $orderObj;

    protected $eavCollection;

    protected $providerObj;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager,
                                \Serole\OvernightUpload\Model\Grid $partnerObj,
                                \Serole\OvernightUpload\Model\Providergrid $providerObj,
                                \Magento\Eav\Model\Config $eav,
                                \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $eavCollection,
                                \Magento\Store\Model\Store $store,
                                \Serole\Generateordercsv\Filesystem\Io\Sftp $varienIoSftp,
                                \Magento\Framework\Filesystem\Io\Ftp $varienIoFtp,
                                \Magento\Framework\Filesystem\Io\File $varienIoFile,
                                \Magento\Framework\App\ResourceConnection $connection,
                                \Magento\Catalog\Model\Product $productObj,
                                \Magento\Sales\Model\Order $orderObj,
                                \Serole\HelpData\Helper\Data $helperData)
    {
        $this->helperData = $helperData;
        $this->objectManager = $objectmanager;
        $this->partnerObj = $partnerObj;
        $this->eav = $eav;
        $this->eavCollection = $eavCollection;
        $this->store = $store;
        $this->varienIoSftp = $varienIoSftp;
        $this->varienIoFtp = $varienIoFtp;
        $this->varienIoFile = $varienIoFile;
        $this->connection = $connection;
        $this->productObj = $productObj;
        $this->orderObj = $orderObj;
        $this->providerObj = $providerObj;
        $this->reportDir = $this->helperData->getMediaBaseDir()."/reports/overnightupload/";
    }

    public function execute()
    {
        //$sqlDate = date('Y-m-d', strtotime('+0 day'));
        $sqlDate = '2018-10-01';
        $this->processDate($sqlDate);
    }

    public function processDate($sqlDate){

        $storeId = $this->getStoreId('rac_en');

        $outDir = $this->reportDir.'/rac/out';
        $sentDir = $this->reportDir.'/rac/sent';

        if (!file_exists($outDir)) {
            mkdir($outDir, 0777, true);
            chmod($outDir, 0777);
        }
        if (!is_writable($outDir)) {
            chmod($outDir, 0777);
        }


        if (!file_exists($sentDir)) {
            mkdir($sentDir, 0777, true);
            chmod($sentDir, 0777);
        }
        if (!is_writable($sentDir)) {
            chmod($sentDir, 0777);
        }
        $this::writeData_RAC($sqlDate);
        $this::writeDataToCSVs($storeId, $sqlDate, $outDir);
        $this::sendFiles($storeId, $outDir, $sentDir, $sqlDate);  /*Done*/

    }

    public function sendFiles($storeId, $outDir, $sentDir, $sqlDate) {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/overnightupload-cronjob-sendFiles.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);


        $collection  = $this->partnerObj->getCollection();
        $collection->addFieldToFilter('store_id',$storeId);

        foreach ($collection as $partnerInfo){
            if($partnerInfo->getStatus() == 0){
                continue;
            }
            $partnerCode = $partnerInfo->getPartnercode();
            $files = glob($outDir.'/'.$partnerCode.'*.csv');
            if (count($files) == 0){
                $logger->info($partnerCode."--No files to process");
                $logger->info($partnerCode."--Writing empty file");
                $this::writeEmptyFile($partnerCode, $outDir, $sqlDate);
                $files = glob($outDir.'/'.$partnerCode.'*.csv');
            }

            $server = $partnerInfo->getServerName();
            $user = $partnerInfo->getServerUsername();
            $pass = $partnerInfo->getServerPassword();

            $logger->info($partnerCode.": Connect '.$user.'@'.$server");

            try{

                /*FTP Connection*/

                $sftp = $this->varienIoFtp;
                $open = $sftp->open(
                    array(  'host' => 'vaibhavreddymarriagebureau.com',
                        'user' => 'ramesh@vaibhavreddymarriagebureau.com',
                        'password' => 'Vvh194!)',
                        'port' => '21',
                        'ssl' => true,
                        'passive' => true
                    )
                );



                /* SFTP connection */

                /*
                  $sftp = $this->varienIoSftp;
                  $open =  $sftp->open(
                                           array('host' => $server,
                                                // 'user' => $user,
                                                 'username' => $user,
                                                 'password' => $pass,
                                                 'port' => '22',
                                                 'ssl' => true,
                                                 'passive' => true
                                             )
                                         );
                */

                $sftp->cd('in');
                foreach ($files as $filename){
                    $filePath = $filename;
                    $filename = basename($filename);
                    $logger->info($partnerCode.': Send '.$filePath.' ('.filesize($filePath).' bytes)');
                    $data = file_get_contents($filePath);
                    $writeResult = $sftp->write($filename, $data);

                    // Move the file to the SENT folder if it went ok
                    if ($writeResult == 1){
                        $logger->info($partnerCode.': Write Success '.$filename );
                        $sentFilePath = $sentDir.'/'.$filename;
                        rename($filePath, $sentFilePath);
                        $logger->info($partnerCode.': Moved to SENT '.$filename );
                    }else{
                        $logger->info($partnerCode.': Write Failed '.$filename );
                    }
                }

                $logger->info($partnerCode.': Disconnect '.$user.'@'.$server);

                $sftp->close();

            }catch (\Exception $e){
                $logger->info($partnerCode.': SFTP Error for '.$user.'@'.$server.': '.$e->getMessage());
            }
        }
    }

    public function clearData($sqlDate, $storeId) {
        $conn = $this->connection->getConnection();
        $qry = "DELETE FROM overnightupload_data
				WHERE date(time_stamp) = '".$sqlDate."'
				AND store_id=".$storeId;
        $conn->query($qry);
    }

    public function writeData($values){
        $conn  = $this->connection->getConnection();
        // Perform the insert, use ON DUPLICATE to prevent duplicates being written
        $writeQry = "INSERT INTO overnightupload_data ( store_id,
                                                        transaction_header_id,
                                                        transaction_detail_id,
                                                        time_stamp,
                                                        member_reference_number,
                                                        member_reference_type,
                                                        unique_item_id,
                                                        unique_item_type,							
                                                        item_description,
                                                        quantity,
                                                        item_price,
                                                        discount,
                                                        source_location,
                                                        sub_category,
                                                        category,
                                                        short_description,
                                                        filler1,
                                                        filler2,
                                                        filler3,
                                                        filler4,
                                                        partner_code,
                                                        autoclub
                                                      )
                       VALUES ".$values." ON DUPLICATE KEY UPDATE store_id=store_id";
        $conn->query($writeQry);
    }

    public function writeDataToCSVs($storeId, $sqlDate, $outDir) {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/overnightupload-cronjob-writeDataToCSVs.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $dateChunk 	= explode("-",$sqlDate);
        $fileDate = $dateChunk[0].$dateChunk[1].$dateChunk[2];

        // Now output the csv files
        $prevPartnerCode = '';

        $conn = $this->connection->getConnection();
        $qry = "SELECT 
					Transaction_Header_ID,
					Transaction_Detail_ID,
					date_format(time_stamp,'%Y%m%d%H%i%s'),
					member_reference_number,
					member_reference_type,
					unique_item_id,
					unique_item_type,
					item_description,
					quantity,
					item_price,
					discount,
					source_location,
					sub_category,
					category,
					short_description,
					filler1,
					filler2,
					filler3,
					filler4,
					partner_code,
					autoclub				 
				FROM overnightupload_data
				WHERE sent_at is NULL
				AND date(time_stamp) = '".$sqlDate."'
				AND store_id = ".$storeId."
				ORDER BY partner_code ASC";

        $items = $conn->query($qry);
        $files = [];
        $csvCount = 0;
        $fp = '';

        if($items->fetch()) {
            while ($row = $items->fetch()) {
                $csvCount++;
                if ($row['partner_code'] != $prevPartnerCode) {
                    if ($fp) {
                        fclose($fp);
                    }
                    $filename = $row['partner_code'] . '_' . $fileDate . '_001.csv';
                    $filenames[] = $filename;
                    $filePath = $outDir . '/' . $filename;

                    $fp = fopen($filePath, "w");

                    // Write the file header
                    $outputRow = [  'Transaction_Header_ID',
                                    'Transaction_Detail_ID',
                                    'Time_Stamp',
                                    'Member_Reference_Number',
                                    'Member_Reference_Type',
                                    'Unique_Item_ID',
                                    'Unique_Item_Type',
                                    'Item_Description',
                                    'Quantity',
                                    'Item_Price',
                                    'Discount',
                                    'Source_Location',
                                    'Sub_Category',
                                    'Category',
                                    'Short_Description',
                                    'Filler_1',
                                    'Filler_2',
                                    'Filler_3',
                                    'Filler_4',
                                    'Partner_Code',
                                    'Autoclub'
                                ];
                    fputcsv($fp, $outputRow);
                    fseek($fp, -1, SEEK_CUR);
                    fwrite($fp, "\r\n");
                }
                // Last pass formatting
                $row['item_price'] = number_format($row['item_price'], 2);
                $row['discount'] = number_format($row['discount'], 2);

                fputcsv($fp, $row);
                fseek($fp, -1, SEEK_CUR);
                fwrite($fp, "\r\n");

                $prevPartnerCode = $row['partner_code'];
            }
            fclose($fp);

        }

        $logger->info($storeId.': '.$csvCount.' CSV Files written to '.$outDir.' for '.$sqlDate);
        if ($csvCount > 0){
            // Update the table to show these records have been written to CSV
            $qry = "UPDATE overnightupload_data
					SET sent_at = CURRENT_TIMESTAMP 
					WHERE date(time_stamp) = '".$sqlDate."'
					AND store_id = ".$storeId;
            $conn->query($qry);
        }
    }


    public function writeData_RAC($sqlDate) {


        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/overnightupload-cronjob-writeData_RAC.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);


        $storeId = $this->getStoreId('rac_en');
        $logger->info($storeId);
        //$logger->info("SQL Date---".$sqlDate);

        // First load up the extract table  AND ord.status='complete'
        $qry = "SELECT
				itm.product_id 			as product_id,
				itm.item_id 			as item_id, 
				itm.qty_invoiced 		as qty_invoiced,
				itm.row_total_incl_tax 	as row_total_incl_tax,
				itm.name 				as name,
				itm.sku 				as sku,
				ord.increment_id 		as increment_id, 
				ord.customer_id 		as customer_id,
				date_format(ord.created_at,'%Y%m%d%H%i%S') as timestamp
					
				FROM sales_order_item itm
				JOIN sales_order ord 
				ON ord.entity_id = itm.order_id 
				WHERE ord.store_id=$storeId				
				AND ord.status='complete'				
				AND date(ord.created_at) = '".$sqlDate."'
				";

        $this::writeItems_RAC($qry);


        // Write refunds
        $qry = "SELECT
				crditm.product_id 				as product_id,
				crditm.order_item_id 			as item_id, 
				crditm.qty * -1					as qty_invoiced,
				crditm.row_total_incl_tax as row_total_incl_tax,
				crditm.name 					as name,
				crditm.sku 						as sku,
				ord.increment_id 				as increment_id, 
				ord.customer_id 				as customer_id,
				date_format(ord.created_at,'%Y%m%d%H%i%S') as timestamp
									
				FROM sales_creditmemo_item crditm
				
				JOIN sales_order_item itm
				ON itm.item_id = crditm.order_item_id
				
				JOIN sales_creditmemo crd
				ON crd.entity_id = crditm.parent_id 
				
				JOIN sales_order ord 
				ON ord.entity_id = crd.order_id 
				
				WHERE ord.store_id=$storeId
				AND date(crd.created_at) = '".$sqlDate."'
				";

        $this::writeItems_RAC($qry);


        $qry = "SELECT				
				ord.discount_amount 	as discount_amount,
				ord.coupon_rule_name 	as coupon_rule_name,
				LOWER(ord.coupon_code) 	as coupon_code,
				ord.increment_id 		as increment_id, 
				ord.customer_id 		as customer_id,
				date_format(ord.created_at,'%Y%m%d%H%i%S') as timestamp,
				concat(ord.entity_id,'C') as entity_id
				
				FROM sales_order ord 
								
				WHERE NOT ISNULL(ord.coupon_code)
				AND NOT EXISTS ( 
					SELECT * 
					FROM sales_order_item itm
					WHERE itm.order_id = ord.entity_id
					AND itm.sftpSentDate != '1979-01-01'
				)
				
				AND ord.store_id=$storeId
				AND ord.status='complete'
				AND date(ord.created_at) = '".$sqlDate."'				
				";

        $this::writeCoupons_RAC($qry);


        $kioskStoreId = $this->getStoreId('rackiosks_en');

        // First load up the extract table
        $qry = "SELECT
				itm.product_id 			as product_id,
				itm.item_id 			as item_id, 
				itm.qty_invoiced 		as qty_invoiced,
				itm.row_total_incl_tax 	as row_total_incl_tax,
				itm.name 				as name,
				itm.sku 				as sku,
				ord.increment_id 		as increment_id, 
				ord.customer_id 		as customer_id,
				date_format(ord.created_at,'%Y%m%d%H%i%S') as timestamp
					
				FROM sales_order_item itm
				JOIN sales_order ord 
				ON ord.entity_id = itm.order_id 
				WHERE itm.sftpSentDate like '%1979-01-01%' 
				
				AND ord.store_id=$kioskStoreId
				AND ord.status='complete'
				
				AND date(ord.created_at) = '".$sqlDate."'
				";

        $this::writeKioskItems_RAC($qry);

    }

    function getPartnerCode($storeId, $productId){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/overnightupload-cronjob-getPartnerCode.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $product = $this->productObj
                        ->setStoreId($storeId)
                        ->load($productId);

        if ($product->getId()){
            $partnerCodeTextById =  $product->getResource()->getAttribute('partnercode')->getFrontend()->getValue($product);
            $partnerCodeInfo = $this->partnerObj->getCollection();
            $partnerCodeInfo->addFieldToFilter('store_id',$storeId);
            $partnerCodeInfo->addFieldToFilter('company_name',$partnerCodeTextById);
            $partnerData = $partnerCodeInfo->getFirstItem()->getData();

            if ($partnerData) {
                $logger->info($partnerData['partnercode']);
                return $partnerData['partnercode'];
            }
        }

        return '';
    }


    public function writeItems_RAC($qry){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/overnightupload-cronjob-writeItems_RAC.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($qry);

        $conn = $this->connection->getConnection();
        $items = $conn->query($qry);

        $prevCustomerId = 0;
        $memberNumber = 0;
        $values = '';

        $storeId = $this->getStoreId('rac_en');
        $customerAttrId = $this->getCustomerAttrId();

         while ($row = $items->fetch()) {
            $productId	= $row['product_id'];
            $product = $this->productObj->load($productId);
            $transaction_header_id = $row['increment_id'];
            $transaction_detail_id = $row['item_id'];
            $time_stamp = $row['timestamp'];

            //$store_id = $this->getStoreId('rac_en');
            // Only process the customer if we need to
            if ($row['customer_id'] != $prevCustomerId){
                $memberResult	= $conn->query("SELECT value FROM customer_entity_varchar 
												 WHERE entity_id = '".$row['customer_id']."' 
												 AND attribute_id=$customerAttrId");
                $memberRow 		= $memberResult->fetch();

                $memberNumber 	= $memberRow['value'];
                $prevCustomerId = $row['customer_id'];
            }

            $member_reference_number = $memberNumber;
            $member_reference_type = '';
            $unique_item_id = $row['sku'];

            $unique_item_type 	= 'SKU';
            $item_description 	= substr(strip_tags($row['name']), 0, 49);
            $item_description	= str_replace("'","''",$item_description);

            $quantity = number_format($row['qty_invoiced']);
            $sell_price = $row['row_total_incl_tax']/$row['qty_invoiced'];

            // Override the RRP - 29/07/2015

            $prices = $this::_getPricesAtDate($storeId,$row['sku'],$time_stamp);
            $item_price = number_format($prices['rrp'],2);
            //$item_price = number_format($product->getPrice(),2);
            if ($item_price == 0){
                $item_price = $sell_price;
                $logger->info(' Overriding RRP to sell price for: '.$row['sku']);
            }
            $discount 	= number_format($item_price - $sell_price,2);
            $logger->info('SKU: '.$row['sku'].' sell_price: '.$sell_price.' item_price: '.$item_price.' discount: '.$discount);

            $source_location = 'Online Shop';
            $sub_category 	= '';
            $category 		= '';
            $short_description = $item_description;
            $filler1 = '';
            $filler2 = '';
            $filler3 = '';
            $filler4 = '';

            $providerTextById =  $product->getResource()->getAttribute('provider')->getFrontend()->getValue($product);
            $old_partner_code = $this::getOldFAMRACOSPartnerCode($providerTextById,
                                                                 $product->getAttributeText('external_product_type'),
                                                                 $product->getSku(),
                                                                 $product->getName()
                                                                 );

            $partner_code = $this::getPartnerCode($storeId, $productId);

            if ($old_partner_code != $partner_code) {
                $logger->info('Partner-Code-Mismatch: '.$row['sku'].' OLD='.$old_partner_code.' NEW='.$partner_code);
            }

            if($partner_code != ''){
                // APPEND THE PARTNER CODE - 29/07/2015
                $transaction_header_id .='-'.$partner_code;

                $autoclub = 'RAC';
                $outputRow = [  $storeId,
                                $transaction_header_id,
                                $transaction_detail_id,
                                $time_stamp,
                                $member_reference_number,
                                $member_reference_type,
                                $unique_item_id,
                                $unique_item_type,
                                $item_description,
                                $quantity,
                                $item_price,
                                $discount,
                                $source_location,
                                $sub_category,
                                $category,
                                $short_description,
                                $filler1,
                                $filler2,
                                $filler3,
                                $filler4,
                                $partner_code,
                                $autoclub
                            ];

                $values.= "('".implode("','",$outputRow)."'),";

            }

        } //End of the while condition

        if ($values != '') {
            // Remove the trailing comma
            $values = substr($values,0,strlen($values)-1);
            $this::writeData($values);
        }

    }

    function getOldFAMRACOSPartnerCode($provider, $external_type, $sku, $desc){
        $groupCode = '';
        if ($provider == 'External') {
            if ($external_type == 'Batteries'){
                $groupCode = 'RACBATTERIES';
            } else if ($external_type == 'Driving'){
                $groupCode = 'FAMRACMOT';
            } else if ($external_type == 'Maps'){
                $groupCode = 'FAMRACTRAV';
            } else if ($external_type == 'WishMyerCard'){
                if (strpos($desc, 'WISH')!==false){
                    $groupCode = 'EXTWHISH';
                } else {
                    $groupCode = 'EXTMYER';
                }
            } else {
                $groupCode = 'FAMRACOS';
            }
        } else {
            if($provider){
                $providerObjData = $this->providerObj->getCollection();
                $providerObjData->addFieldToFilter('providerid',$provider);
                $providerItem = $providerObjData->getFirstItem()->getData();
                if($providerItem){
                    $groupCode = $providerItem['patner_groupid'];
                }else{
                    $groupCode = 'FAMRACOS';
                }
            }
        }

        return $groupCode;
    }

    public function writeCoupons_RAC($qry){
        $conn = $this->connection->getConnection();
        $items = $conn->query($qry);

        $prevCustomerId = 0;
        $memberNumber = 0;
        $values = '';
        $customerAttrId = $this->getCustomerAttrId();
        while ($row = $items->fetch()) {
            $store_id = $this->getStoreId('rac_en');
            $transaction_header_id = $row['increment_id'];
            $transaction_detail_id = $row['entity_id'];
            $time_stamp = $row['timestamp'];
            // Only process the customer if we need to
            if ($row['customer_id'] != $prevCustomerId){
                $memberResult	= $conn->query("SELECT value FROM customer_entity_varchar 
												    WHERE entity_id = '".$row['customer_id']."' 
												    AND attribute_id=$customerAttrId");  //csutomer custome attribute
                $memberRow 		= $memberResult->fetch();
                $memberNumber 	= $memberRow['value'];

                $prevCustomerId = $row['customer_id'];
            }


            $member_reference_number = $memberNumber;
            $member_reference_type = '';
            $unique_item_id 	= $row['coupon_code'];
            $unique_item_type 	= 'COUPON';
            $item_description 	= substr(strip_tags($row['coupon_rule_name']), 0, 49);
            $item_description	= str_replace("'","''",$item_description);
            $quantity 			= number_format(1);

            $item_price = number_format(0);
            $sell_price = number_format(0);
            $discount 	= number_format($row['discount_amount']*-1,2);

            $source_location = 'Online Shop';
            $sub_category 	= '';
            $category 		= '';
            $short_description = $item_description;
            $filler1 = '';
            $filler2 = '';
            $filler3 = '';
            $filler4 = '';
            $partner_code = 'FAMRACOS';
            $autoclub = 'RAC';

            $outputRow = [  $store_id,
                            $transaction_header_id,
                            $transaction_detail_id,
                            $time_stamp,
                            $member_reference_number,
                            $member_reference_type,
                            $unique_item_id,
                            $unique_item_type,
                            $item_description,
                            $quantity,
                            $item_price,
                            $discount,
                            $source_location,
                            $sub_category,
                            $category,
                            $short_description,
                            $filler1,
                            $filler2,
                            $filler3,
                            $filler4,
                            $partner_code,
                            $autoclub
                       ];
            $values.= "('".implode("','",$outputRow)."'),";
        }
        if ($values != '') {
            // Remove the trailing comma
            $values = substr($values,0,strlen($values)-1);
            $this::writeData($values);
        }
    }

    public function writeKioskItems_RAC($qry){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/overnightupload-cronjob-writeKioskItems_RAC.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $conn = $this->connection->getConnection();
        $items = $conn->query($qry);

        $memberNumber = 0;

        $store_id = $this->getStoreId('rac_en');

        $values = '';
        while ($row = $items->fetch()) {
            $productId	= $row['product_id'];
            $product 	= $this->productObj->load($productId);

            $transaction_header_id = $row['increment_id'];
            $transaction_detail_id = $row['item_id'];
            $time_stamp = $row['timestamp'];

            // For Kiosk Sales the member number will be in the fax number
            $order = $this->orderObj->loadByIncrementId($row['increment_id']);

            $memberNumber = $order->getBillingAddress()->getFax();

            $member_reference_number = $memberNumber;
            $member_reference_type = '';
            $unique_item_id 	= $row['sku'];
            $unique_item_type 	= 'SKU';
            $item_description 	= substr(strip_tags($row['name']), 0, 49);
            $item_description	= str_replace("'","''",$item_description);

            $quantity = number_format($row['qty_invoiced']);

            $sell_price = $row['row_total_incl_tax']/$row['qty_invoiced'];

            // Override the RRP - 29/07/2015
            $prices = $this::_getPricesAtDate($store_id,$row['sku'],$time_stamp);

            $item_price = number_format($prices['rrp'],2);
            //$item_price = number_format($product->getPrice(),2);
            if ($item_price == 0){
                $item_price = $sell_price;
                $logger->info(' Overriding RRP to sell price for: '.$row['sku']);
            }
            $discount 	= number_format($item_price - $sell_price,2);

            // For Kiosk Sales, the Source location should be the customer "Firstname"
            // Which will contain a value like "Kiosk 1"
            $source_location = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
            $sub_category 	= '';
            $category 		= '';
            $short_description = $item_description;
            $filler1 = '';
            $filler2 = '';
            $filler3 = '';
            $filler4 = '';
            $providerTextById =  $product->getResource()->getAttribute('provider')->getFrontend()->getValue($product);
            $old_partner_code = $this::getOldFAMRACOSPartnerCode($providerTextById,
                                                                 $product->getAttributeText('external_product_type'),
                                                                 $product->getSku(),
                                                                 $product->getName()
                                                                );

            $partner_code = $this::getPartnerCode($store_id, $productId);

            if ($old_partner_code != $partner_code) {
                $logger->info('Partner-Code-Mismatch: '.$row['sku'].' OLD='.$old_partner_code.' NEW='.$partner_code);
            }

            if ($partner_code != ''){

                // APPEND THE PARTNER CODE - 29/07/2015
                $transaction_header_id .='-'.$partner_code;

                $autoclub = 'RAC';

                $outputRow = [  $store_id,
                                $transaction_header_id,
                                $transaction_detail_id,
                                $time_stamp,
                                $member_reference_number,
                                $member_reference_type,
                                $unique_item_id,
                                $unique_item_type,
                                $item_description,
                                $quantity,
                                $item_price,
                                $discount,
                                $source_location,
                                $sub_category,
                                $category,
                                $short_description,
                                $filler1,
                                $filler2,
                                $filler3,
                                $filler4,
                                $partner_code,
                                $autoclub
                            ];
                $values.= "('".implode("','",$outputRow)."'),";
            }
        }
        if ($values != '') {
            // Remove the trailing comma
            $values = substr($values,0,strlen($values)-1);
            $this::writeData($values);
        }
    }


    public function getStoreId($code){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/overnightupload-cronjob-getStore.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $store = $this->store->load($code);
        return $store['store_id'];
    }

    function _getPricesAtDate($storeId, $sku, $dateToFind){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/overnightupload-cronjob-getPricesAtDate.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $qry = "SELECT sell_price, rrp FROM price_history
				WHERE store_id = ".$storeId."
				AND sku = '".$sku."'
				AND created_at <= '".$dateToFind."'
				ORDER BY created_at DESC
				LIMIT 1";
        
        $conn 	= $this->connection->getConnection();
        $res 	= $conn->query($qry);
        $rec	= $res->fetch();

        if ($rec){
            return $rec;
        } else {
            $qry = "SELECT rrp FROM price_history
					WHERE store_id = 0
					AND sku = '".$sku."'
					AND created_at <= '".$dateToFind."'
					ORDER BY created_at DESC
					LIMIT 1";
            $res 	= $conn->query($qry);
            $rec	= $res->fetch();

            if ($rec){
                return $rec;
            } else {
                $logger->info(' NO PRICING DATA: '.$sku.' '.$dateToFind);
                return null;
            }
        }
    }

    public function getCustomerAttrId(){
        $customerAttr = $this->eavCollection->addFieldToFilter('attribute_code','memberno');
        $customerAttData  = $customerAttr->getFirstItem();
        $customerAttrId = $customerAttData['attribute_id'];
        return $customerAttrId;
    }

}