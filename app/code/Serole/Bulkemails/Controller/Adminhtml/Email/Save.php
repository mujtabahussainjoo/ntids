<?php

namespace Serole\Bulkemails\Controller\Adminhtml\Email;

use Magento\Backend\App\Action;


class Save extends \Magento\Backend\App\Action{

   
    private $coreRegistry = null;

    private $resultPageFactory;

    private $attachModel;

    private $backSession;

    protected $pdfHelper;

    protected $productattachment;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,        
        \Serole\Serialcode\Model\OrderitemSerialcode $orderItemSerialcode,
        \Magento\Sales\Model\Order\Item $orderItemsCollection,
        \Serole\Productattachment\Model\Productattachment $productattachment,
        \Serole\Pdf\Helper\Pdf $pdfHelper,
        \Magento\Sales\Model\Order $orderObj
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;        
        $this->backSession = $context->getSession();
        $this->orderObj = $orderObj;
        $this->orderItems = $orderItemsCollection;
        $this->orderItemSerialcodes = $orderItemSerialcode;
        $this->productattachment = $productattachment;
        $this->pdfHelper = $pdfHelper;
        parent::__construct($context);
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Serole_Productattach::save');
    }

    /**
     * Edit CMS page
     *
     * @return void
     */
    public function execute()
    {
        /*You can't print $pdf data because product obj in that, plz comment if u want print */
        $postData = $this->getRequest()->getParams();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productRepository = $objectManager->create('\Magento\Catalog\Model\ProductRepository');

        $fileTypes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');

        if (!empty($_FILES)) {
                //echo "condition"; exit;
                $fileName = $_FILES['file']['name'];
                $filesize = $_FILES['file']['size'];
                $fileType = $_FILES['file']['type'];
                $fileTmpName = $_FILES['file']['tmp_name'];
                $serialCodesList = array();
                if(in_array($fileType,$fileTypes)){
                   $orderId = $postData['orderid'];
				   $emailTemplateId = $postData['emailtemplateid'];
                   $orderLoad = $this->orderObj->loadByIncrementId($orderId);
                   $invoiceCollection = $orderLoad->getInvoiceCollection();
                   if(count($invoiceCollection)){
                       $orderQty = $orderLoad->getTotalQtyOrdered();
                       $mediaPath = $this->pdfHelper->getMediaBaseDir();
                       $pdfData = array();
                       $pdfData['orderData'] = $orderData = $orderLoad->getData();
                       $pdfData['orderData']['incrementId'] = $orderId;
                       $pdfData['orderData']['orderId'] = $orderId;
                       $pdfData['orderData']['emailtemplateid'] = $emailTemplateId;
                       $readCsv = fopen($fileTmpName,'r');
                       if($readCsv !== FALSE) {
                           $row = 0;
                           while(! feof($readCsv)) {
                               $data = fgetcsv($readCsv, 1000, ",");
                               if ($row == 0) {
                                   $row++;
                                   continue;
                               }

                               if ($data) {
                                   //echo "<pre>"; print_r($data); exit;
                                   if ($data['0'] && $data['1']) {
                                       $orderSerialCodeData = $this->getOrderItemSerialcodes($orderId, $data['0']);
                                       if ($orderSerialCodeData) {
                                           if (count($orderSerialCodeData) > 1) {
                                               //echo $data[0]; exit;
                                               $errors[$data['0']] = "something went wrong, there is more that one record with this serialcode";
                                           }else{
                                               //echo "This con"; exit;
                                               if ($orderSerialCodeData[0]['parentsku']) {
                                                   //echo "1234"; exit;
                                                   $errors[$data['0']] = "This serialcode ".$data[0]." is part of bundle Product";
                                               }else{
                                                   //echo "322"; exit;
                                                   $productObj = $productRepository->get($orderSerialCodeData[0]['sku'],FALSE,$orderData['store_id'],FALSE);
                                                   $productAttchId = $productObj->getProductAttachment(); //exit;
                                                   if($productAttchId){
                                                       $attachObj = $this->productattachment->load($productAttchId);
                                                       if($attachObj->getFile()){
                                                           $fileName = $attachObj->getFile(); //exit;
                                                           $filePath = $mediaPath.$fileName;
                                                           if(file_exists($filePath) && $fileName){
                                                               //echo "File exist"; exit;
                                                               $productAttributeJsonData = $productObj->getProductJsonFormat(); //exit;
                                                               $productAttributeData = (array)json_decode($productAttributeJsonData, TRUE);
                                                               //echo "<pre>"; print_r($productAttributeData); exit;
                                                               //echo "<pre>"; print_r($orderSerialCodeData); exit;
                                                               if($productAttributeData) {
                                                                   $pdfData['sku'][$orderSerialCodeData[0]['sku']][$data['0']]['data'] = $orderSerialCodeData[0];
																   $pdfData['sku'][$orderSerialCodeData[0]['sku']][$data['0']]['toname'] = $data[1];
                                                                   $pdfData['sku'][$orderSerialCodeData[0]['sku']][$data['0']]['email'] = $data[2];
                                                                   $pdfData['sku'][$orderSerialCodeData[0]['sku']][$data['0']]['fromname'] = $data[3];
                                                                   $pdfData['sku'][$orderSerialCodeData[0]['sku']][$data['0']]['fromemail'] = $data[4];
                                                                   $pdfData['sku'][$orderSerialCodeData[0]['sku']][$data['0']]['message'] = $data[5];
                                                                   if (!isset($pdfData['productdata'][$orderSerialCodeData[0]['sku']])) {
                                                                       $pdfData['productdata'][$orderSerialCodeData[0]['sku']] = $productObj->getData();
                                                                   }
                                                                   $pdfData['filePath'][$orderSerialCodeData[0]['sku']] = $filePath;
                                                               }else{
                                                                   echo "no product json".$productObj->getSku();
                                                                   $errors[$productObj->getSku()] = "This product has no product json ".$productObj->getSku();
                                                               }
                                                              $row++;
                                                           }else{
                                                               $errors[$data['0']] = "Document not avilable";
                                                           }
                                                       }else{
                                                           $errors[$data['0']] = "Document not avilable in attahment list";
                                                       }
                                                   }else{
                                                       $errors[$data['0']] = "Document not assigned to product";
                                                   }
                                               }
                                           }
                                       }else{
                                           $errors[$data['0']] = "This serialcode isn't part of this order Please use correct Serial code";
                                       }
                                   }
                               }
                           }
                       }
                       fclose($readCsv);

                       //exit;

                       if(empty($errors)) {
                           foreach ($pdfData['sku'] as $sku => $serailcodeItems) {
                               $itemsCount = count($serailcodeItems);
                               $orderSerailcodesCount = $this->getCountofSerialcodes($orderId, $sku);
                               if ($itemsCount > $orderSerailcodesCount) {
                                   $errors[$sku] = "serial code has count issue with following sku " . $sku;
                               }
                           }
                       }
                       $errorMessage = '';
					  // echo "<pre>"; print_r($errors); exit;
                       if(empty($errors)){
                          // echo "<pre>"; print_r($pdfData); exit;
                           $status = $this->pdfHelper->bulkEmails($pdfData);
                           if($status['status'] == 'success'){
                              $this->messageManager->addSuccess("mails sent scuessfully");
                           }elseif($status['status'] == 'error'){
                              foreach ($status['message'] as $sub => $errorItem){
                                  $errorMessage = $sub.' : '.$errorItem;
								  $this->messageManager->addError($errorMessage);
                              }
   
                               $this->_redirect('*/*/');
                           }
                       }
					   
					   if(!empty($errors))
					   {
						   foreach($errors as $errorKey=>$errorVal)
						   {
							   $this->messageManager->addError($errorKey." : ".$errorVal);
						   }
					   }

                   }else{
                       $this->messageManager->addError("This order does't have invoices");
                   }
                }else{
                    $this->messageManager->addError("Please upload only CSV file");
                }
            }
        $this->_redirect('*/*/');
     }

     private function getCountofSerialcodes($orderId,$sku){
         $serialCodesCollection = $this->orderItemSerialcodes->getCollection();
         $serialCodesCollection->addFieldToFilter('OrderID',$orderId);
         $serialCodesCollection->addFieldToFilter('sku',$sku);
         $filterData = $serialCodesCollection->getData();
         return count($filterData);
     }
     private function getOrderItemSerialcodes($orderId,$serialCode){
         $serialCodesCollection = $this->orderItemSerialcodes->getCollection();
         $serialCodesCollection->addFieldToFilter('OrderID',$orderId);
         $serialCodesCollection->addFieldToFilter('SerialNumber',$serialCode);
         return $serialCodesCollection->getData();
     }

}