<?php

namespace Serole\Partnerreport\Controller\Adminhtml\Salesorder;

use Magento\Backend\App\Action;

require_once(BP.'/lib/reports/ReportUtils/Report.class.php');

class Post extends \Magento\Backend\App\Action {

    private $coreRegistry = null;

    private $resultPageFactory;

    private $backSession;

    private $order;

    private $pdfHelper;

    private $objectmanager;

    private $category;

    protected $catCache;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order $order,
        \Serole\Pdf\Helper\Pdf $pdfHelper,
        \Serole\Orderreport\Helper\Data $orderReportHelper,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Catalog\Model\Category $category,
        \Magento\Store\Model\Store $store
        
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->pdfHelper = $pdfHelper;
        $this->orderReportHelper = $orderReportHelper;        
        $this->backSession = $context->getSession();        
        $this->store = $store;
        $this->objectmanager =  $objectmanager;
        $this->category = $category;
        $this->catCache = array();
        parent::__construct($context);
    }
	
	protected function _isAllowed()
    {
        return true;
    }
 
     public function execute(){
            //echo "Execute"; exit;
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-partnercodeReport-controllerPost.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            try {
                $params = $this->getRequest()->getParams();


                $mediaPath = $this->pdfHelper->getMediaBaseDir();
                $report_dir = $mediaPath . "/reports/";

                if (!file_exists($report_dir)) {
                    mkdir($report_dir, 0777, true);
                    chmod($report_dir, 0777);
                }
                if (!is_writable($report_dir)) {
                    chmod($report_dir, 0777);
                }

                $from_date = $params['from_date'];
                $to_date = $params['to_date'];

                $report_timerange = $params['report_timerange'];

                if ($report_timerange == 'invoice') {
                    $qry_from_date = date('Y-m-d', strtotime(str_replace('/', '-', $from_date))) . ' 07:00:01';
                    $qry_to_date = date('Y-m-d', strtotime(str_replace('/', '-', $to_date))) . ' 07:00:00';
                } else {
                    $qry_from_date = date('Y-m-d', strtotime(str_replace('/', '-', $from_date))) . ' 00:00:00';
                    $qry_to_date = date('Y-m-d', strtotime(str_replace('/', '-', $to_date))) . ' 23:59:59';
                }

                $qry_from_date = date('Y-m-d H:i:s', strtotime($qry_from_date . '-8 hours'));
                $qry_to_date = date('Y-m-d H:i:s', strtotime($qry_to_date . '-8 hours'));

                $report_datetype = $params['report_datetype'];
                $skip_order_row = $params['report_skiporderrow'] == 1;

                if($params['store_id'] == 'ALLSTORES'){
                    $websiteCode = 'ALLSTORES';
                }
                $resource = $this->objectmanager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $getWebsitesql = "select website_id,name,code from store_website where code = '" . $params['store_id'] . "'";
                $results = $connection->fetchAll($getWebsitesql);
                //echo "<pre>"; print_r($results); exit;
               if($results){
                $websiteId 		= $results[0]['website_id'];
                $websiteCode 	= $results[0]['code'];
                $websiteName 	= $results[0]['name'];
               }
                if ($websiteCode != 'ALLSTORES') {
                    $storeDetailsSql = "select * from store where website_id=".$websiteId;
                    $storeResults = $connection->fetchAll($storeDetailsSql);
                    $storeId = $storeResults[0]['store_id'];
                } else {
                    $storeId = 0;
                }

                $eavAttributeId = $this->objectmanager->get('\Magento\Eav\Model\ResourceModel\Entity\Attribute')->getIdByCode('catalog_product', 'price');
                $memberNoAttributeId = $this->objectmanager->get('\Magento\Eav\Model\ResourceModel\Entity\Attribute')->getIdByCode('customer', 'memberno'); //exit;
                //$skus = $this->scopeManger->getValue('partnerreport/general/skuvalue', ScopeInterface::SCOPE_STORE, $storeId);
                //$skusArray = explode(',',$skus);
                //echo "Rameshhhh"; exit;
                $report_type = $params['report_type'];
                //if(!empty($skusArray)){
                if (isset($report_type) && $report_type == 'category') {
                    //echo "Step1"; exit;
                    $file = $this->writeCategoryCSV($connection, $qry_from_date, $qry_to_date, $storeId, $websiteCode,
                        $report_dir, $report_datetype, $eavAttributeId, $memberNoAttributeId);
                } else {
                    //echo "Step2"; exit;
                    $file = $this->writeEverythingCSV($connection, $qry_from_date, $qry_to_date, $storeId, $websiteCode,
                        $report_dir, $report_datetype, $skip_order_row, $memberNoAttributeId);
                }
                $this->returnFile($report_dir, $file);
                //}
            }catch (\Exception $e){
                $logger->info($e->getMessage());
            }
        //exit;
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

    public function writeCategoryCSV($connection, $qry_from_date, $qry_to_date, $storeId, $websiteName, $report_dir,
                                     $report_datetype,$eavAttributeId,$memberNoAttributeId){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-partnercodeReport-writeCategoryCSV.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try {
            $time = time();
            $filename = 'Category-Sales-Extract-' . $websiteName . '-' . substr($qry_from_date, 0, 10) . '-' . substr($qry_to_date, 0, 10) . '-' . $time . '.csv';
            $fp = fopen($report_dir . $filename, 'w');

            $columnHeaders = ['Order Number',
                'Date Created',
                'Date Updated',

                'Customer Name',
                'Member Number',
                'Customer Email',
                'Shipping Address',

                'Item Category',
                'Item SKU',
                'Item Name',
                'Item RRP',
                'Item Price (ex. GST)',
                'Item Qty',
                'Item Subtotal (ex. GST)'

            ];
            $line = '"' . implode('","', $columnHeaders) . '"' . "\n";
            fwrite($fp, $line);

            /*@Ramesh " fooman_surcharge_amount_invoiced    as Surcharge," add after "shipping_amount"*/

            $order_sql = "SELECT
                ord.entity_id                   as entity_id,
                'Order'                         as Row_type,
                ord.increment_id                as Order_Number,                                
                ord.created_at                  as Create_Date,
                ord.updated_at                  as Update_Date,                  
                concat(shp.firstname,' ',shp.lastname) as Member_Name,  
                (select value FROM customer_entity_varchar mbrno 
                    WHERE mbrno.entity_id = ord.customer_id 
                    AND mbrno.attribute_id = " . $memberNoAttributeId . ") as Member_No,                          
                ord.customer_email              as Email,               
                concat(shp.street,', ',shp.city,', ',shp.region,' ',shp.postcode) as Member_Address,    
                ' ' as SKU,
                ' ' as Product_Name,
                ' ' as RRP,
                ' ' as Unit_Price,
                ' ' as Qty,
                ' ' as Item_Subtotal,                           
                subtotal                            as Subtotal,
                shipping_amount                     as Postage,
                foomanorder.amount_invoiced	        as Surcharge,           
                foomanorder.tax_amount                          as GST,
                total_invoiced                      as OrderTotal
                
                FROM sales_order ord 
				JOIN sales_order_address shp ON shp.parent_id = ord.entity_id AND shp.address_type='billing'
                LEFT JOIN fooman_totals_order foomanorder ON	foomanorder.order_id = ord.entity_id
                
                WHERE ord.status = 'complete' and 
                    (ord.store_id = " . $storeId . " or " . $storeId . "=21 or " . $storeId . "='0') and (
                    (ord.updated_at >= '" . $qry_from_date . "' and ord.updated_at <='" . $qry_to_date . "')
                        or 
                    (ord.created_at >= '" . $qry_from_date . "' and ord.created_at <='" . $qry_to_date . "')
                )";


            $orders = $connection->query($order_sql);
			// echo "<pre>";
			// print_r($orders->fetchAll());
			// exit;
            while ($order = $orders->fetch()) {
                $order_row = array_slice($order, 1);
                $entity_id = $order['entity_id'];
                $order_number = $order['Order_Number'];
                $perth_create_Dates = date('Y-m-d h:i:s', strtotime($order_row['Create_Date'] . '+8 hours'));
                $perth_update_Dates = date('Y-m-d h:i:s', strtotime($order_row['Update_Date'] . '+8 hours'));
                $order_row['Create_Date'] = $perth_create_Dates;
                $order_row['Update_Date'] = $perth_update_Dates;

                $item_sql = "SELECT
                        '" . $order_number . "' as Order_Number,                        
                        ' ' as Create_Date,
                        ' ' as Update_Date,                     
                        ' ' as Member_Name,
                        ' ' as Member_No,
                        ' ' as Email,
                        ' ' as Member_Address,
                        (SELECT GROUP_CONCAT(category_id) FROM catalog_category_product catprod WHERE catprod.product_id =itm.product_id) 				as Category,
                        itm.sku                         as SKU, 
                        itm.name                        as Product_Name,
                        
                        (select value from catalog_product_entity_decimal prd
                        where prd.entity_id = itm.product_id and prd.attribute_id = " . $eavAttributeId . " limit 1) as RRP,
                        format(itm.row_total/itm.qty_ordered,3)                     as Unit_Price, 
                        itm.qty_ordered                								as Qty_Ordered,
                        itm.row_total                   							as SubTotal
                        FROM sales_order_item itm  WHERE itm.order_id = " . $entity_id;

                $items = $connection->query($item_sql);
				 //echo "<pre>";
				// print_r($items->fetchAll());
				// exit;
                while ($item = $items->fetch()) {
					 //print_r($item['Category']);
					// exit;
                    $sku = $item['SKU'];
                    if ($sku == 'WFG100M') {
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

                    } else if ($sku == 'SPEICALHARUM') {
                        $item['RRP'] = 20.00;
                        // Someone deleted "SPEICALHARUM" so the RRP won't be available :(
                    } else if ($sku == 'HLPSE') {
                        $item['RRP'] = 36.00;
                        // Someone deleted "HLPSE" so the RRP won't be available :(
                    }

                    if (!$item['RRP'] > 0) {
                        $rrpZero = true;
                    }

					// $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					// $categoryobj = $this->objectmanager->create('\Magento\Store\Model\StoreManagerInterface');
					// $rootCatId = $categoryobj->getStore($storeId)->getRootCategoryId();
					// $subCategory = $this->objectmanager->create('Magento\Catalog\Model\Category')->load($rootCatId);
					// $subCats = $subCategory->getChildrenCategories();
					// //print_r($subCats->getData());
					// $subCatsIds=array();
					// foreach($subCats as $sub_cats){ 
						// $subCatsIds[]=$sub_cats->getId();
					// }
					// //print_r($subCatsIds);
					// $catsIds=explode(",",$item['Category']);
					// print_r($catsIds);
					
					// $category_id=array_intersect($subCatsIds,$catsIds);					
					// //print_r($category_id);
					// $category_ids=implode(',',$category_id);
					
                    // include the order create date, Update Date, Member names etc

                    $item['Create_Date'] = $order_row['Create_Date'];
                    $item['Update_Date'] = $order_row['Update_Date'];
                    $item['Member_Name'] = $order_row['Member_Name'];
                    $item['Member_No'] = $order_row['Member_No'];
                    $item['Email'] = $order_row['Email'];
                    $item['Member_Address'] = $order_row['Member_Address'];
                    $item['Category'] = $this->getCategory($storeId, $item['Category']);
                    //$item['Category'] = $this->getCategories($storeId, $category_ids);
                    fputcsv($fp, $item);
                }

            }
			//exit;
            fclose($fp);
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }
        return $filename;       
    }       

    // function getCategories($storeId, $categoryIds){
		// $categoryobj = $this->objectmanager->create('\Magento\Store\Model\StoreManagerInterface');
        // $root_id = $categoryobj->getStore($storeId)->getRootCategoryId();
		// $category = $this->objectmanager->create('Magento\Catalog\Model\ResourceModel\Category\Collection');
		// $category->addAttributeToSelect('*');
		// $category->addAttributeToFilter('entity_id',array('in' =>$categoryIds));
		// $catagory_name=array();
		// foreach($category as $categories){ 
			// //$catagory_name[]=$categories->getName();
			// if($categories->getParentCategory()->getId()!=$root_id){
				// $catagory_name[]=$categories->getParentCategory()->getName()."<<". $categories->getName();
			// }else{ 
				// $catagory_name[]=$categories->getName();
			// }
		// }
		// $catagoryName=implode(',',$catagory_name);
        // return $catagoryName;
        
    // }    
	function getCategory($storeId, $categoryIds){

        if (!isset($this->catCache)){
            $this->catCache = [];
            $categoryobj = $this->objectmanager->create('\Magento\Store\Model\StoreManagerInterface');
            $rootCat = $categoryobj->getStore($storeId)->getRootCategoryId();
            $this->getChildCategories('',$rootCat);
        }
    
        $catDes= '';
    
        if ($categoryIds){
            $cats = explode(',',$categoryIds); 
            foreach ($cats as $catId){
                if (array_key_exists($catId, $this->catCache)){
                    $catDes .= $this->catCache[$catId];                 
                }
            }           
        }
		
        if ($catDes == '' ){
            $catDes = $categoryIds;
        }
        return $catDes;
        
    }   

     function getChildCategories($prefix, $categoryId){
        $parent = $this->category->load($categoryId);
        $children = $parent->getChildrenCategoriesWithInactive();
        if ($prefix != ''){
            $prefix .= ' > ';
        }
        foreach ($children as $category) {        
            $this->catCache[$category->getId()] = $prefix.$category->getName();            
            $this->getChildCategories($prefix.$category->getName(), $category->getId());
        }
    }    

    public function writeEverythingCSV($connection, $qry_from_date, $qry_to_date, $storeId, $websiteName, $report_dir,$report_datetype, $skip_order_row,$memberNoAttributeId){
        
        $showSubsidy = true;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-partnercodeReport-writeEverythingCSV.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        ini_set('memory_limit','-1');

        try {

            $productObjIns = $this->objectmanager->get('\Magento\Catalog\Model\ProductRepository');            

            $time = time();
            $filename = 'Sales-Extract-' . $websiteName . '-' . substr($qry_from_date, 0, 10) . '-' . substr($qry_to_date, 0, 10) . '-' . $time . '.csv';
            $fp = fopen($report_dir . $filename, 'w');

            $columnHeaders = ['Row Type',
                            'Order Number',
                            'Date Created',
                            'Date Updated',

                            'Customer Name',
                            'Member Number',
                            'Customer Email',
                            'Shipping Address',

                            'Item SKU',
                            'Item Name',
                            'Item RRP',
                            'Item Price (ex. GST)',
                            'Item Price (inc. GST)',
                            'Item Qty',
                            ' ',
                            'Item Subtotal (inc. GST)'
                        ];
            if ($showSubsidy) {
                array_push($columnHeaders, 
                          'Item Subsidy', 
                          'Subsidy Subtotal', 
                          'VIP Subsidy', 
                          'VIP Subsidy Subtotal', 
                          'Member Profit', 
                          'Member Profit Subtotal'
                          );
            }
                array_push($columnHeaders,
                           'Order Subtotal (ex. GST)',
                           'Postage (ex. GST)',
                           'Surcharge (ex. GST)',
                           'GST',
                           'Order Total (inc. GST)'
                          );

            if ($storeId == 0) {
                array_unshift($columnHeaders, "Store Name");
            }

            $line = '"' . implode('","', $columnHeaders) . '"' . "\n";
            fwrite($fp, $line);

            $order_sql = "SELECT
                ord.entity_id                   as entity_id,
                bill.fax                        as Alt_Member_No,                               
                ord.store_id                    as Store_Id,
                'Order'                         as Row_type,
                ord.increment_id                as Order_Number,                                
                ord.created_at                  as Create_Date,
                ord.updated_at                  as Update_Date,                   
                concat(shp.firstname,' ',shp.lastname) as Member_Name,
                concat(bill.firstname,' ',bill.lastname) as BillingMember_Name,  
                (select value FROM customer_entity_varchar mbrno 
                    WHERE mbrno.entity_id = ord.customer_id 
                    AND mbrno.attribute_id = " . $memberNoAttributeId . ") as Member_No,                          
                ord.customer_email              as Email,               
                concat(shp.street,', ',shp.city,', ',shp.region,' ',shp.postcode) as Member_Address,
                concat(bill.street,', ',bill.city,', ',bill.region,' ',bill.postcode) as BillingMember_Address,    
                ' ' as SKU,
                ' ' as Product_Name,
                ' ' as RRP,
                ' ' as Unit_Price,
                ' ' as Unit_Price_Inc,              
                ' ' as Qty,
                ' ' as Item_Subtotal,
                ' ' as Item_Subtotal_Inc,";

            if ($showSubsidy) {
                $order_sql .= "
                ' ' as Item_Subsidy,
                ' ' as Item_Subsidy_Subtotal,
                ' ' as Item_VIP_Subsidy,                
                ' ' as Item_VIP_Subsidy_Subtotal,
                ' ' as Item_Member_Profit,
                ' ' as Item_Member_Profit_Subtotal,                             
            ";
            }

            /* @Ramesh add "fooman_surcharge_amount_invoiced  as Surcharge" after shipping_amount after "shipping_amount" in below query */

            $order_sql .= "                                   
                subtotal                            as Subtotal,
                shipping_amount                     as Postage,
                foomanorder.amount_invoiced	        as Surcharge, 				                               
                foomanorder.tax_amount              as GST,
                total_invoiced                      as OrderTotal
                
                FROM sales_order ord 
                LEFT JOIN sales_order_address shp ON shp.parent_id = ord.entity_id AND shp.address_type='shipping'         
                LEFT JOIN sales_order_address bill ON bill.parent_id = ord.entity_id AND bill.address_type='billing'           
                LEFT JOIN fooman_totals_order foomanorder ON	foomanorder.order_id = ord.entity_id
                WHERE ord.status = 'complete'";

                if($storeId != 0){
                    $order_sql .= "and (ord.store_id = " . $storeId . ")";
                }


            switch ($report_datetype) {
                case 'created':
                    $order_sql .= " and (ord.created_at >= '" . $qry_from_date . "' and ord.created_at <='" . $qry_to_date . "')";
                    break;

                case 'updated':
                    $order_sql .= " and  (ord.updated_at >= '" . $qry_from_date . "' and ord.updated_at <='" . $qry_to_date . "')";
                    break;

                default:
                    $order_sql .= " and (
                    (ord.updated_at >= '" . $qry_from_date . "' and ord.updated_at <='" . $qry_to_date . "')
                        or 
                    (ord.created_at >= '" . $qry_from_date . "' and ord.created_at <='" . $qry_to_date . "')
                )";

            }
            //echo $order_sql; exit;
            $logger->info($order_sql);
            $orders = $connection->query($order_sql);

            while ($order = $orders->fetch()) {
               
                //$logger->info($order);
                $order_row = array_slice($order, 3);
                $entity_id = $order['entity_id'];
                $order_number = $order['Order_Number'];

                $perth_create_Dates = date('Y-m-d H:i:s', strtotime($order_row['Create_Date'] . '+8 hours'));
                $perth_update_Dates = date('Y-m-d H:i:s', strtotime($order_row['Update_Date'] . '+8 hours'));
                $order_row['Create_Date'] = $perth_create_Dates;
                $order_row['Update_Date'] = $perth_update_Dates;
                if ($order['Store_Id'] == 35) {
                    $order_row['Member_No'] = $order['Alt_Member_No'];
                }
                //$storeName = Mage::app()->getStore($order['Store_Id'])->getName();

                $storeName = $this->store->load($order['Store_Id'])->getName();

                
                if (strtotime($order['Create_Date']) < strtotime('2016-01-01')) {

                    $item_sql = "SELECT sum(itm.tax_amount) as incorrectGST
                                FROM sales_order_item itm 
                                WHERE itm.order_id = " . $entity_id . "
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
                    $items = $connection->query($item_sql);
                    if ($item = $items->fetch()) {

                        $incorrectGST = $item['incorrectGST'];

                        $order_row['GST'] = $order_row['GST'] - $incorrectGST;
                        $order_row['Subtotal'] = $order_row['Subtotal'] + $incorrectGST;
                    }
                }

                if ($skip_order_row == false) {

                    if ($storeId == 0) {
                        array_unshift($order_row, $storeName);
                    }

                    fputcsv($fp, $order_row);
                }

                $item_sql = "SELECT
                        'Item'                          as Row_type,
                        '" . $order_number . "' as Order_Number,                        
                        ' ' as Create_Date,
                        ' ' as Update_Date,                     
                        ' ' as Member_Name,
                        ' ' as Member_No,
                        ' ' as Email,
                        ' ' as Member_Address,
                        itm.sku                         as SKU, 
                        itm.name                        as Product_Name,
                        
                        (select value from catalog_product_entity_decimal prd
                        where prd.entity_id = itm.product_id and prd.attribute_id = 77 limit 1) as RRP,
                        
                        format(itm.row_total/itm.qty_ordered,2)                     as Unit_Price, 
                        format(itm.row_total_incl_tax/itm.qty_ordered,2)            as Unit_Price_Inc,                      
                        itm.qty_ordered                 as Qty_Ordered,
                        itm.row_total                   as SubTotal,
                        itm.row_total_incl_tax          as SubTotal_Inc,";


                if ($showSubsidy) {
                    $item_sql .= "
                    itm.subsidy as Item_Subsidy,
                    format(itm.subsidy * itm.qty_ordered,3) as Item_Subsidy_Subtotal,
                    itm.subsidy_vip as Item_VIP_Subsidy,
                    format(itm.subsidy_vip * itm.qty_ordered,3) as Item_VIP_Subsidy_Subtotal,
                    itm.member_profit as Item_Member_Profit,                    
                    format(itm.member_profit * itm.qty_ordered,3) as Item_Member_Profit_Subtotal,                   
                ";
                }

                $item_sql .= "              
                        ' ' as Order_Subtotal,
                        ' ' as Postage,
                        ' ' as Surcharge,
                        ' ' as GST,
                        ' ' as OrderTotal

                        FROM sales_order_item itm                                                
                        WHERE parent_item_id is NULL and itm.order_id = " . $entity_id;


                $checkOrderSubTotal = 0;
                $itemString = '';
                $rrpZero = false;
                $logger->info($item_sql);
                $items = $connection->query($item_sql);


                while ($item = $items->fetch()) {
                    
                    $product = $productObjIns->get($item['SKU'],false,$order['Store_Id'],false);
                    $member_profit = $product->getNiMemberProfit();  //exit;
                    $item['Item_Member_Profit'] = $member_profit;
                    $item['Item_Member_Profit_Subtotal'] = $member_profit * $item['Qty_Ordered'];

                    // At the beginning of their life, Woolworths and Myer cards were
                    // Incorrectly recorded as having a GST component.
                    // So we must process them differently - as the row_total will not
                    // have the GST component.

                    $sku = $item['SKU'];
                    if (strtotime($order['Create_Date']) < strtotime('2016-01-01')) {
                        if ($sku == 'WFG100M') {
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

                        } else if ($sku == 'SPEICALHARUM') {
                            $item['RRP'] = 20.00;
                            // Someone deleted "SPEICALHARUM" so the RRP won't be available :(
                        } else if ($sku == 'HLPSE') {
                            $item['RRP'] = 36.00;
                            // Someone deleted "HLPSE" so the RRP won't be available :(

                        } else if ($sku == 'DY50E') {
                            $item['RRP'] = 50.00;
                        } else if ($sku == 'DY100E') {
                            $item['RRP'] = 100.00;
                        } else if ($sku == 'SPECIALHCBCT') {
                            $item['RRP'] = 5.8;


                        } else if ($sku == 'RS100E') {
                            $item['RRP'] = 100.00;

                        }
                    }
                    if (!$item['RRP'] > 0) {
                        $item['RRP'] = 0; //$this->_getRRPAtDate($connection, $storeId, $sku, substr($order_row['Create_Date'], 0, 10));
                    }
                    if (!$item['RRP'] > 0) {
                        $item['RRP'] = $item['SubTotal'];
                        //              $rrpZero = true;
                    }

                    // include the order create date, Update Date, Member names etc
                    //$perth_create_Date=date('Y-m-d h:i:s', strtotime($order_row['Create_Date'].'+10 hours'));
                    //$perth_update_Date=date('Y-m-d h:i:s', strtotime($order_row['Update_Date'].'+10 hours'));
                    //$item['Create_Date']  = $perth_create_Date;
                    //$item['Update_Date']  = $perth_update_Date;
                    $item['Create_Date'] = $order_row['Create_Date'];
                    $item['Update_Date'] = $order_row['Update_Date'];
                    if($order_row['Member_Name'] != '') {
                        $item['Member_Name'] = $order_row['Member_Name'];
                    }else{
                        $item['Member_Name'] = $order_row['BillingMember_Name'];
                    }
                    $item['Member_No'] = $order_row['Member_No'];
                    $item['Email'] = $order_row['Email'];
                    if($order_row['Member_Address']!= '') {
                        $item['Member_Address'] = $order_row['Member_Address'];
                    }else{
                        $item['Member_Address'] = $order_row['BillingMember_Address'];
                    }
                    $item['Member_Profit'] = "12345";

                    $checkOrderSubTotal += $item['SubTotal'];
                    $itemString .= serialize($item) . '<br />';

                    if ($storeId == 0) {
                        array_unshift($item, $storeName);
                    }
                    $logger->info($item['Order_Number']);
                    $logger->info(gettype($item['Order_Number']));

                    settype($item['Order_Number'],"string");
                    $logger->info(gettype($item['Order_Number']));

                    //$logger->info($item);
                    //echo "<pre>"; print_r($item); exit;
                    fputcsv($fp, $item);
                }

                if (number_format($order_row['Subtotal'], 2) != number_format($checkOrderSubTotal, 2)
                    or $rrpZero) {
                    echo '<pre>';
                    echo "Check order: " . $order_row['Order_Number'] . '<br />';
                    echo "RRP" . $item['RRP'];
                    print_r($order_row) . '<br />';
                    echo "Items: <br />" . $itemString;
                    echo '</pre>';
                    die('');
                }

            }
            fclose($fp);
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }
        return $filename;       
    }   

    public function _getRRPAtDate($connection,$storeId, $sku, $dateToFind){
        //echo "Hello"; exit;
        $qry = "SELECT rrp FROM price_history
                WHERE store_id = ".$storeId."
                AND sku = '".$sku."'
                AND created_at <= '".$dateToFind."'
                ORDER BY created_at DESC
                LIMIT 1";
        //$conn   = Mage::getSingleton('core/resource')->getConnection('core_read');      
        $res    = $connection->query($qry);
        $rec    = $res->fetch();
        //echo "<pre>"; print_r($rec); exit;
        if ($rec){
            return $rec['rrp'];
        } else {
           //echo "Rames"; exit;
           $qry = "SELECT rrp FROM price_history
                    WHERE store_id = 0
                    AND sku = '".$sku."'
                    AND created_at <= '".$dateToFind."'
                    ORDER BY created_at DESC
                    LIMIT 1"; 
            //$conn   = Mage::getSingleton('core/resource')->getConnection('core_read');      
            $res    = $connection->query($qry);
            $rec    = $res->fetch();

            if ($rec){
                return $rec['rrp'];
            } else {
                echo ' NO PRICING DATA: '.$sku.' '.$dateToFind.'<br />';
                return 0;
            }
        }
    }
}
