<?php

  namespace Serole\Generateordercsv\Cron;

  class Exportcsv{

      protected $helpData;

      protected $order;

      protected $storeManager;

      protected $scopeConfig;

      protected $_escaper;

      protected $inlineTranslation;

      protected $_transportBuilder;

      protected $varienIoSftp;

      protected $varienIoFile;

      protected $varienIoFtp;


      public function __construct(\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
                                  \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
                                  \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                  \Magento\Store\Model\StoreManagerInterface $storeManager,
                                  \Serole\Generateordercsv\Filesystem\Io\Sftp $varienIoSftp,
                                  \Magento\Framework\Filesystem\Io\Ftp $varienIoFtp,
                                  \Magento\Framework\Filesystem\Io\File $varienIoFile,
                                  \Magento\Framework\Escaper $escaper
      ) {
          $this->_transportBuilder = $transportBuilder;
          $this->inlineTranslation = $inlineTranslation;
          $this->scopeConfig = $scopeConfig;
          $this->storeManager = $storeManager;
          $this->_escaper = $escaper;
          $this->varienIoSftp = $varienIoSftp;
          $this->varienIoFtp = $varienIoFtp;
          $this->varienIoFile = $varienIoFile;
      }

      public function execute(){
          $this->racvExportCsv();
      }

      private function racvExportCsv(){

          $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
          $helperData = $objectManager->create('Serole\HelpData\Helper\Data');

          $customerAttr = $objectManager->create('\Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection');
          $customerAttrData = $customerAttr->addFieldToFilter('attribute_code','memberno');
          $customerAttrId = $customerAttrData->getFirstItem();

          $sender = array('name' => $helperData->getStoreEmail('trans_email/ident_sales/name'),
                          'email' => $helperData->getStoreEmail('trans_email/ident_sales/email'));

          $toEmail = $helperData->getConfigValues('generateorderscsv/general/toemail');
          $toName = $helperData->getConfigValues('generateorderscsv/general/toname');
          $toSubject = $helperData->getConfigValues('generateorderscsv/general/tosubject');

          if(!$toEmail || !$toName || $toSubject){
              $this->adminMailSend($sender);
              throw new \Exception(
                  sprintf("Setting are empty please go to the generate order CSV cron job setting and please update all fields")
              );
          }

          $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
          $connection = $resource->getConnection();
          $orderTableName = $resource->getTableName('sales_order');
          $orderItemTableName = $resource->getTableName('sales_order_item');

          //$write = Mage::getSingleton('core/resource')->getConnection('core_write');
          $currentdt =explode("-",date('Y-m-d'));

          $sql = "SELECT distinct entity_id 
                  from ".$orderTableName." as ms 
                  left join ".$orderItemTableName." msf on ms.entity_id = msf.order_id 
                  WHERE msf.sftpSentDate like '%1979-01-01%' and ms.store_id=10";

          $readresult=$connection->query($sql);
          //$strfilename = 'FAMRACOS_'.$currentdt[0].$currentdt[1].$currentdt[2].'_001.csv';
          $strfilename = 'EXTNEAT_'.$currentdt[0].$currentdt[1].$currentdt[2].'_001.csv';
          $dir = $helperData->getBaseDir();
          //$fileDir = $dir."/pub/reports/racv/";

          if(!file_exists($dir."/pub/reports/racv/")){
              mkdir($dir."/pub/reports/racv/",0777,true);
              chmod($dir."/pub/reports/racv/",0777);
          }
          if(!is_writable($dir."/pub/reports/racv/")){
              chmod($dir."/pub/reports/racv/",0777);
          }

          $filename = $dir."/pub/reports/racv/".$strfilename;
          $handle = fopen($filename, "w");
          $orderArr = array();
          $orderItemArr = array();
          $content="";
          $content .="\"HEADER\",\"EXTNEAT\",\"FILE\"\n\"HEADER\",\"PARTNER\"\n\"030\",\"X\",\"EXTNEAT\"\n\"FOOTER\",\"PARTNER\",\"1\"\n\"HEADER\",\"TXNREC\"\n";
          $totcount = 0;
          while ($row1 = $readresult->fetch() ) {
              $orderId = $row1['entity_id'];
              $orderrec = $helperData->getOrder($orderId);
              $orderdata = $orderrec->getData();

              $orderUpdated = $orderdata['updated_at'];
              $createdt =explode(" ",$orderUpdated);
              $createy =explode("-",$createdt[0]);
              $createT =explode(":",$createdt[1]);
              $timestamp = $createy[0]."".$createy[1]."".$createy[2]."".$createT[0].$createT[1].$createT[2]." ";

              $customerId = $orderdata['customer_id'];
              $customerVarcharTable = $resource->getTableName('customer_entity_varchar');
              $readresult1=$connection->query("SELECT value FROM ".$customerVarcharTable." WHERE entity_id = '".$customerId."' and attribute_id=$customerAttrId");
              $row2 = $readresult1->fetch();
              $memberno = $row2['value'];
              $items = $orderrec->getAllItems();
              foreach ($items as $itemId => $item) {
                  $orderItemVal = '';
                  $itemId = $item->getData('item_id');
                  $orderItemVal = $orderId.'-'.$itemId;

                  $sftpSentDate = $item->getData('sftpSentDate');
                  $sku = $item->getSku();
                  $proId = $item->getData('product_id');
                  //$model = Mage::getModel('catalog/product');
                  $_product = $helperData->getProduct($proId);
                  //$_product = $model->load($proId);

                  $desc =   substr(strip_tags($_product->getName()), 0, 49);
                  if($item->getData('qty_ordered') != '') {
                      $qty = number_format($item->getData('qty_ordered'));
                  } else {
                      $qty = number_format($item->getData('qty_invoiced'));
                  }

                  $price = number_format($_product->getPrice(),2);
                  $price =  str_replace(".","",$price);

                  $special_price = $item->getOriginalPrice();
                  $product_price = $_product->getPrice();
                  if ($product_price > $special_price && $product_price > 0) {
                      $discount=$product_price-$special_price;
                  } else {
                      $discount=0;
                  }
                  $discount = number_format($discount,2);
                  $discount =  str_replace(".","",$discount).'$';

                  $store = 'RACV';
                  if($sftpSentDate == '1979-01-01 00:00:00') {
                      if (!in_array($orderItemVal, $orderItemArr)) {
                          array_push($orderItemArr,$orderItemVal);
                      }
                      $content .="055,A,".$timestamp.",\"".$memberno."\",\"".$sku."\",\"".$desc."\",".$qty.",".$price.",".$discount.",\"\",,,,\"".$store."\",,,,\n";
                      $totcount++;
                  }
              }
          }
          $content .="\"FOOTER\",\"TXNREC\",\"".$totcount."\"\n\"FOOTER\",\"EXTNEAT\",\"FILE\"\n";
          $contents = $content ;
          fwrite($handle, $contents);
          fclose($handle);

          //create the sftp object
          //$sftpExport = new \Varien_Io_Sftp();

          /*FTP Connection*/

              $sftpExport = $this->varienIoFtp;
              $open = $sftpExport->open(
                  array('host' => 'vaibhavreddymarriagebureau.com',
                      'user' => 'ramesh@vaibhavreddymarriagebureau.com',
                      'password' => 'Vvh194!)',
                      'port' => '21',
                      'ssl' => true,
                      'passive' => true
                  )
              );
           //echo $open; exit;

           /* SFTP connection */
         /*  $sftpExport = $this->varienIoSftp;
           $open =  $sftpExport->open(
                                      array('host' => 'dev.pennantsportswear.com',
                                          'user' => 'ubuntu',
                                          'username' => 'ubuntu',
                                          'password' => 'pennant123$',
                                          'port' => '22',
                                          'ssl' => true,
                                          'passive' => true
                                      )
                                  );*/
          $baseDir = $helperData->getBaseDir();
          $varDir = $baseDir.'/'.'pub';
          $timeOfImport = date('jmY_his');
          $importReadyDir = $varDir.'/'.'import_ready';
          $exportReadyDir = $varDir.'/'.'export';

          $_fileToExportRemote = $strfilename;
          $_fileToImportLocal = $importReadyDir.'/'.$timeOfImport.$strfilename;
          $_fileToExportLocal = $exportReadyDir.'/'.$strfilename; //exit;

          if(!file_exists($importReadyDir)){
              mkdir($importReadyDir,0777,true);
              chmod($importReadyDir,0777);
          }

          if(!is_writable($importReadyDir)){
              chmod($importReadyDir,0777);
          }
          //echo $exportReadyDir; exit;
          if(!file_exists($exportReadyDir)){
              mkdir($exportReadyDir,0777,true);
              chmod($exportReadyDir,0777);
          }

          if(!is_writable($exportReadyDir)){
              chmod($exportReadyDir,0777);
          }

          $exportReadyDir = $varDir.'/'.'export'.'/'.$timeOfImport;
          //$file = new Varien_Io_File();

          $file = $this->varienIoFile;
          $file->write($_fileToExportLocal, $contents);

          //$emailTemplate  = Mage::getModel('core/email_template')->load(6);
          //$emailTemplate->setSenderName($this->helpData->getStoreEmail('trans_email/ident_sales/name'));
          //$emailTemplate->setSenderEmail($this->helpData->getStoreEmail('trans_email/ident_sales/email'));
          /*$sender =  [
                          'name' => $helperData->getStoreEmail('trans_email/ident_sales/name'),
                          'email' => $helperData->getStoreEmail('trans_email/ident_sales/email'),
                      ];*/

          $fileLocation = $varDir."/reports/racv/".$strfilename;
          $sftpExport->cd('test');
          $_fileToExportRemoteTmp = $sftpExport->write($_fileToExportRemote, $contents);
          if($_fileToExportRemoteTmp){
              $this->sendMail($fileLocation,$strfilename,$sender);
              $ordercount = count($orderItemArr);
              for($i=0;$i<$ordercount;$i++) {
                  $orders1 = explode("-",$orderItemArr[$i]);
                  $orderId1 = $orders1[0];
                  $orderItemId1 = $orders1[1];
                  $resource->query("UPDATE sales_order_item SET sftpSentDate=now() WHERE item_id ='".$orderItemId1."' and order_id ='".$orderId1."'");
              }
          }

      }

      public function sendMail($file,$strfilename,$sender){

          try {
              $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Orderreport-exportcsv-cronjob-sendMail.log');
              $logger = new \Zend\Log\Logger();
              $logger->addWriter($writer);

              $mail = new \Zend_Mail();
              $attachment = $mail->createAttachment(file_get_contents($file));
              $attachment->type = 'application/csv';

              $attachment->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
              $attachment->encoding = \Zend_Mime::ENCODING_BASE64;
              $attachment->filename = $strfilename;
              $html = "Hello";
              $mail->setBodyHtml($html);
              $mail->setFrom($sender['email'], $sender['name']);

              $to_email = 'iamramesh.a@gmail.com';
              $to_name = "Ramesh Allamsetti";
              $mail->addTo($to_email, $to_name);
              $mail->setSubject('RACV order SFTP Csv File');
              $mail->send();
          }catch (\Exception $e){
              $logger->info($e->getMessage());
          }

      }

      public function adminMailSend($sender){
          $mail = new \Zend_Mail();
          $mail->setBodyHtml("Setting are empty please go to the generate order CSV cron job setting and please update all fields");
          $mail->setFrom($sender['email'],$sender['name']);
          $mail->addTo($sender['email'],$sender['name']);
          $mail->setSubject('Alert Mail regariding Generate Order CSV Cron Job Setting');
          $mail->send();
      }
  }