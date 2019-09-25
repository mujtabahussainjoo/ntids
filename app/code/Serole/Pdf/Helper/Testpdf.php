<?php

namespace Serole\Pdf\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

#use Zend_Barcode;
use Zend\Barcode\Barcode;

require '/var/www/html/lib/gearpdf/vendor/autoload.php';
require_once '/var/www/html/lib/gearpdf/vendor/H2OpenXML/sourcecode/HTMLtoOpenXML.php';

class Testpdf extends \Magento\Framework\App\Helper\AbstractHelper
{

    private $mediaDirectory;

    private $filesystem;

    private $fileUploaderFactory;

    private $storeManager;

    private $backendUrl;

    protected $storeConfig;

    protected $product;

    protected $orderItemsCollection;

    protected $orderPdf;

    protected $orderItemSerialcode;

    protected $order;

    protected $customerSession;

    protected $_inlineTranslation;

    protected $_transportBuilder;

    protected $_template;

    protected $scopeConfig;

    protected $temporaryDocFilesPath;

    protected $temporaryPdfFilesPath;

    protected $mergedPdfFilesPath;

    protected $barcodeFilesPath;

    protected $orderData;

    protected $date;

    protected $errors;

    protected $helperData;

    protected $cmsBlock;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $storeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order $order,
        \Magento\Catalog\Model\Product $product,
        \Serole\Pdf\Model\Pdf $orderPdf,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Serole\Serialcode\Model\OrderitemSerialcode $orderItemSerialcode,
        \Magento\Sales\Model\Order\Item $orderItemsCollection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Serole\HelpData\Helper\Data $helperData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Cms\Model\Block $cmsBlock,
        \Serole\Pdf\Model\Mail\TransportBuilder $transportBuilder
        //\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->backendUrl = $backendUrl;
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->storeManager = $storeManager;
        $this->storeConfig  = $storeConfig;
        $this->product = $product;
        $this->orderItems = $orderItemsCollection;
        $this->orderPdf = $orderPdf;
        $this->order = $order;
        $this->orderItemSerialcode = $orderItemSerialcode;
        $this->customerSession =  $customerSession;
        //$this->scopeConfig = $scopeConfig;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->temporaryDocFilesPath = '';
        $this->temporaryPdfFilesPath = '';
        $this->mergedPdfFilesPath = '';
        $this->barcodeFilesPath = '';
        $this->ordeData = array();
        $this->date = $date;
        $this->errors = array();
        $this->helperData = $helperData;
        $this->scopeConfig = $scopeConfig;
        $this->cmsBlock = $cmsBlock;
        $this->storeId = '';
        $this->customerData = array();
        parent::__construct($context);
    }

    public function getDefaultBaseUrl(){
        return $this->storeConfig->getValue('web/secure/base_url','default');
    }

    public function remoteFileExists($pdfUrl){
        $curl = curl_init($pdfUrl);
        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);
        //do request
        $result = curl_exec($curl);
        $ret = false;
        //if request did not fail
        if ($result !== false) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($statusCode == 200) {
                $ret = true;
            }
        }
        curl_close($curl);
        return $ret;
    }

    public function generateUrl($action,$params){
        if(empty($params)){
            $url = $this->backendUrl->getUrl($action);
        }else{
            $url = $this->backendUrl->getUrl($action, $params);
        }
        return $url;
    }

    public function getChildItemSerialCodes($bundleSerialCodes,$childItemSku){
        $result = array_filter($bundleSerialCodes, function ($item) use ($childItemSku) {
            if (stripos($item['sku'], $childItemSku) !== false) {
                return true;
            }
            return false;
        });
        return $result;
    }

    public function checkBundleSerialData($serialCodeItems,$bundleSku){
        $result = array_filter($serialCodeItems, function ($item) use ($bundleSku) {
            if (stripos($item['parentsku'], $bundleSku) !== false) {
                return true;
            }
            return false;
        });
        return $result;
    }

    public function getSerialCodesByFilter($serialCodeItems,$sku,$parentsku){
        $result = array_filter($serialCodeItems, function ($item) use ($sku,$parentsku) {
            if (stripos($item['sku'], $sku) !== false) {
                if($parentsku) {
                    if (stripos($item['parentsku'], $parentsku) !== false) {
                        return true;
                    }
                }else{
                    if(!$item['parentsku']){
                        return true;
                    }
                }
            }
            return false;
        });
        return $result;
    }

    /*trans_email_ident_support_email  */
    public function getStoreEmail(){
        return $this->storeConfig->getValue(
            'trans_email/ident_support/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


    public function getBaseStoreUrl(){
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_WEB
        );
    }

    public function getRootBaseDir(){
        $path = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)
            ->getAbsolutePath();
        return $path;
    }

    public function getMediaBaseDir(){
        $path = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath();
        return $path;
    }

    public function getMediaBasePath(){
        $path = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath();
        return $path;
    }

    public function getLogo($path){
        return $this->storeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getFilePath($folderName,$fileName){
        return $this->getMediaBaseDir().$folderName.'/'.$fileName;
    }

    public function getFileUrl($folderName,$fileName){
        return  $this->getBaseStoreUrl().$folderName.'/'.$fileName;
    }

    public function sendEmailAdminProductNotExist($productSku,$orderId,$message){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/helper-sendEmailtoAdmin-ProductnotExist.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $status = '';

        $templateId = $this->helperData->getConfigValues('pdfemailtemplate/general/erroremailtemplateId');

        $emailTemplateVariables['sku'] = $productSku;
        if($message) {
            $emailTemplateVariables['message'] = $message;
        }
        $emailTemplateVariables['orderid'] = $orderId;

        if(!$templateId){
            $templateId = 74;
        }

        $this->_inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom([
                'name' => "Alert Product Does't have attachment",
                'email' => $this->getStoreEmail(),
            ])
            ->addTo($this->getStoreEmail(), 'Name Goes Here');
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            $status = TRUE;
        } catch (\Exception $e) {
            $status = FALSE;
            $logger->info("Admin Product Not Exist alert Mail issue".$e->getMessage());
        }
    }



    public function sendEmailToAdminPDFissue($productSku,$orderId,$message){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/helper-sendEmailtoAdmin-ProductnotExist.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $status = '';

        //$emailTemplateVariables['message'] = 'This is a test message.';
        //$this->getConfigValues('pdfemailtemplate/general/emailtemplateId');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $templateId = $objectManager->create('\Serole\HelpData\Helper\Data')
            ->getConfigValues('pdfemailtemplate/general/erroremailtemplateId');

        $emailTemplateVariables['sku'] = $productSku;
        if($message) {
            $emailTemplateVariables['message'] = $message;
        }
        $emailTemplateVariables['orderid'] = $orderId;

        if(!$templateId){
            $templateId = 81;
        }

        $this->_inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom([
                'name' => "Alert Product Does't have attachment",
                'email' => $this->getStoreEmail(),
            ])
            ->addTo($this->getStoreEmail(), 'Name Goes Here');
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            $status = TRUE;
        } catch (\Exception $e) {
            $status = FALSE;
            $logger->info("Admin Product Not Exist alert Mail issue".$e->getMessage());
        }
    }


    public function sendPdfToCustomerEmail($fileUrl,$data,$orderId){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/helper-sendEmail.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data);

        $status = '';

        if(isset($data['emailtemplateid']) && $data['emailtemplateid']) {
            $emailTemplateId = $data['emailtemplateid'];
        }else{
            $templateId = $this->helperData->getConfigValues('pdfemailtemplate/general/emailtemplateId');
            $emailTemplateId = $templateId;
        }

        if(!$emailTemplateId){
            $emailTemplateId = '1';
        }
        $logger->info("Email TemplateId".$emailTemplateId);

        if(!$fileUrl){
            $logger->info("Attachment File not exist.".$orderId);
            $status = FALSE;
            return $status;
        }

        if(isset($data['message']) && $data['message']){
            $emailTemplateVariables['message'] = $data['message'];
        }else{
            $emailTemplateVariables['message'] = '';
        }

        if(isset($data['toname']) && $data['toname']) {
            $emailTemplateVariables['toname'] = $data['toname'];
        }

        if(isset($data['fromname']) && $data['fromname']) {
            $emailTemplateVariables['fromname'] = $data['fromname'];
            $fromName = $data['fromname'];
        }
        else
        {
            $emailTemplateVariables['fromname'] = '';
            $fromName = 'Neat Ideas';
        }

        if(isset($data['fromemail']) && $data['fromemail']) {
            $emailTemplateVariables['fromemail'] = $data['fromemail'];
            $fromEmail = $data['fromemail'];
        }
        else
        {
            $emailTemplateVariables['fromemail'] = $this->getStoreEmail();
            $fromEmail = $this->getStoreEmail();
        }

        $pdfFileName = $orderId.'.pdf';
        $this->_inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier($emailTemplateId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom([
                'name' => "JJJJJJJ",
                'email' => $fromEmail,
            ])
            ->addTo($data['email'],$data['toname'])
            # ->addAttachment(file_get_contents($fileUrl),$pdfFileName); //Attachment goes here.
            ->addAttachment(file_get_contents($fileUrl),$pdfFileName,'application/pdf'); //Attachment goes here.
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            $status = TRUE;
        } catch (\Exception $e) {
            $status = FALSE;
            $logger->info("Email Attachment issue".$e->getMessage());
        }
        return $status;
    }

    private function getCustomerId(){
        $customerId = $this->customerSession->getCustomer()->getGroupId();
        return $customerId;
    }


    public function bulkEmails($pdfData){

        $directoryPath = $this->getRootBaseDir();
        $mediaPath = $this->getMediaBaseDir();
        $this->temporaryDocFilesPath = $directoryPath.'neatideafiles/temporryfiles/doc/';
        $this->temporaryPdfFilesPath = $directoryPath.'neatideafiles/temporryfiles/pdf/';
        $this->mergedPdfFilesPath = $directoryPath.'neatideafiles/pdf/';
        $this->barcodeFilesPath = $directoryPath.'neatideafiles/barcodeimages/';

        if (!file_exists($this->temporaryDocFilesPath)) {
            mkdir($this->temporaryDocFilesPath, 0777, true);
            chmod($this->temporaryDocFilesPath, 0777);
        }
        if (!is_writable($this->temporaryDocFilesPath)) {
            chmod($this->temporaryDocFilesPath, 0777);
        }
        if (!file_exists($this->temporaryPdfFilesPath)) {
            mkdir($this->temporaryPdfFilesPath, 0777, true);
            chmod($this->temporaryPdfFilesPath, 0777);
        }
        if (!is_writable($this->temporaryPdfFilesPath)) {
            chmod($this->temporaryPdfFilesPath, 0777);
        }

        if (!file_exists($this->barcodeFilesPath)) {
            mkdir($this->barcodeFilesPath, 0777, true);
            chmod($this->barcodeFilesPath, 0777);
        }
        if (!is_writable($this->barcodeFilesPath)) {
            chmod($this->barcodeFilesPath, 0777);
        }

        $this->orderData = $pdfData['orderData'];
        $status = $this->createPdfConcept($pdfData['sku'],$pdfData['filePath'],$pdfData['productdata']);
        return $status;
    }

    public function createPdfConcept($serialcodeData,$filesList,$productDataList){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/helper-createPDFConcept-process.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $customerData = array();

        $logoPath = $this->getLogo('design/header/logo_src');
        $logoUrl = $this->getMediaBaseDir().'logo/'.$logoPath;
        //echo "<pre>"; print_r($this->orderData); exit;

        $this->storeId = $this->orderData['store_id'] ;
        $this->customerData['name'] = $this->orderData['customer_firstname'].' '.$this->orderData['customer_lastname'] ;
        $this->customerData['email'] = $this->orderData['customer_email'];
        if(isset($this->orderData['delivery_email'])) {
            $this->customerData['email'] = $this->orderData['customeremail'];
        }

        try{
            foreach ($serialcodeData as $sku => $items) {
                foreach ($items as $item) {
                    $serialCodeItem = $item['data'];
                    $customerData['toname'] = $item['toname'];
                    $customerData['email'] = $item['email'];
                    $customerData['fromname'] = $item['fromname'];
                    $customerData['fromemail'] = $item['fromemail'];
                    $customerData['message'] = $item['message'];
                    $customerData['emailtemplateid'] = $this->orderData['emailtemplateid'];
                    $filePath = $filesList[$sku];
                    $productData = $productDataList[$sku];
                    /*
                    $customerData['toname'] = $email;
                    $customerData['email'] = $email;
                    $customerData['fromname'] = $fromname;
                    $customerData['fromemail'] = $fromemail;
                    $customerData['message'] = $message;
                    $customerData['emailtemplateid'] = $emailtemplateid;
                    */
                    $productAttributeJsonData = $productData['product_json_format']; //exit;
                    $productAttributeData = (array)json_decode($productAttributeJsonData, TRUE);

                    $docsSaveFilename = "ticket-" . $this->order['increment_id'] . '-' . trim($serialCodeItem['SerialNumber']) . "-document.docx";
                    $pdfFilename = "ticket-" . $this->order['increment_id'] . '-' . trim($serialCodeItem['SerialNumber']) . "-pdffile.pdf";
                    $barcodeSaveFileName = "barcode-" . $this->order['increment_id'] . '-item-' . trim($serialCodeItem['SerialNumber']) . ".png";

                    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($filePath);
                    foreach ($productAttributeData as $productAttributeItem) {
                        if ($productAttributeItem['type'] == 'image') {
                            if ($productAttributeItem['source'] == 'attribute') {
                                if(isset($productData[$productAttributeItem['variable']])) {
                                    $attributeVariableData = $productData[$productAttributeItem['variable']];
                                    if (!$attributeVariableData) {
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    } else {
                                        $width = 100;
                                        $height = 100;
                                        if (isset($productAttributeItem['width'])) {
                                            $width = $productAttributeItem['width'];
                                        }
                                        if (isset($productAttributeItem['height'])) {
                                            $height = $productAttributeItem['height'];
                                        }
                                        #$templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                                        $productBaseDirPath = $this->getMediaBaseDir().'catalog/product';
                                        $productImagePath = $productBaseDirPath.$attributeVariableData;
                                        if(file_exists($productImagePath)){
                                            $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $productImagePath, 'size' => array($width, $height)));
                                        }else{
                                            $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                        }
                                        # Set Image Here
                                    }
                                }else{
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }
                            } elseif ($productAttributeItem['source'] == 'customOption') {
                                if (isset($this->orderData['product_options']['options'])) {
                                    $width = 100;
                                    $height = 100;
                                    if (isset($productAttributeItem['width'])) {
                                        $width = $productAttributeItem['width'];
                                    }
                                    if (isset($productAttributeItem['height'])) {
                                        $height = $productAttributeItem['height'];
                                    }
                                    $orderItemCustomOptions = $this->orderData['product_options']['options'];
                                    $key = array_search($productAttributeItem['variable'], array_column($orderItemCustomOptions, 'label'));
                                    if (is_numeric($key)) {
                                        $customOptionData = json_decode($orderItemCustomOptions[$key]['option_value'], true);
                                        $customOptionImagepath = $customOptionData['fullpath'];
                                        if (file_exists($customOptionImagepath)) {
                                            $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $customOptionImagepath, 'size' => array($width, $height)));
                                        }
                                    } else {
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    }
                                }
                            } elseif ($productAttributeItem['source'] == 'logo') {
                                $width = 150;
                                $height = 150;
                                if (isset($productAttributeItem['width'])) {
                                    $width = $productAttributeItem['width'];
                                }
                                if (isset($productAttributeItem['height'])) {
                                    $height = $productAttributeItem['height'];
                                }
                                $mediaPath = $this->getMediaBaseDir();
                                $scopeConfig = $this->scopeConfig;
                                $configPath = "design/header/logo_src";
                                $filename = $scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                                $logger->info("Logo image name" . $filename);
                                $imagePath = $mediaPath . 'logo/' . $filename;
                                //echo $imagePath;
                                $logger->info("Image Path" . $imagePath);
                                if (file_exists($imagePath)) {
                                    $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $imagePath, 'size' => array($width, $height)));
                                } else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }
                            }
                        } elseif ($productAttributeItem['type'] == 'text') {
                            if ($productAttributeItem['source'] == 'attribute') {
                                if(isset($productData[$productAttributeItem['variable']])){
                                    $attributeVariableData = $productData[$productAttributeItem['variable']];
                                    if (!$attributeVariableData) {
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    } else {
                                        if($productAttributeItem['variable'] == 'short_description' || $productAttributeItem['variable'] == 'description' || $productAttributeItem['variable'] == 'short_description_pdf'){
                                            $logger->info($attributeVariableData);
                                            $toOpenXML = \HTMLtoOpenXML::getInstance()->fromHTML($attributeVariableData);
                                            $logger->info($toOpenXML);
                                            $templateProcessor->setValue($productAttributeItem['variable'], $toOpenXML);
                                        }else {
                                            $templateProcessor->setValue($productAttributeItem['variable'], str_replace('&', '&amp;', $attributeVariableData));
                                        }
                                        //$templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                                    }
                                }else{
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }

                            } elseif ($productAttributeItem['source'] == 'serialCode') {
                                $serialCodeNumber = trim($serialCodeItem[$productAttributeItem['variable']]);
                                $attributeVariableData = $serialCodeNumber;
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                } else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                                }
                            } elseif ($productAttributeItem['source'] == 'customOption') {
                                /*$itemData is the order Data*/
                                if (isset($this->orderData['product_options']['options'])) {
                                    $orderItemCustomOptions = $this->orderData['product_options']['options'];
                                    $key = array_search($productAttributeItem['variable'], array_column($orderItemCustomOptions, 'label'));
                                    if (is_numeric($key)) {
                                        $customOptionVariableData = $orderItemCustomOptions[$key]['value'];
                                        $templateProcessor->setValue($productAttributeItem['variable'], $customOptionVariableData);
                                    } else {
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    }
                                } else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }
                            } elseif ($productAttributeItem['source'] == 'orderData') {
                                if(isset($this->orderData[$productAttributeItem['variable']])) {
                                    $attributeVariableData = $this->orderData[$productAttributeItem['variable']];
                                    if (!$attributeVariableData) {
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    } else {
                                        $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                                    }
                                }
                            }elseif ($productAttributeItem['source'] == 'block'){
                                if(isset($productAttributeItem['id'])){
                                    $cmsBlockObj = $this->cmsBlock->setStoreId($this->storeId)->load($productAttributeItem['id']);
                                    $cmsBlockData = $cmsBlockObj->getData();
                                    if(isset($cmsBlockData['content']) && $cmsBlockData['content']){
                                        //$content = strip_tags($cmsBlockData['content']);
                                        //$templateProcessor->setValue($productAttributeItem['variable'],$content); #$content
                                        $toOpenXML = \HTMLtoOpenXML::getInstance()->fromHTML($cmsBlockData['content']);
                                        $templateProcessor->setValue($productAttributeItem['variable'], $toOpenXML);
                                    }else{
                                        $templateProcessor->setValue($productAttributeItem['variable'],'');
                                    }
                                }
                            }elseif ($productAttributeItem['source'] == 'customer'){
                                if($productAttributeItem['variable'] == 'customername'){
                                    $attributeVariableData = $this->customerData['toname'];
                                    if (!$attributeVariableData) {
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    }else{
                                        $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                                    }
                                }elseif ($productAttributeItem['variable'] == 'customeremail'){
                                    $attributeVariableData = $this->customerData['email'];
                                    if (!$attributeVariableData) {
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    }else{
                                        $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                                    }
                                }
                            }
                        } elseif ($productAttributeItem['type'] == 'barcode') {
                            $barwidth = 100;
                            $barheight = 70;

                            $serialCodeNumber = trim($serialCodeItem['SerialNumber']);

                            if (isset($productAttributeItem['barwidth']) && $productAttributeItem['barwidth'] > 0) {
                                $barwidth = $productAttributeItem['barwidth'];
                            }
                            if (isset($productAttributeItem['barheight']) && $productAttributeItem['barheight'] > 0) {
                                $barheight = $productAttributeItem['barheight'];
                            }
                            if (isset($productAttributeItem['width']) && $productAttributeItem['width'] > 0) {
                                $docImagewidth = $productAttributeItem['width'];
                            }
                            if (isset($productAttributeItem['height']) && $productAttributeItem['height'] > 0) {
                                $docImageheight = $productAttributeItem['height'];
                            }
                            //echo "Step"; //exit;

                            $docImagewidth = 145;
                            $docImageheight = 50;

                            $barcodeFilePath = $this->barCodeCreate($barcodeSaveFileName, $serialCodeNumber, $barwidth, $barheight);
                            //echo "after barcode";
                            if (file_exists($barcodeFilePath)) {
                                $templateProcessor->setImg('barcode', array('src' => $barcodeFilePath, 'size' => array($docImagewidth, $docImageheight)));
                                $status['barcode'] = $barcodeSaveFileName;
                            } else {
                                $logger->info("Bar Code not exist for" . $this->orderData['increment_id'] . ' product is ' . $sku);
                                $templateProcessor->setValue('barcode', ' ');
                            }
                            //echo "step 2";
                        } elseif ($productAttributeItem['type'] == 'date') {
                            if ($productAttributeItem['source'] == 'other') {
                                if (!isset($productAttributeItem['duration'])) {
                                    $logger->info("Date Duration not valid " . $this->orderData['increment_id'] . '=>' . $sku);
                                }
                                $currentDate = $this->date->gmtDate();
                                $expDate = '';
                                $dateData = explode('_', $productAttributeItem['duration']);
                                $logger->info($dateData);
                                if ($dateData[1] == 'M') {
                                    $expDate = date('Y-m-d', strtotime($currentDate . '+ ' . $dateData[0] . " months"));
                                } elseif ($dateData[1] == 'D') {
                                    $expDate = date('Y-m-d', strtotime($currentDate . '+ ' . $dateData[0] . " days"));
                                } elseif ($dateData[1] == 'Y') {
                                    $expDate = date('Y-m-d', strtotime($currentDate . '+ ' . $dateData[0] . " years"));
                                } else {
                                    $logger->info("Something wrong about Date Duration " . $this->orderData['increment_id'] . '=>' . $sku);
                                }
                                if (!$expDate) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                } else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], $expDate);
                                }
                            }elseif($productAttributeItem['source'] == 'serialCode'){
                                $attributeVariableData = $serialCodeItem[$productAttributeItem['variable']];
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                                }
                            }elseif($productAttributeItem['source'] == 'customOption'){
                                if(isset($this->orderData['product_options']['options'])){
                                    $orderItemCustomOptions = $this->orderData['product_options']['options'];
                                    $logger->info($orderItemCustomOptions);
                                    $key = array_search($productAttributeItem['variable'], array_column($orderItemCustomOptions, 'label'));
                                    if (is_numeric($key)) {
                                        if(isset($productAttributeItem['duration'])){
                                            $expDate = '';
                                            //$currentDate = date_format($orderItemCustomOptions[$key]['value'],'Y-m-d');
                                            $dateObj = new \DateTime($orderItemCustomOptions[$key]['value']);
                                            $currentDate = $dateObj->format('Y-m-d');
                                            $dateData = explode('_', $productAttributeItem['duration']);
                                            if ($dateData[1] == 'M') {
                                                $expDate = date('Y-m-d', strtotime($currentDate . '+ ' . $dateData[0] . " months"));
                                            } elseif ($dateData[1] == 'D') {
                                                $expDate = date('Y-m-d', strtotime($currentDate . '+ ' . $dateData[0] . " days"));
                                            } elseif ($dateData[1] == 'Y') {
                                                $expDate = date('Y-m-d', strtotime($currentDate . '+ ' . $dateData[0] . " years"));
                                            } else {
                                                $logger->info("Something wrong about Date Duration " .$this->orderId .'=>'.$sku);
                                            }
                                            //$customOptionVariableData = $orderItemCustomOptions[$key]['value'];
                                            $templateProcessor->setValue($productAttributeItem['variable'], $expDate);
                                        }else{
                                            $customOptionVariableData = $orderItemCustomOptions[$key]['value'];
                                            $templateProcessor->setValue($productAttributeItem['variable'], $customOptionVariableData);
                                        }
                                    } else {
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    }
                                }else{
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }
                            }
                        } elseif ($productAttributeItem['type'] == 'cdate') {
                            if ($productAttributeItem['source'] == 'other') {
                                $currentDate = $this->date->gmtDate();
                                $templateProcessor->setValue($productAttributeItem['variable'], $currentDate);
                            }
                        }elseif ($productAttributeItem['type'] == 'url'){
                            if($productAttributeItem['source'] == 'serialCode') {
                                $attributeVariableData = $serialCodeItem[$productAttributeItem['variable']];
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }else {
                                    $cleanAttributeVariableData = str_replace('&','&amp;', $attributeVariableData);
                                    $templateProcessor->setValue($productAttributeItem['variable'], $cleanAttributeVariableData);
                                }
                            }
                        }
                    } /*End of the Foreach*/
                    //echo "Ramesh"; exit;
                    $templateProcessor->saveAs($this->temporaryDocFilesPath . '/' . $docsSaveFilename);
                    if (file_exists($this->temporaryDocFilesPath . '/' . $docsSaveFilename)) {
                        $pdfFileCreateStatus = $this->createPdf($this->temporaryDocFilesPath, $docsSaveFilename, $this->temporaryPdfFilesPath, $pdfFilename);
                        if($pdfFileCreateStatus['type'] == 'success') {
                            if (file_exists($this->temporaryPdfFilesPath . '/' . $pdfFilename)) {
                                $mailStatus = $this->sendPdfToCustomerEmail($this->temporaryPdfFilesPath . '/' . $pdfFilename, $customerData,
                                    $this->orderData['increment_id']);
                            } else {
                                $this->errors[$serialCodeItem['SerialNumber']] = "pdf file not exist";
                            }
                        }else{
                            $this->errors[$serialCodeItem['SerialNumber']] = $pdfFileCreateStatus['message'];
                        }
                    } else {
                        $this->errors[$serialCodeItem['SerialNumber']] = "doc file not exist";
                    }


                }
            }

            if(empty($this->errors)){
                $status['status'] = "success";
            }
            else{
                $status['status'] = "error";
                $status['message'] = $this->errors;
            }
        }catch (\Exception $e){
            $status['status'] = "error";
            $logger->info("Error while creating time-".$e->getMessage());
            $this->errors[$serialCodeItem['SerialNumber']]  = $e->getMessage();
            $status['message'] = $this->errors;
        }
        //exit;
        return $status;
    }

    /* method for creating barcodes*/

    protected function barCodeCreate($barcodeSaveFileName,$serialcode,$barwidth,$barheight){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-create-barcodecreate.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try{
            $barcodeOptions = array('text' => $serialcode,'barHeight'=> $barheight);
            $logger->info($barheight);

            $rendererOptions = array(
                //'topOffset' => 10,
                //'leftOffset' => 10,
            );
            $imageResource = Barcode::factory('code39', 'image', $barcodeOptions, $rendererOptions)->draw();
            $filename = $this->barcodeFilesPath.$barcodeSaveFileName;
            imagepng($imageResource,$filename);
            return $this->barcodeFilesPath.$barcodeSaveFileName;
        }catch (\Exception $e){
            $logger->info("Issue creation barcode for ".$barcodeSaveFileName.' Order is '.$this->orderId."Error Messsage ".$e->getMessage());
        }
    }


    /*method for creating pdf file based on word document*/
    public function createPdf($docFileTemporaryPath,$docTemporaryFileName,$pdfDirectoryPath,$pdfSaveFilename){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-create-createPdf.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try {
            if (file_exists($docFileTemporaryPath . '/' . $docTemporaryFileName)) {
                /*
                 $document = new \Gears\Pdf($docFileTemporaryPath . '/' . $docTemporaryFileName);
                 $document->converter = function () {
                     return new \Gears\Pdf\Docx\Converter\Unoconv();
                 };
                 $document->save($pdfDirectoryPath . '/' . $pdfSaveFilename);
               */
                \Gears\Pdf::convert($docFileTemporaryPath . '/' . $docTemporaryFileName, $pdfDirectoryPath . '/' . $pdfSaveFilename);

                if(file_exists($pdfDirectoryPath . '/' . $pdfSaveFilename)) {
                    chmod($docFileTemporaryPath . '/' . $docTemporaryFileName,0777);
                    //unlink($templacePath . '/' . $docsSaveFilename);
                    $status['type'] = "success";
                }else{
                    $status['type'] = "error";
                    $status['message'] = "Pdf creation issue...check log";
                }
            }
        } catch (\Exception $e) {
            $status['type'] = "error";
            $status['message'] = $this->orderData['increment_id']."Error Messsage ".$e->getMessage();
            $logger->info("Error at Pdf Creation ".$pdfSaveFilename.' Order is '.$this->orderData['increment_id']."Error Messsage ".$e->getMessage());
        }
        return $status;
    }

    /*method for merging all pdf files*/
    public function createMergePdf($listOfPdfFiles,$pdfDirectoryPath,$orderId,$orderStatusId){
        $mediaPath = $this->dataHelper->getMediaPath();
        $mergedPdfFiles = "mergedpdfiles";
        $mergedDirectoryPath = $mediaPath.'/'.$mergedPdfFiles;
        $mergedFileName = $orderId.".pdf";

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-pdfMerge-create-process.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $mergedPdfResponse = array();

        try{
            if($listOfPdfFiles) {
                $pdfMerged = new \Zend_Pdf();
                foreach ($listOfPdfFiles as $key => $pdfFileitem) {
                    if (file_exists($pdfDirectoryPath.'/'.$pdfFileitem)) {
                        $logger->info($pdfDirectoryPath . '/' . $pdfFileitem);
                        $pdf = \Zend_Pdf::load($pdfDirectoryPath . '/' . $pdfFileitem);
                        foreach ($pdf->pages as $page) {
                            $clonedPage = clone $page;
                            $pdfMerged->pages[] = $clonedPage;
                        }
                    }else{
                        $mergedPdfResponse['status'] = "failed";
                        $logger->info("path not exists" .$pdfDirectoryPath. '/' . $pdfFileitem);
                        if($orderStatusId) {
                            $orderStatusObj = $this->orderPdf->load($orderStatusId);
                            $orderStatusObj->setDescription("path not exists" . $pdfDirectoryPath . '/' . $pdfFileitem);
                            $orderStatusObj->save();
                        }
                    }
                }
                unset($clonedPage);
                $pdfMerged->save($mergedDirectoryPath.'/'.$mergedFileName);
                if(file_exists($mergedDirectoryPath.'/'.$mergedFileName)){
                    $mergedPdfResponse['status'] = "scuess";
                    $mergedPdfResponse['filepath'] = $mergedDirectoryPath.'/'.$mergedFileName;
                }
                if($orderStatusId) {
                    $orderStatusObj = $this->orderPdf->load($orderStatusId);
                    $orderStatusObj->setStatus("Completed");
                    $orderStatusObj->save();
                }
            }
            // print_r($mergedPdfResponse);

            return $mergedPdfResponse;
        }catch (\Exception $e){
            $logger->info("Error at Pdf Merger:-".$e->getMessage());
            $mergedPdfResponse['status'] = $e->getMessage();
            if($orderStatusId) {
                $orderStatusObj = $this->orderPdf->load($orderStatusId);
                $orderStatusObj->setDescription($e->getMessage());
                $orderStatusObj->save();
            }
        }
    }

}