<?php

     namespace Serole\Racvportal\Controller\Report;

     use Magento\Framework\App\Action\Context;

     class GenerateReport extends \Serole\Racvportal\Controller\Cart\Ajax {


         public function execute(){

            try {
                $post = $this->getRequest()->getParams();
                if (!$post['from'] || !$post['to'] || !is_numeric($post['shop']) || !is_numeric($post['report_for'])) {
                    $this->messageManager->addError("Some thing went wrong");
                    $this->pageRedirect();
                }
                $report_dir = $this->helper->getBaseDir() . "/var/reports/";
                if (!file_exists($report_dir)) {
                    mkdir($report_dir, 0777, true);
                    chmod($report_dir, 0777);
                }
                if (!is_writable($report_dir)) {
                    chmod($report_dir, 0777);
                }

                $report_shopId = $post['shop'];
                $from_date = $post['from'];
                $to_date = $post['to'];
                $report_for = $post['report_for'];

                $sql = "SELECT incrementid FROM `racvportal` WHERE `shop_id` =" . $report_shopId;
                $connection = $this->resourceConnection->getConnection();
                $result = $connection->fetchAll($sql);

                $increment_id = array();
                foreach ($result as $row) {
                    foreach ($row as $row_s) {
                        $increment_id[] = $row_s;
                    }
                }

                $filter_increment_id = implode(",", $increment_id);

                $qry_to_date = $to_date . " 23:59:59";
                $qry_from_date = $from_date;

                $qry_from_date = date('Y-m-d h:i:s', strtotime($qry_from_date . '-10 hours')); //echo "<br>";
                $qry_to_date = date('Y-m-d h:i:s', strtotime($qry_to_date . '-10 hours')); //echo "<br>"; exit;


                $report_datetype = $_POST['report_datetype'];
                $skip_order_row = $_POST['report_skiporderrow'] == 1;

                $getWebsitesql = "select website_id,name,code from store_website where code = '" . $post['website'] . "'";
                $queryResults = $connection->fetchAll($getWebsitesql);

                $websiteId = $queryResults[0]['website_id'];
                $websiteCode = $queryResults[0]['code'];
                $websiteName = $queryResults[0]['name'];

                if ($websiteCode != "ALLSTORES") {
                    $storeDetailsSql = "select * from store where website_id=" . $websiteId;
                    $storeResults = $connection->fetchAll($storeDetailsSql);
                    $storeId = $storeResults[0]['store_id'];
                }

                $report_type = $_POST['report_type'];
                if(!$filter_increment_id) {
                   $this->orderNotFound();
                }
                $file = $this->writeEverythingCSV($filter_increment_id, $report_for, $connection, $qry_from_date, $qry_to_date, $storeId,
                    $websiteCode, $report_dir, $report_datetype, $skip_order_row);
                $this->returnFile($report_dir, $file);
                exit;
            }catch (\Exception $e){
                echo  $e->getMessage();
            }
         }


         public function returnFile($report_dir, $filename){
             header('Content-Description: File Transfer');
             header("Content-type: text/csv");
             header('Content-Disposition: attachment; filename='.$filename);
             header('Expires: 0');
             header('Cache-Control: private, max-age=1');
             header("Pragma: ");
             header('Content-Length: ' . filesize($report_dir.$filename));
             ob_clean();
             flush();
             readfile($report_dir.$filename);
         }



         public function writeEverythingCSV($filter_increment_id,$report_for,$connection, $qry_from_date, $qry_to_date,
                                            $storeId, $websiteName, $report_dir, $report_datetype, $skip_order_row){

             $showSubsidy = true;
             $customerName= $this->helper->getCustomerName();
             $customerId= $this->helper->getCustomerId();
             $time = time();
             $filename = 'Sales-Extract-RacvPortal-'.$websiteName.'-'.substr($qry_from_date,0,10).'-'.substr($qry_to_date,0,10).'-'.$time.'.csv';
             $fp = fopen($report_dir.$filename, 'w');

             $columnHeaders = [
                                 'Row Type',
                                 'Order Number',
                                 'Date Created',
                                 'Date Updated',
                                 'Customer Name',
                                 'Shipping Address',
                                 'Item SKU',
                                 'Item Name',
                                 'Item Price (inc. GST)',
                                 'Item Qty',
                                 'Item Subtotal (inc. GST)'
                             ];
             $line = '"'.implode('","', $columnHeaders).'"'."\n";
             fwrite($fp, $line);

             $order_sql = "SELECT
                            ord.entity_id         	as entity_id,
                            bill.fax            	as Alt_Member_No,               
                            ord.store_id          	as Store_Id,
                            'Order'             	as Row_type,
                            ord.increment_id        as Order_Number,                
                            ord.created_at          as Create_Date,
                            ord.updated_at          as Update_Date,          
                            concat(bill.firstname,' ',bill.lastname) as Member_Name,  
                            concat(bill.street,', ',bill.city,', ',bill.postcode) as Member_Address,  
                            ' ' as SKU,
                            ' ' as Product_Name,
                            ' ' as Unit_Price,
                            ' ' as Unit_Price_Inc,        
                            ' ' as Qty,";

             if($report_for==0){
                 if($filter_increment_id!=''){
                     $order_sql.="                 
                                total_invoiced            as OrderTotal
                                FROM sales_order ord 
                                     
                                LEFT JOIN sales_order_address bill ON bill.parent_id = ord.entity_id AND bill.address_type='billing'     
                
                                WHERE ord.status = 'complete'
                                and (ord.store_id = ".$storeId." and ord.increment_id IN (".$filter_increment_id.") and ord.customer_id =".$customerId.")";
                 }else{
                    if($filter_increment_id==''){
                        $this->orderNotFound();
                        //exit();
                    }else{
                       $this->error();
                       //exit();
                    }
                 }
             }else if($report_for==1){
                 if($filter_increment_id!=''){
                     $order_sql.="                 
                                    total_invoiced            as OrderTotal
                                    FROM sales_order ord 
                                                                             
                                    LEFT JOIN sales_order_address bill ON bill.parent_id = ord.entity_id AND bill.address_type='billing'     
                    
                                    WHERE ord.status = 'complete'
                                    and (ord.store_id = ".$storeId." and ord.increment_id IN (".$filter_increment_id."))";
                 }else{
                     if($filter_increment_id==''){
                         $this->orderNotFound();
                        // exit();
                     }else{
                         $this->error();
                        // exit();
                     }
                 }
             }
             //print_r($order_sql); exit;
             //echo $report_datetype; exit;
             switch ($report_datetype){
                 case 'created':
                     $order_sql.= " and (ord.created_at >= '".$qry_from_date."' and ord.created_at <='".$qry_to_date."')";
                     break;

                 case 'updated':
                     $order_sql.= " and  (ord.updated_at >= '".$qry_from_date."' and ord.updated_at <='".$qry_to_date."')";
                     //$order_sql.= " and (ord.created_at >= '".$qry_from_date."' and ord.created_at <='".$qry_to_date."')";
                     break;

                 default:
                     $order_sql.= " and (
                      (ord.updated_at >= '".$qry_from_date."' and ord.updated_at <='".$qry_to_date."')
                        or 
                      (ord.created_at >= '".$qry_from_date."' and ord.created_at <='".$qry_to_date."')
                    )";

             }
             $totalSum = 0;
             //echo $order_sql;exit();
             $orders=$connection->query($order_sql);
             //echo "<pre>"; print_r($orders->fetch()); exit;
             while($order = $orders->fetch()) {
                 $order_row = array_slice($order,3);
                 $entity_id = $order['entity_id'];
                 $order_number = $order['Order_Number'];
                 $storeName = $this->store->getStore($order['Store_Id'])->getName();

                 if( strtotime($order['Create_Date']) < strtotime('2016-01-01') ) {
                         $item_sql = "SELECT sum(itm.tax_amount) as incorrectGST
                                        FROM sales_order_item itm 
                                        WHERE itm.order_id = ".$entity_id."
                                        AND itm.sku in ('WGC', 
                                                'WGC500E', 
                                                'WGC500M', 
                                                'WGC250E', 
                                                'WGC250M', 
                                                'WGF100E', 
                                                'WFG100M', 
                                                'MCG100M', 
                                                'MGC50M'
                                                  )
                                        GROUP BY itm.order_id";
                                             $items=$connection->query($item_sql);
                                             if ($item = $items->fetch()){
                                                 $incorrectGST = $item['incorrectGST'];
                                                 $order_row['GST']     = $order_row['GST'] - $incorrectGST;
                                                 $order_row['Subtotal']  = $order_row['Subtotal'] + $incorrectGST;
                                             }
                 }
                 if($report_for==0){
                     $order_row['Member_Name']  = $customerName;
                 }else{
                     $order_row['Member_Name'] ;
                 }

                 if ($skip_order_row == false){
                     fputcsv($fp, $order_row);
                 }

                 $item_sql = "SELECT
            'Item'              as Row_type,
            '".$order_number."' as Order_Number,            
            ' ' as Create_Date,
            ' ' as Update_Date,           
            ' ' as Member_Name,
            ' ' as Member_Address,
            itm.sku             as SKU, 
            itm.name            as Product_Name,
			format(itm.row_total_incl_tax/itm.qty_ordered,2)      as Unit_Price_Inc,            
            itm.qty_ordered         as Qty_Ordered,
            format(itm.row_total_incl_tax,2)      as SubTotal_Inc,";


                 $item_sql .= "   
					' ' as Order_Subtotal,
					' ' as GST,
					' ' as OrderTotal
            FROM sales_order_item itm
            WHERE itm.order_id = ".$entity_id;

                 $checkOrderSubTotal = 0;
                 $itemString = '';
                 $rrpZero = false;
                 $items=$connection->query($item_sql);

                 $totalColumn = array();

                 while($item = $items->fetch()) {
                     // At the beginning of their life, Woolworths and Myer cards were
                     // Incorrectly recorded as having a GST component.
                     // So we must process them differently - as the row_total will not
                     // have the GST component.

                     $sku = $item['SKU'];
                     if( strtotime($order['Create_Date']) < strtotime('2016-01-01') ) {
                         if ($sku == 'WFG100M'){
                             $item['Unit_Price'] = 95.00;
                             $item['SubTotal'] = $item['Qty_Ordered'] * $item['Unit_Price'];
                         } else if ($sku == 'WGC250M') {
                             $item['Unit_Price'] = 237.50;
                             $item['SubTotal'] = $item['Qty_Ordered'] * $item['Unit_Price'];
                         } else if ($sku == 'WGC500M') {
                             $item['Unit_Price'] = 475.00;
                             $item['SubTotal'] = $item['Qty_Ordered'] * $item['Unit_Price'];
                         } else if ($sku == 'MCG100M') {
                             $item['Unit_Price'] = 95.00;
                             $item['SubTotal'] = $item['Qty_Ordered'] * $item['Unit_Price'];
                         } else if ($sku == 'MGC50M') {
                             $item['Unit_Price'] = 47.50;
                             $item['SubTotal'] = $item['Qty_Ordered'] * $item['Unit_Price'];
                         }
                     }

                     // include the order create date, Update Date, Member names etc

                     $perth_create_Date=date('Y-m-d h:i:s', strtotime($order_row['Create_Date'].'+10 hours'));
                     $perth_update_Date=date('Y-m-d h:i:s', strtotime($order_row['Update_Date'].'+10 hours'));

                     $item['Create_Date']  = $perth_create_Date;
                     $item['Update_Date']  = $perth_update_Date;

                     if($report_for==0){
                         $item['Member_Name']  = $customerName;
                     }else{
                         $item['Member_Name'] 	= $order_row['Member_Name'];
                     }

                     $item['Member_Address'] = $order_row['Member_Address'];

                     $checkOrderSubTotal += $item['SubTotal_Inc'];
                     $totalSum += $item['SubTotal_Inc'];

                     $itemString .= serialize($item).'<br />';
                     if ($storeId==0){
                         array_unshift($item,$storeName);
                     }
                     fputcsv($fp, $item);
                 }
                 $totalColumn['Row_type'] = '';
                 $totalColumn['Order_Number'] = '';
                 $totalColumn['Create_Date'] = '';
                 $totalColumn['Update_Date'] = '';
                 $totalColumn['Member_Name'] = '';
                 $totalColumn['Member_Address'] = '';
                 $totalColumn['SKU'] = '';
                 $totalColumn['Product_Name'] = '';
                 $totalColumn['Unit_Price_Inc'] = '';
                 $totalColumn['Qty_Ordered'] = 'Total';
                 $totalColumn['SubTotal_Inc'] =$totalSum;



             }
             if(!empty($totalColumn)) {
                 fputcsv($fp, $totalColumn);
             }
             fclose($fp);
             return $filename;
         }

      public function orderNotFound(){
         $this->messageManager->addWarning("No order's for selected shop");
         $this->pageRedirect();
      }

      public function pageRedirect(){
         $this->_redirect($this->_redirect->getRefererUrl());
      }

      public function error(){
          $this->messageManager->addError("Shop is't selected");
          $this->pageRedirect();
      }
  }