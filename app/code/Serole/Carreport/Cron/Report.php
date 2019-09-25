<?php

 namespace Serole\Carreport\Cron;


class Report {

    protected $product;

    protected $carReport;

    protected $helpData;

    protected $orderObj;

    public function __construct(\Magento\Catalog\Model\Product $product,
                                \Magento\Sales\Model\Order $orderObj,
                                \Serole\HelpData\Helper\Data $helpData,
                                \Serole\Carreport\Model\Carreport $carreport
                              ) {
       $this->product = $product;
       $this->carReport = $carreport;
       $this->helpData = $helpData;
       $this->orderObj = $orderObj;
    }

    public function execute() {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/carreport-cron-.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

      try {
          $this->checkVedaDowntime();

          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
          $connection = $resource->getConnection();
          $sqlQuery = "SELECT * from car_report_status WHERE (status like '%pending%') or (status like '%error%' and error_count <4)";
          $readresult = $connection->fetchAll($sqlQuery);
          //echo "<pre>"; print_r($readresult);//exit;
          $i = 1;
          $proArr = array();
          foreach ($readresult as $row1) {
              //echo "<pre>";print_r($row1); exit;
              $orderId = $row1['order_id'];
              $productId = $row1['product_id'];
              $vin = $row1['vin'];
              $odometer = $row1['odometer'];
              $error_count = $row1['error_count'];
              $odometer1 = '';
              if ($odometer != '') {
                  $odometer1 = '<ved1:odometer>' . $odometer . '</ved1:odometer>';
              }
              $user = 'it.live';
              $pass = 'RACWA2015';
              $serviceUrl = 'https://www.vedaauto.com/VedaAutoServices.svc?wsdl';

              $client = new \SoapClient($serviceUrl, array('trace' => TRUE, "exceptions" => 0));
              $xml = "<ved:RunAutoDealerReport xmlns:ved=\"Vedaadvantage.VedaAuto\" xmlns:ved1=\"http://schemas.datacontract.org/2004/07/Vedaadvantage.AutoBureau.Common\">
                        <ved:request>
                            <ved1:ClientReference>" . $orderId . "</ved1:ClientReference>
                            <ved1:Password>" . $pass . "</ved1:Password>
                            <ved1:Username>" . $user . "</ved1:Username>
                            <ved1:VIN>" . $vin . "</ved1:VIN>
                            " . $odometer1 . "
                        </ved:request>
                     </ved:RunAutoDealerReport>";

              $url = 'https://www.vedaauto.com/VedaAutoServices.svc';
              $soap_string = '<?xml version="1.0" encoding="utf-8"?>
                            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                               <soap:Body>'
                  . $xml .
                  '</soap:Body>
                            </soap:Envelope>';
              $headers = array(
                  "Content-type: text/xml;charset=\"utf-8\"",
                  "Accept: text/xml",
                  "SOAPAction: Vedaadvantage.VedaAuto/IVedaAutoServices/RunAutoDealerReport",
                  "Content-length: " . strlen($soap_string),
              );
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($ch, CURLOPT_TIMEOUT, 60);
              curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
              curl_setopt($ch, CURLOPT_FAILONERROR, true);
              curl_setopt($ch, CURLOPT_POST, true);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $soap_string);
              $responseData = curl_exec($ch);

              $logger->info("Response");
              $logger->info($responseData);
              if (curl_errno($ch)) {
                  $logger->info('CURL Error: ' . curl_error($ch));
              }
              curl_close($ch);

              // As the response being sent by the server is not actually valid xml most of the time
              // We're going to need to process this manually (sigh)

              $responsehtml0 = self::extract_value($responseData, '<a:Response>', '</a:Response>');
              $logger->info('Response HTML: ' . $responsehtml0);

              $res_status = self::extract_value($responseData, '<a:Success>', '</a:Success>');
              $logger->info('Response STATUS: ' . $res_status);

              $res_message = self::extract_value($responseData, '<a:Message>', '</a:Message>');
              $logger->info('Response MESSAGE: ' . $res_message);


              $currentDate = date('Y-m-d_His');
              $completeFileName = $orderId . '_' . $currentDate . '_' . $vin . '.html';

              $mediaDir = $this->helpData->getMediaBaseDir();
              $dir = $mediaDir . '/reports/car_history/';

              if (!file_exists($dir)) {
                  mkdir($dir, 0777, true);
                  chmod($dir, 0777);
              }
              if (!is_writable($dir)) {
                  chmod($dir, 0777);
              }

              $currentDate = date('Y-m-d_His');
              $completeFileName = $orderId . '_' . $currentDate . '_' . $vin . '.html';
              $filename = $dir . $completeFileName;
              $fp = fopen($filename, 'w') or die("Unable to open file!");

              $addHtml = '<style type="text/css">
							#bg-fade-left,#bg-fade-right,body {
								background-color:white  !important;
								background-image:none !important;
								padding:0 !important;
							}
							th {
							background-image:none !important;  
							 background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#009044), color-stop(100%,#006c33)) !important;
							 background: -webkit-linear-gradient(top, #009044 0%,#006c33 100%) !important;
							 background: -o-linear-gradient(top, #009044 0%,#006c33 100%) !important;
							 background: -ms-linear-gradient(top, #009044 0%,#006c33 100%) !important;
							 background: linear-gradient(to bottom, #009044 0%,#006c33 100%) !important;
							 color:white;
							}
							th h3 {
								white-space: nowrap !important;
							}
							#ppsr_certificate img {
								max-height: 80px !important;
							}
							#ppsr_certificate * {
								font-size: 9.5px !important;
							}
							#ppsr_certificate span.text3 {
								display:block !important;
								min-height:10px !important;
							}
							#ppsr_certificate .heading4 {
								padding-bottom:0px !important;								
							}
							#ppsr_certificate tr  {
								display:table-row !important;
							}
							#ppsr_certificate, #ppsr_certificate table,#ppsr_certificate tbody {
								width:100% !important;
							}
							#ppsr_certificate,
							#revs {
								page-break-inside: avoid !important;
							}
							#ppsr_certificate > table > tbody > tr > td {
								min-width:50% !important;
								padding-left:5px !important;
							}				
							th p,
							th p span {
								color:white !important;
								padding-left:4px !important;
							}
							table#odometer-history,
							#termsanddefinitions,
							#revs,
							#termstable
							{
								page-break-before: always !important;
							}
							#revs {
								page-break-after: always !important;
							}							
							table p {							
							    line-height: 27px !important;
							}
							table#odometer-history tbody tr + tr th p, 
							table#odometer-history tbody tr + tr td p, 
							table#insurance-history tbody tr + tr th p, 
							table#insurance-history tbody tr + tr td p, 
							table#sales-listing tbody tr + tr th p, 
							table#sales-listing tbody tr + tr td p {
								background-image: none !important;
								background: -moz-linear-gradient(top, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.3) 100%);
								background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,0.3)), color-stop(100%,rgba(255,255,255,0.3)));
								background: -webkit-linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0.3) 100%);
								background: -o-linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0.3) 100%); 
								background: -ms-linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0.3) 100%); 
								background: linear-gradient(to bottom, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0.3) 100%); 
							}
							</style></head>';

              $responsehtml = str_replace('</head>', $addHtml, $responsehtml0);

              $offset1 = strpos($responsehtml, 'id="ppsr_certificate"');
              $offset2 = strpos($responsehtml, 'This is a serial number search certificate for a serial number search', $offset1);
              $offset3 = strpos($responsehtml, '<br><br>', $offset2);

              $offset4 = strpos($responsehtml, '<div style="text-align:left; " class="heading2">', $offset3);

              if ($offset1 !== false && $offset2 !== false && $offset3 !== false && $offset4 !== false) {
                  $responsehtml = substr($responsehtml, 0, $offset3 + 8) . substr($responsehtml, $offset4);
              }

              $offset1 = strpos($responsehtml, '<p><strong>URL Link:</strong>');
              $offset2 = strpos($responsehtml, '</p>', $offset1);
              if ($offset1 !== false && $offset2 !== false) {
                  $responsehtml = substr($responsehtml, 0, $offset1) . substr($responsehtml, $offset2 + 4);
              }


              $offset1 = strpos($responsehtml, '<h3>terms and definitions</h3>');
              $offset2 = strrpos($responsehtml, '<table');
              if ($offset1 !== false && $offset2 !== false) {
                  $responsehtml = substr($responsehtml, 0, $offset2 + 6) . ' id="termstable" ' . substr($responsehtml, $offset2 + 6);
              }

              $responsehtml = str_replace('<span>Privacy and Terms and Conditions</span>',
                  '<div id="privacytitle">Privacy and Terms and Conditions</div>',
                  $responsehtml);
              $responsehtml = str_replace('width:577px;',
                  'width:577px; margin:0px auto;',
                  $responsehtml);
              $responsehtml = str_replace('border:0px solid;',
                  'border:0px solid white;',
                  $responsehtml);

              $responsehtml = str_replace('<h3>terms and definitions</h3>',
                  '<h3 id="termsanddefinitions">terms and definitions</h3>',
                  $responsehtml);

              $responsehtml = str_replace('<h3>general disclaimers</h3>',
                  '<h3 id="generaldisclaimers">general disclaimers</h3>',
                  $responsehtml);
              $responsehtml = str_replace('"page-break-before:always"',
                  '""',
                  $responsehtml);
              $responsehtml = str_replace('"page-break-before:always; "',
                  '""',
                  $responsehtml);
              fwrite($fp, $responsehtml);
              fclose($fp);

              $pdfFile = $dir . substr($completeFileName, 0, strlen($completeFileName) - 4) . 'pdf';
              $pdfFileName = substr($completeFileName, 0, strlen($completeFileName) - 4) . 'pdf';
              $cmd = 'xvfb-run wkhtmltopdf ' . $filename . ' ' . $pdfFile;
              $result1 = exec($cmd, $response);
              $logger->info($xml);

              $order = $this->orderObj->loadByIncrementId($orderId);
              $status = $order->getStatus();
              $storeid = $order->getStoreId();
              $shippingAddress = $order->getBillingAddress();
              $customerName = $order->getBillingAddress()->getFirstname().' '.$order->getBillingAddress()->getLastname();

              if ($order->getDeliveryemail()) {
                  $customerEmailAddress = $order->getDeliveryemail();

              }elseif ($order->getBillingAddress()->getEmail()){
                  $customerEmailAddress = $order->getBillingAddress()->getEmail();
              }else{
                  $customerEmailAddress = $order->getCustomerEmail();
              }
              //echo $customerEmailAddress;

              $cusId = $order->getCustomerId();
              $website_id = $order->getStoreId();
              $orderWebsiteName = $order->getStoreName();

              $logoName = $this->helpData->getStoreConfigValue('design/header/logo_src', $website_id);
              $logoimg = $mediaDir . '/logo/' . $logoName;

              if ($res_status == 'true') {
                  $sta = 'complete';
                  $errorCountNext = $error_count;
                  $templateId = '5';
                  $fromDetail['name'] = $this->helpData->getStoreConfigValue('trans_email/ident_sales/name');
                  $fromDetail['email'] = $this->helpData->getStoreConfigValue('trans_email/ident_sales/email');
                  $toDetails['name'] = $customerEmailAddress;
                  $toDetails['email'] = $customerName;
                  $storeId = $website_id;
                  $templateParams = array(
                      'prodUrlPdf' => $pdfFile,
                      'customername' => $customerName,
                      'storename' => $orderWebsiteName,
                      'orderid' => $orderId,
                      'logo_url' => $logoimg
                  );
                  $filePath = $pdfFile;
                  $requestMethodName = 'Serole-Carreport-Cron-Report-execute(method-if-trueConidition)';
                  $mailStatus = $this->helpData->sendPdfAttachentMail($templateId, $fromDetail, $toDetails, $storeId,
                      $templateParams, $filePath, $requestMethodName,$fileType='application/pdf',$pdfFileName);

              } else {
                  $sta = 'error';
                  $errorCountNext = $error_count + 1;
                  $templateId = '6';
                  $fromDetail['name'] = $this->helpData->getStoreEmail('trans_email/ident_sales/name');
                  $fromDetail['email'] = $this->helpData->getStoreEmail('trans_email/ident_sales/email');
                  $toDetails['name'] = $customerEmailAddress;
                  $toDetails['email'] = $customerName;
                  $storeId = $website_id;
                  $templateParams = array(
                      'orderid' => $orderId,
                      'logo_url' => $logoimg
                  );
                  $requestMethodName = 'Serol-Carreport-Cron-Report-execute(method-if-elseConidition)';
                  $mailStatus = $this->helpData->sendPdfAttachentMail($templateId, $fromDetail, $toDetails, $storeId,
                      $templateParams, $filePath = false, $requestMethodName,$fileType=FALSE,$pdfFileName = FALSE);
              }
              if($mailStatus == true):
                $readresultas = $connection->query("update car_report_status set updated_at = '".date("Y-m-d H:i:s")."',status = '" . $sta . "',result_msg='" . $res_message . "',error_count='" . $errorCountNext . "',result_html='" . $filename . "' WHERE order_id = '" . $orderId . "' and product_id = '" . $productId . "'");
              endif;
          }
       }catch (\Exception $e){
          $logger->info($e->getMessage());
      }

    }


    function extract_value($data, $startTag, $endTag){
        $returnString = '';
        $startPos 	= strpos($data,$startTag);
        $endPos 	= strrpos($data,$endTag,-1);
        if ($startPos!==false && $endPos!==false){
            $startPos+=strlen($startTag);
            $returnString = substr($data,$startPos,$endPos-$startPos);
            $returnString = html_entity_decode($returnString);
        }
        return $returnString;
    }

    private function checkVedaDowntime(){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/carreport-cron-product-checkVedaDowntime.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $aest = strtotime(date("Y-m-d H:i:s", strtotime('+10 hours')));
        $logger->info("Day is: ".date("w",$aest)." Time is: ".date('Hi',$aest));

        $productObj = $this->product->getCollection();
        $productObj->addAttributeToFilter('iscarreport',1);
        foreach ($productObj as $prodcutItem){
            //echo "<pre>"; print_r($prodcutItem->getData()); exit;
            if($prodcutItem->getTypeId() == 'virtual'){
                $storeId = $prodcutItem->getStoreId();
                $sku = $prodcutItem->getSku();
                $productId = $prodcutItem->getId();
                $productObjLoad = $this->product->setStoreId($storeId)->loadByAttribute('sku', $sku);

                $cur_status = $productObjLoad->getComingSoon();

                $new_status = 0;
                $new_status_text = "ENABLED";

                // Only disable product on wednesdays from 8:30pm
                if (date("w",$aest) == 3 && date('Hi',$aest) > 2029) {
                    $new_status = 1;
                    $new_status_text = "DISABLED";
                }
                if ($new_status != $cur_status){
                    $productObjLoad->getResource()->getAttributeRawValue($productId, 'comingsoon', $storeId);
                }
            }
        }
    }
}