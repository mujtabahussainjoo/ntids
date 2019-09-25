<?php

   namespace Serole\HelpData\Helper;

   use Magento\Framework\App\Helper\Context;

   class Data extends \Magento\Framework\App\Helper\AbstractHelper{

       protected $filesystem;

       protected $order;

       protected $customer;

       protected $product;

       protected $eavAttribute;

       protected $storeConfig;

       protected $transport;

       public function __construct(Context $context,
                                   \Magento\Framework\Filesystem\DirectoryList $filesystem,
                                   \Magento\Sales\Model\Order $order,
                                   \Magento\Customer\Model\Customer $customer,
                                   \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
                                   \Magento\Framework\App\Config\ScopeConfigInterface $storeConfig,
                                   \Serole\Pdf\Model\Mail\TransportBuilder $transport,
                                   \Magento\Catalog\Model\Product $product){
           parent::__construct($context);
           $this->filesystem = $filesystem;
           $this->order      = $order;
           $this->customer   = $customer;
           $this->product    = $product;
           $this->eavAttribute = $eavAttribute;
           $this->storeConfig = $storeConfig;
           $this->pdfTransport = $transport;
       }

       public function isCoreConfigEnabled($path,$scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT){
           return $this->scopeConfig->isSetFlag(
               $path,
               $scope
           );
       }

       public function getBaseDir(){
           return $this->filesystem->getRoot();
       }


       public function getMediaBaseDir(){
           return $this->filesystem->getPath('media');
       }

       public function getOrder($id){
           return $this->order->load($id);
       }

       public function getProduct($id){
          return $this->product->load($id);
       }

       public function getCustomer(){

       }

       public function getProductIdBySku($sku){
           return $this->product->getIdBySku($sku);
       }

       public function getStoreConfigValue($path,$scopeId){
           return $this->storeConfig->getValue($path,
                                     \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                              $scopeId);
       }

       public function getStoreEmail($path){
           return $this->scopeConfig->getValue(
               $path,
               \Magento\Store\Model\ScopeInterface::SCOPE_STORE
           );
       }

       public function getConfigValues($path){
           return $this->scopeConfig->getValue(
               $path,
               \Magento\Store\Model\ScopeInterface::SCOPE_STORE
           );

       }

       public function getAttributeId($type,$code){
           return $this->eavAttribute->getIdByCode($type,$code);
       }

       public function sendPdfAttachentMail($templateId,$fromDetail,$toDetails,$storeId,$templateParams,$filePath,$requestMethodName,$fileType,$pdfFileName){

           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/global-helper-pdfMail.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           $logger->info("------------------------".$requestMethodName."-----------------------");

           try {
                   $status = '';
                   if (empty($templateId)) {
                       $templateId = 1;
                   }
                   if (empty($storeId)) {
                       $storeId = 1;
                   }
                  if ($filePath && $pdfFileName && $fileType) {
                       if(file_exists($filePath)) {
                           $transportBuilder = $this->Transport;
                           $transportBuilder->setTemplateIdentifier($templateId)
                               ->setTemplateOptions(
                                   [
                                       'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                       'store' => $storeId,
                                   ]
                               )
                               ->setTemplateVars($templateParams)
                               ->setFrom([
                                   'name' => $fromDetail['name'],
                                   'email' => $fromDetail['email']
                               ])
                               ->addTo($toDetails['email'], $toDetails['name'])
                               ->addAttachment(file_get_contents($filePath),$pdfFileName,$fileType); //Attachment goes here.
                           $transport = $transportBuilder->getTransport();
                       }else{
                           $status = false;
                       }
                   } else {
                       $transportBuilder = $this->Transport;
                       $transportBuilder->setTemplateIdentifier($templateId)
                           ->setTemplateOptions(
                               [
                                   'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                   'store' => $storeId,
                               ]
                           )
                           ->setTemplateVars($templateParams)
                           ->setFrom([
                               'name' => $fromDetail['name'],
                               'email' => $fromDetail['email']
                           ])
                           ->addTo($toDetails['email'], $toDetails['name']);
                       //->addAttachment(file_get_contents($filePath)); //Attachment goes here.
                       $transport = $transportBuilder->getTransport();
                   }
                   if ($transport->sendMessage()) {
                       $status = true;
                   } else {
                       $status = false;
                   }


           }catch (\Exception $e){
              $logger->info($e->getMessage());
               $status = false;
           }
         return $status;
       }


       public function sendCsvAttachentMail($templateId,$fromDetail,$toDetails,$storeId,$templateParams,$filePath,$requestMethodName,$fileType,$attachFileName){

           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/global-helper-csvMail.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           $logger->info("------------------------".$requestMethodName."-----------------------");

           try {
               $status = '';
               if (empty($templateId)) {
                   $templateId = 1;
               }
               if (empty($storeId)) {
                   $storeId = 1;
               }
               if ($filePath) {
                   if(file_exists($filePath)) {
                       $transportBuilder = $this->Transport;
                       $transportBuilder->setTemplateIdentifier($templateId)
                           ->setTemplateOptions(
                               [
                                   'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                   'store' => $storeId,
                               ]
                           )
                           ->setTemplateVars($templateParams)
                           ->setFrom([
                               'name' => $fromDetail['name'],
                               'email' => $fromDetail['email']
                           ])
                           ->addTo($toDetails['email'], $toDetails['name'])
                           ->addAttachment(file_get_contents($filePath),$attachFileName,$fileType); //Attachment goes here.
                       $transport = $transportBuilder->getTransport();
                   }else{
                       $status = false;
                   }
               } else {
                   $transportBuilder = $this->Transport;
                   $transportBuilder->setTemplateIdentifier($templateId)
                       ->setTemplateOptions(
                           [
                               'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                               'store' => $storeId,
                           ]
                       )
                       ->setTemplateVars($templateParams)
                       ->setFrom([
                           'name' => $fromDetail['name'],
                           'email' => $fromDetail['email']
                       ])
                       ->addTo($toDetails['email'], $toDetails['name']);
                   //->addAttachment(file_get_contents($filePath)); //Attachment goes here.
                   $transport = $transportBuilder->getTransport();
               }
               if ($transport->sendMessage()) {
                   $status = true;
               } else {
                   $status = false;
               }


           }catch (\Exception $e){
               $logger->info($e->getMessage());
               $status = false;
           }
           return $status;
       }
   }

