<?php

   namespace Serole\Orderreport\Helper;

   use Zend_Pdf_Font;

   use Zend_Pdf_Page;

   use Zend_Pdf_Image;

   use Zend_Pdf_Color_Html;

   use Zend_Pdf_Color_GrayScale;

   require_once(BP.'/lib/reports/jpgraph/jpgraph.php');
   require_once(BP.'/lib/reports/jpgraph/jpgraph_pie.php');
   require_once(BP.'/lib/reports/jpgraph/jpgraph_pie3d.php');
   require_once(BP.'/lib/reports/ReportUtils/Report.class.php');

   class Data extends \Magento\Framework\App\Helper\AbstractHelper{

       private $order;

       private $getMediaBaseDir;

       private $store;

       private $storeConfig;

       protected $_inlineTranslation;

       protected $_transportBuilder;

       public function __construct(\Magento\Framework\App\Helper\Context $context,
                                   \Magento\Sales\Model\Order $order,
                                   \Magento\Catalog\Model\Product $product,
                                   \Magento\Catalog\Model\Category $category,
                                   \Serole\Pdf\Helper\Pdf $pdfHelper,
                                   \Magento\Framework\App\Config\ScopeConfigInterface $storeConfig,
                                   \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
                                   \Serole\Pdf\Model\Mail\TransportBuilder $transportBuilder,
                                   \Magento\Store\Model\Store $store
                                  ){
           $this->order = $order;
           $this->product = $product;
           $this->category = $category;
           $this->store = $store;
           $this->storeConfig  = $storeConfig;
           $this->pdfHelper = $pdfHelper;
           $this->_inlineTranslation = $inlineTranslation;
           $this->_transportBuilder = $transportBuilder;
           parent::__construct($context);

       }

       public function exportsalesorderPDFOrders($orderColl,$post){
         try {
             $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-report-helper-exportSalesOrders.log');
             $logger = new \Zend\Log\Logger();
             $logger->addWriter($writer);

             $record = array();
             $pdf = new \Zend_Pdf();
             $font = \Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
             $fontBold = \Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
             $page = new \Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
             $store = '';

             $mediaPath = $this->pdfHelper->getMediaBaseDir();
             $logoPath = $this->pdfHelper->getLogo('design/header/logo_src');
             $image = $mediaPath . 'logo/' . $logoPath;

             $this->drawPieChartImageInPdf($page, $image, 825, 80, 50, 30, '');

             $yy = 830 - 50;
             $yy -= 20;
             $page->setFont($fontBold, 10)->drawText("Website Sales by State", 30, $yy);
             $page->setFont($font, 8);
             if ($post['store_id']) {
                 $page->setFont($fontBold, 8);
                 $yy -= 12;
                 $page->drawText("Website", 30, $yy);
                 $page->drawText(":", 64, $yy);
                 $storeObj = $this->store->load($post['store_id']);
                 $page->setFont($font, 7)->drawText($storeObj['name'], 70, $yy);
             }
             $yy -= 12;
             $page->setFont($fontBold, 8)->drawText("Period", 30, $yy);
             $page->drawText(":", 64, $yy);
             $page->setFont($font, 7)->drawText($this->dateformat($post['from_date']) . " to " . $this->dateformat($post['to_date']), 70, $yy);


             $rr = $yy;
             $yy = $rr;

             $counttt = 0;
             $rr = $yy;
             $yy -= 2;
             $tot = 0;
             $counttt = 1;
             /*Preparing Data Start*/
             $productCategory = array();
			 $i = 0;
            foreach ($orderColl as $order) {
                 //echo "<pre>";
                 //echo $order->getId();
                 $order = $this->order->load($order->getId());
                 $orderItems = $order->getItemsCollection();
                 
                 $australiasatecode = array(
                     'Australia Capital Territory' => 'ACT',
                     'New South Wales' => 'NSW',
                     'Northern Territory' => 'N T',
                     'Queensland' => 'QLD',
                     'South Australia' => 'SA',
                     'Tasmania' => 'TAS',
                     'Victoria' => 'VIC',
                     'Western Australia' => 'W A'
                 );
                 $regcode = $order->getBillingAddress()->getRegion();
                 $regionfull = $order->getBillingAddress()->getRegion();
                 foreach ($orderItems as $k => $item) {
                     if (array_key_exists($regionfull, $australiasatecode)) {
                         $regcode = $australiasatecode[$regionfull];
                     }
                     $productCategory[$regcode][$i]['qty'] = (int)$item->getQtyOrdered();
                     $productCategory[$regcode][$i]['totalorder'] = $item->getOrderId();
                     $i++;
                 }
            }
             //echo "<pre>"; print_r($productCategory);
             /*Preparing Data End*/
             $yy -= 15;
             $page->setFont($fontBold, 8);
             $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("State", 30, $yy);
             $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Total Orders", 100, $yy);
             $page->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Total Items Sold", 170, $yy);
             $page->setFont($font, 8);
             $yy -= 4;
             $page->setFillColor(new \Zend_Pdf_Color_Html('#edebeb'))->setLineColor(new \Zend_Pdf_Color_GrayScale(0))->setLineWidth(.4)->drawLine(30, $yy, 265, $yy);
             $piearrayQty = array();
             $piearrayOrder = array();
             $pielable = array();
             //echo "<pre>";print_r($productCategory); exit;
             $logger->info($productCategory);
            foreach ($productCategory as $k => $productcategoryval) {
                 $totalqty = 0;
                 $totalorder = array();
                 foreach ($productcategoryval as $item) {
                     $totalqty += $item['qty'];
                     if (!in_array($item['totalorder'], $totalorder)) {
                         array_push($totalorder, $item['totalorder']);
                     }
                 }
                 $yy -= 15;
                 $page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($k, 32, $yy);
                 $page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText(count($totalorder), 141, $yy);
                 $page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($totalqty, 225, $yy);
                 array_push($piearrayOrder, count($totalorder)); /*Total count of orders not duplicate*/
                 array_push($piearrayQty, $totalqty); /*Total count of qty*/
                 //array_push($pielable, $k);
                 // array_push($pielable, $k." (%.1f%%)");
                 array_push($pielable, $k . "\n%.1f%%"); /* $k => returns state name as per order address */

            }
             /*$logger->info("------------------------------------");
             $logger->info($piearrayOrder);
             $logger->info("------------------------------------");
             $logger->info($piearrayQty);
             $logger->info("------------------------------------");*/
             $logger->info($pielable);
             /*   echo "<pre>"; print_r($piearrayorder);
                  print_r($piearray);
                  print_r($pielable);
                  exit;*/
             /*Item Pie Chart Image*/
             if (!empty($piearrayQty)) {
                 $imgnameitem = $this->generatepie($piearrayQty, 'totalitemsold', $pielable);
                 $this->drawPieChartImageInPdf($page, $imgnameitem, ($yy - 50), 200, 150, 350, 'remove');//Draw logo
             }

             /*Order Pie Chart Image*/
             if (!empty($piearrayQty)) {
                 $imgnameorder = $this->generatepie($piearrayOrder, 'totalordersold', $pielable);
                 $this->drawPieChartImageInPdf($page, $imgnameorder, ($yy - 50), 200, 150, 35, 'remove');///Draw logo
             }

             $pdf->pages[] = $page;
             $from = str_replace('/', '', $post['from_date']);
             $to = str_replace('/', '', $post['to_date']);
             $strfilename = 'Website_sales_by_state_' . $from . '-' . $to . '.pdf';
             $reportsFoldername = "reports";
             $reportsFolderpath = $mediaPath . 'reports/';
             if (!file_exists($reportsFolderpath)) {
                 mkdir($reportsFolderpath, 0777, true);
                 chmod($reportsFolderpath, 0777);
             }
             if (!is_writable($reportsFolderpath)) {
                 chmod($reportsFolderpath, 0777);
             }
             $outfile = $reportsFolderpath . $strfilename;
             $pdf->save($outfile);
             $file = $strfilename;
             if (file_exists($outfile)) {
                 header('Content-Description: File Transfer');
                 header('Content-Type: application/octet-stream');
                 header('Content-Disposition: attachment; filename=' . basename($file));
                 header('Content-Transfer-Encoding: binary');
                 header('Expires: 0');
                 header('Cache-Control: must-revalidate');
                 header('Pragma: public');
                 header('Content-Length: ' . filesize($outfile));
                 ob_clean();
                 flush();
                 readfile($outfile);
                 $pdf=  $pdf->render();
                 $this->sendMail($type='pdf',$contents=false,$strfilename,$post,$outfile);
                 exit;
                 /*Send Mail*/
             }
         }catch(\Exception $e){
            $logger->info($e->getMessage());
         }
   }

       public function generatepie($data,$type,$labels){
           try {
               $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-report-helper-gereratepie.log');
               $logger = new \Zend\Log\Logger();
               $logger->addWriter($writer);
               //$logger->info("test");
               $graph = new \PieGraph(400, 300);
               //$logger->info("test1");
               $theme_class = new \VividTheme;
               //$logger->info("test2");
               $graph->SetTheme($theme_class);
               //$logger->info("test3");
               // Set A title for the plot
               $mediaPath = $this->pdfHelper->getMediaBaseDir();
               //$logger->info("test4");
               $graphsFolderName = "pdfpiechart";
               //$logger->info("test5");
               $graphsFolderPath = $mediaPath . 'reports/' . $graphsFolderName;
               //$logger->info($graphsFolderPath);

               if (!file_exists($graphsFolderPath)) {
                   mkdir($graphsFolderPath, 0777, true);
                   chmod($graphsFolderPath, 0777);
               }
               if (!is_writable($graphsFolderPath)) {
                   chmod($graphsFolderPath, 0777);
               }

               if ($type == 'totalitemsold') {
                   $graph->title->Set("Total items sold by State");
                   $fileName = $graphsFolderPath . '/pie' . time() . '.png';
                   $logger->info("file Name".$fileName);
               }
               if ($type == 'totalordersold') {
                   $graph->title->Set("Total orders by State");
                   $fileName = $graphsFolderPath . '/pieorder' . time() . '.png';
                   $logger->info("file Name".$fileName);
               }

               // Create
               $p1 = new \PiePlot3D($data);
               $p1->SetLabelMargin(25);
               $graph->Add($p1);
               //$p1->SetLabelPos(0.9);
               $p1->SetLabels($labels, 0.9);
               $p1->ShowBorder();
               $p1->SetColor('black');

               $graph->Stroke($fileName);
               return $fileName;
           }catch (\Exception $e){
               $logger->info($e->getMessage());
           }
       }

       public function dateformat($date){
           //echo $date;
           //$dt = explode('/',$date);
           $dt = explode('-',$date);
           //echo "<pre>"; print_r($dt); exit;store_id
           return $dt[2].'/'.$dt[1].'/'.$dt[0];
       }


       public function drawPieChartImageInPdf(&$page,$imageoriginal,$top,$widthLimit,$heightLimit,$xLeft,$remove=''){
           //$imageoriginal ='imageurl',$top=830,$widthLimit=110,$heightLimit=63,$xLeft=55;$remove='image is deleted or not
           try {
               $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-report-helper-drawPieChartImageInPdf.log');
               $logger = new \Zend\Log\Logger();
               $logger->addWriter($writer);

               $image = Zend_Pdf_Image::imageWithPath($imageoriginal);
               $width = $image->getPixelWidth();
               $height = $image->getPixelHeight();
               $ratio = $width / $height;
               if ($ratio > 1 && $width > $widthLimit) {
                   $width = $widthLimit;
                   $height = $width / $ratio;
               } elseif ($ratio < 1 && $height > $heightLimit) {
                   $height = $heightLimit;
                   $width = $height * $ratio;
               } elseif ($ratio == 1 && $height > $heightLimit) {
                   $height = $heightLimit;
                   $width = $widthLimit;
               }

               $yTop = $top - $height;
               $yBottom = $top;
               //$xLeft = 55;
               $xRight = $xLeft + $width;
               $page->drawImage($image, $xLeft, $yTop, $xRight, $yBottom);
               if ($remove != '') {
                   @unlink($imageoriginal);
               }
           }catch (\Exception $e){
               $logger->info($e->getMessage());
           }
       }

       public function exportsalesorderCSVOrders($orders,$post){
           try {
               $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-report-helper-exportsalesorderCSVOrders.log');
               $logger = new \Zend\Log\Logger();
               $logger->addWriter($writer);

               $record = array();
               $from = str_replace('-', '', $post['from_date']);
               $to = str_replace('-', '', $post['to_date']);
               $strfilename = 'Website_sales_by_state_' . $from . '-' . $to . '.csv';
               $file = $strfilename;
               /*************** construct array************************/
               $productCategory = array();
               $productCategory = array();
               $content = '';
                $i = 0;
               foreach ($orders as $order) {
                   $order = $this->order->load($order->getId());
                   $orderItems = $order->getItemsCollection();
                  
                   foreach ($orderItems as $k => $item) {
                       $productCategory[$order->getBillingAddress()->getRegion()][$i]['qty'] = (int)$item->getQtyOrdered();
                       $productCategory[$order->getBillingAddress()->getRegion()][$i]['totalorder'] = $item->getOrderId();
                       $i++;
                   }
               }

               /************** End *******************************/
               foreach ($productCategory as $k => $productcategoryval) {
                   $totalqty = 0;
                   $totalorder = array();
                   foreach ($productcategoryval as $item) {
                       $totalqty += $item['qty'];
                       if (!in_array($item['totalorder'], $totalorder)) {
                           array_push($totalorder, $item['totalorder']);
                       }
                   }
                   $content .= "\"" . $k . "\"," . count($totalorder) . "," . $totalqty . "\n";

               }
               $contents = $content;
               header('Content-type: application/ms-excel');
               header('Content-Disposition: attachment; filename=' . $file);
               echo $contents;
              $this->sendMail($type='csv',$contents,$strfilename,$post,$pdf=false);
           }catch (\Exception $e){
               $logger->info($e->getMessage());
           }

       }

        public function sendMail($type,$contents,$strfilename,$post,$pdf){

           try {
               $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-order-report-helper-sendMail.log');
               $logger = new \Zend\Log\Logger();
               $logger->addWriter($writer);

               $mail = new \Zend_Mail();
               if ($type == 'csv') {
                   $attachment = $mail->createAttachment($contents);
                   $attachment->type = 'application/csv';
               } else {
                   if ($pdf) {
                      //$attachment = $mail->createAttachment($pdf);
                    $attachment = $mail->createAttachment(file_get_contents($pdf));
                      $attachment->type = 'application/pdf';
                   }
               }
               $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
               $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
               $attachment->filename = $strfilename;
               $storeEmail = $this->getStoreEmail('trans_email/ident_custom1/name');
               $logger->info("Store email".$storeEmail);
               // set basic email data including sender, receiver, message and subject line
               $html = "Hello, " . $storeEmail . "<br /><br />Please find attached your subsidy invoice for the period " . $post['from_date'] . " to " . $post['to_date'] . ".<br />
                        If you have any queries about this website orders, please call us on 01 1234 1234.<br />
                        Alternatively send an email to info@neatideas.com.au<br /><br />
                        Kind Regards,<br />
                        The Neat Ideas Team";
               $mail->setBodyHtml($html);
               $mail->setFrom('info@neatideas.com.au', 'The Sender');

               $to_email = $this->getStoreEmail('trans_email/ident_custom1/email'); //trans_email_ident_custom1_email
               $logger->info("to email".$to_email);
               $to_name = $this->getStoreEmail('trans_email/ident_custom1/name'); // trans_email_ident_custom1_name
               $logger->info("to name".$to_name);

               $mail->addTo($to_email, $to_name);
               $logger->info("Before Sub");
               $mail->setSubject('Website orders from Neat Ideas');
               $logger->info("Before send");
               $mail->send();
               $logger->info("email Sent");
               exit;
           }catch (\Exception $e){
               $logger->info($e->getMessage());
           }

       }

       public function getStoreEmail($path){
           return $this->storeConfig->getValue(
               $path,
               \Magento\Store\Model\ScopeInterface::SCOPE_STORE
           );
       }

   }