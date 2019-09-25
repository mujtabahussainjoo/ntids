<?php

namespace Serole\Pdf\Model;

use Magento\Framework\Event\ObserverInterface;

#use Zend_Barcode;
use Zend\Barcode\Barcode;
use Library\QrCode\QrCode;
use Library\QrCode\Renderer\GoogleChartRenderer;

require '/var/www/html/lib/gearpdf/vendor/autoload.php';
require_once '/var/www/html/lib/gearpdf/vendor/H2OpenXML/sourcecode/HTMLtoOpenXML.php';

class Testcreatepdf extends \Magento\Framework\Model\AbstractModel {

    protected $product;

    protected $orderPdf;

    protected $order;

    protected $orderItemsCollection;

    protected $orderItemSerialcode;

    protected $pdfHelper;

    protected $date;

    protected $productOptions;

    protected $giftMessage;

    protected $giftImage;

    protected $fileSystemPath;

    protected $barCodesDirectoryPath;

    protected $temporaryDocFilesPath;

    protected $temporaryPdfFilesPath;

    protected $mergedPdfFilesPath;

    protected $barcodeFilesPath;

    protected $mediaPath;

    protected $logoUrl;

    protected $virtualProductfilesListArray;

    protected $bundleProductfilesListArray;

    protected $totalOrderQty;

    protected $orderSerialCodesQtyFromTable;

    protected $useSerialCodesFromTable;

    protected $useSageSerialCodes;

    protected $serialCodesQtyIssueFromTable;

    protected $isProductMissedJsonData;

    protected $isProductSerialised;

    protected $errorList;

    protected $isPdfGroupTicket;

    protected $productattachment;

    protected $productDocsFilePath;

    protected $cmsBlock;

    protected $sageInventory;

    protected $resourceConnection;

    protected $productRepository;

    protected $scopeConfig;

    protected $isOnlyPdfWithOutSerialize;

    protected $orderStatusId;

    public function __construct(\Magento\Catalog\Model\Product $product,
                                \Serole\Pdf\Model\Pdf $orderPdf,
                                \Serole\Serialcode\Model\OrderitemSerialcode $orderItemSerialcode,
                                \Magento\Sales\Model\Order\Item $orderItemsCollection,
                                \Magento\Sales\Model\Order $order,
                                \Serole\Pdf\Helper\Testpdf $pdfHelper,
                                \Serole\Productattachment\Model\Productattachment $productattachment,
                                \Magento\Framework\Stdlib\DateTime\DateTime $date,
                                \Magento\Catalog\Model\Product\Option $productOptions,
                                \Serole\GiftMessage\Model\Message $giftMessage,
                                \Serole\GiftMessage\Model\Image $giftImage,
                                \Magento\Cms\Model\Block $cmsBlock,
                                \Magento\Customer\Model\Session $customerSession,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Magento\Framework\App\ResourceConnection $resourceConnection,
                                \Serole\Sage\Model\Inventory $sageInventory,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Catalog\Model\ProductRepository $productRepository

    ) {
        $this->customerSession =$customerSession;
        $this->productattachment = $productattachment;
        $this->product = $product;
        $this->orderItems = $orderItemsCollection;
        $this->orderPdf = $orderPdf;
        $this->order = $order;
        $this->orderItemSerialcode = $orderItemSerialcode;
        $this->pdfHelper = $pdfHelper;
        $this->date = $date;
        $this->productOptions = $productOptions;
        $this->giftMessage = $giftMessage;
        $this->giftImage = $giftImage;
        $this->fileSystemPath = '';
        $this->directoryPath = '';
        $this->temporaryDocFilesPath = '';
        $this->temporaryPdfFilesPath = '';
        $this->mergedPdfFilesPath = '';
        $this->barcodeFilesPath = '';
        $this->qrCodesFilePath = '';
        $this->mediaPath = '';
        $this->logoUrl = '';
        $this->virtualProductfilesListArray = array();
        $this->bundleProductfilesListArray = array();
        $this->itemData = array();
        $this->virtualProductfilesListArray['doc'] = array();
        $this->virtualProductfilesListArray['pdf'] = array();
        $this->bundleProductfilesListArray['doc'] =  array();
        $this->bundleProductfilesListArray['pdf'] =  array();
        $this->orderId = '';
        $this->checkoutSession = $checkoutSession;
        $this->totalOrderQty = 0;
        $this->serlizedTotalOrderQty = 0;
        $this->orderSerialCodesQtyFromTable = 0;
        $this->orderSerlizedSerialCodesQtyFromTable = 0;
        $this->useSerialCodesFromTable = 0;
        $this->useSageSerialCodes = 1;
        $this->serialCodesQtyIssueFromTable = 0;
        $this->isProductMissedJsonData = array();
        $this->isProductSerialised = array();
        $this->errorList = array();
        $this->isPdfGroupTicket = array();
        $this->productDocsFilePath = array();
        $this->customerData = array();
        $this->cmsBlock = $cmsBlock;
        $this->storeId = '';
        $this->sageInventory = $sageInventory;
        $this->resourceConnection = $resourceConnection;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
        $this->useSerlizedSerialCodesFromTable = 0;
        $this->useSerlizedSageSerialCodes = 1;
        $this->serlizedSerialCodesQtyIssueFromTable = 0;
        $this->isOnlyPdfWithOutSerialize = 0;
        $this->orderStatusId;
        $this->pdfGenerationError = array();
    }


    public function createPdfConcept($orderId,$emailStauts,$reqType){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-createPdfConcept.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $order = $this->order->loadByIncrementId($orderId);

        if($order->hasInvoices()) {
            $this->createLog("------------------".$orderId."-----------------------------");
            $quoteId = $order->getQuoteId();
            $this->orderId = $orderId;
            $incrementId = $order->getIncrementId();
            $this->incrementId = $order->getIncrementId();
            $this->storeId = $order->getStoreId();
            $customerId = $order->getCustomerId();
            $isGuest = $order->getCustomerIsGuest();

            $totalQtyOrdered = $order->getTotalQtyOrdered();

            $resource = $this->resourceConnection;
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('order_item_serialcode');

            $giftMessageObj = $this->giftMessage->getCollection();
            $giftMessageObj->addFieldToFilter('order_id', $incrementId);
            $giftMessageData = $giftMessageObj->getFirstItem()->getData();

            $customerData = array();

            if ($giftMessageData) {
                $emailTemplateObj = $this->giftImage->load($giftMessageData['image']);
                $emailTemplateData = $emailTemplateObj->getData();
                $customerData['toname'] = $giftMessageData['to'];
                $customerEmail = $giftMessageData['email'];
                $customerData['message'] = $giftMessageData['message'];
                $customerData['fromname'] = $giftMessageData['from'];
                if ($emailTemplateData) {
                    $customerData['emailtemplateid'] = $emailTemplateData['emailtemplateid'];
                }
            } else {
                $customerFirstName = $order->getCustomerFirstname();
                $customerLastName = $order->getCustomerLastname();
                $deliveryemail = $order->getdeliveryemail();
                if ($deliveryemail) {
                    $customerEmail = $deliveryemail;
                } else {
                    $customerEmail = $order->getCustomerEmail();
                }
                $customerData['toname'] = $customerFirstName . ' ' . $customerLastName;
            }
            $customerData['email'] = $customerEmail;

            $this->customerData = $customerData;

            $orderItems = $order->getAllItems();

            $this->directoryPath = $this->pdfHelper->getMediaBaseDir();
            $this->mediaPath = $this->pdfHelper->getMediaBaseDir();
            $logoPath = $this->pdfHelper->getLogo('design/header/logo_src');
            $this->logoUrl = $this->mediaPath . 'logo/' . $logoPath;

            $this->fileSystemPath = $this->pdfHelper->getRootBaseDir();
            $this->temporaryDocFilesPath = $this->fileSystemPath . 'neatideafiles/temporryfiles/doc/';
            $this->temporaryPdfFilesPath = $this->fileSystemPath . 'neatideafiles/temporryfiles/pdf/';
            $this->mergedPdfFilesPath = $this->fileSystemPath . 'neatideafiles/pdf/';
            $this->mergedOrginalPdfFilesPath = $this->fileSystemPath . 'neatideafiles/pdfOrginal/';
            $this->barcodeFilesPath = $this->fileSystemPath . 'neatideafiles/barcodeimages/';
            $this->qrCodesFilePath = $this->fileSystemPath . 'neatideafiles/qrcodes/';


            if (!file_exists($this->temporaryDocFilesPath)) {
                mkdir($this->temporaryDocFilesPath, 0777, true);
                chmod($this->temporaryDocFilesPath, 0777);
            }
            if (!is_writable($this->temporaryDocFilesPath)) {
                chmod($this->temporaryDocFilesPath, 0777);
            }

            if (!file_exists($this->qrCodesFilePath)) {
                mkdir($this->qrCodesFilePath, 0777, true);
                chmod($this->qrCodesFilePath, 0777);
            }
            if (!is_writable($this->qrCodesFilePath)) {
                chmod($this->qrCodesFilePath, 0777);
            }

            if (!file_exists($this->temporaryPdfFilesPath)) {
                mkdir($this->temporaryPdfFilesPath, 0777, true);
                chmod($this->temporaryPdfFilesPath, 0777);
            }
            if (!is_writable($this->temporaryPdfFilesPath)) {
                chmod($this->temporaryPdfFilesPath, 0777);
            }


            if (!file_exists($this->mergedPdfFilesPath)) {
                mkdir($this->mergedPdfFilesPath, 0777, true);
                chmod($this->mergedPdfFilesPath, 0777);
            }
            if (!is_writable($this->mergedPdfFilesPath)) {
                chmod($this->mergedPdfFilesPath, 0777);
            }

            if (!file_exists($this->mergedOrginalPdfFilesPath)) {
                mkdir($this->mergedOrginalPdfFilesPath, 0777, true);
                chmod($this->mergedOrginalPdfFilesPath, 0777);
            }
            if (!is_writable($this->mergedOrginalPdfFilesPath)) {
                chmod($this->mergedOrginalPdfFilesPath, 0777);
            }


            if (!file_exists($this->barcodeFilesPath)) {
                mkdir($this->barcodeFilesPath, 0777, true);
                chmod($this->barcodeFilesPath, 0777);
            }
            if (!is_writable($this->barcodeFilesPath)) {
                chmod($this->barcodeFilesPath, 0777);
            }

            try {
                $docMissedProductItems = array();
                $docNameNotExist = array();
                $sageVirtualProductReqData['virtual'] = array();
                $sageVirtualProductReqData['bundle'] = array();

                /*This variable we are using only serlized flag on and no PDF requires*/
                $sageVirtualSerizedProductReqData['virtual'] = array();
                $sageVirtualSerizedProductReqData['bundle'] = array();

                $bundleSkus = array();
                $isProductjsonDataMissed = array();
                $productNotAvilable = array();
                $status = array();
                $productNames = array();

                $orderStatusId = $this->setOrderStatus();

                $logger->info($orderStatusId);

                foreach ($orderItems as $key => $item) {
                    $itemData = $item->getData();
                    $productId = $item->getProductId();
                    $productType = $item->getProductType();
                    $productSku = $item->getSku();
                    $qty = (int)$item->getQtyOrdered();
                    $parentItemId = trim((int) $item->getParentItemId());

                    $this->itemData[$productSku] = $item->getData();
                    $this->itemData[$productSku]['incrementId'] = $orderId;
                    $this->itemData[$productSku]['orderId'] = $orderId;

                    $productObj = $this->product->setStoreId($item->getStoreId())->load($productId);
                    $productAttachObj = $this->productattachment->load($productObj->getProductAttachment());
                    $docFileName = $productAttachObj->getFile();
                    $docFilePath = $this->mediaPath . $docFileName;

                    $isSerlized = $productObj->getData('isserializeditem');

                    if ($productObj->getData()) {
                        if ($productType == "bundle") {
                            $bundleSkus[$item->getId()]['isPdf'] = $productObj->getData('ni_product_pdf_required');
                            $bundleSkus[$item->getId()]['sku'] = $item->getSku();
                            $bundleSkus[$item->getId()]['docFile'] = $docFilePath;
                            $bundleSkus[$item->getId()]['docName'] = $docFileName;
                            $bundleSkus[$item->getId()]['productId'] = $productId;
                            $bundleSkus[$item->getId()]['isSerialized'] = $isSerlized;
                            $bundleSkus[$item->getId()]['isPdfGropTicket'] = $productObj->getNiPdfGroupTicket();
                            $bundleSkus[$item->getId()]['productJson'] = (array)json_decode($productObj->getProductJsonFormat(), TRUE);
                            if($productObj->getNiPdfGroupTicket()){
                                $this->isPdfGroupTicket[] = $item->getSku();
                            }
                        }
                        # ***********Product should be Virtual & pdf_required & is_seralized item*************
                        if ($productObj->getTypeId() == "virtual" && $productObj->getData('ni_product_pdf_required') && $item->getParentItemId() == '' && $isSerlized) {
                            $this->isOnlyPdfWithOutSerialize = 1;
                            //$logger->info("Order Id".$orderId."V&P&S");
                            if($docFileName){
                                $productAttributeData = (array)json_decode($productObj->getProductJsonFormat(), TRUE);
                                if(empty($productAttributeData)){
                                    $this->isProductMissedJsonData[] = $item->getSku();
                                }
                                #$this->isProductSerialised[$incrementId][$item->getSku()] = $productObj->getSerialno();
                                $sageVirtualProductReqData['virtual'][] = $quoteId . ',' . $incrementId . ',' . $productSku . ',' . $qty;
                                $this->totalOrderQty += $qty;

                                if (file_exists($docFilePath) && $docFileName) {
                                    $this->productDocsFilePath[$productSku] = $docFilePath;
                                } else {
                                    array_push($docMissedProductItems, $productSku);  #Documents Missed Products List in Array
                                }
                            }else{
                                array_push($docMissedProductItems, $productSku);
                            }
                        }elseif ($productType == 'virtual' && $parentItemId && $bundleSkus[$parentItemId]['isPdf'] && $bundleSkus[$parentItemId]['isSerialized']){
                            $this->isOnlyPdfWithOutSerialize = 1;
                            //$logger->info("Order Id".$orderId."B&P&S");
                            if($bundleSkus[$parentItemId]['docName']){
                                if($bundleSkus[$parentItemId]['isSerialized']) {
                                    $parentSkuJsonData = $bundleSkus[$parentItemId]['productJson'];
                                    if (empty($bundleSkus[$parentItemId]['productJson'])) {
                                        $this->isProductMissedJsonData[] = $bundleSkus[$parentItemId]['sku'];
                                    }
                                    if ($bundleSkus[$parentItemId]['isPdf']) {

                                        $this->totalOrderQty += $qty;
                                        $sageVirtualProductReqData['bundle'][$bundleSkus[$parentItemId]['sku']][] = $quoteId . ',' . $incrementId . ',' . $productSku . ',' . $qty;

                                        if (file_exists($bundleSkus[$parentItemId]['docFile']) && $bundleSkus[$parentItemId]['docName']) {
                                            $this->productDocsFilePath[$bundleSkus[$parentItemId]['sku']] = $bundleSkus[$parentItemId]['docFile'];
                                        } else {
                                            array_push($docMissedProductItems, $bundleSkus[$parentItemId]['sku']);  #Documents Missed Products List in Array
                                        }
                                    }
                                }
                            }else{
                                array_push($docMissedProductItems, $bundleSkus[$parentItemId]['sku']);
                            }
                        }elseif ($isSerlized && !$productObj->getData('ni_product_pdf_required') && $productType == "virtual" && !$item->getParentItemId()){
                            $this->isOnlyPdfWithOutSerialize = 1;
                            //$logger->info("Order Id".$orderId."V&!P&S");
                            $this->serlizedTotalOrderQty += $qty;
                            $sageVirtualSerizedProductReqData['virtual'][] = $quoteId . ',' . $incrementId . ',' . $productSku . ',' . $qty;
                        }elseif (isset($bundleSkus[$parentItemId]['isSerialized']) && !$bundleSkus[$parentItemId]['isPdf'] && $productType == "virtual" && $item->getParentItemId()){
                            $this->isOnlyPdfWithOutSerialize = 1;
                            //$logger->info("Order Id".$orderId."B&!P&S");
                            $this->serlizedTotalOrderQty += $qty;
                            $sageVirtualSerizedProductReqData['bundle'][] = $quoteId . ',' . $incrementId . ',' . $productSku . ',' . $qty;
                        }elseif (!$isSerlized && $productObj->getData('ni_product_pdf_required') && ($productType == "virtual" || $productType = "bundle") && !$item->getParentItemId()){
                            //$logger->info("Order Id".$orderId."B&V&P&!S");
                            if (file_exists($docFilePath) && $docFileName) {
                                $this->productDocsFilePath[$productSku] = $docFilePath;
                                $this->createOnlyPdf($productSku);
                            }else{
                                array_push($docMissedProductItems, $productSku);
                            }
                        }
                        /*else{
                            array_push($productNotAvilable, $item->getSku());
                        }*/

                    } else {
                        array_push($productNotAvilable, $item->getSku());
                    }
                }

                $sageVirtualProductReturnData = array();
                $sageSerlizedVirtualProductReturnData = array();
                $serialItemsFromTable = array();
                $apiError = '';
                $serlizedApiError = '';

                $logger->info($this->isProductMissedJsonData);

                if($this->isOnlyPdfWithOutSerialize == 1) {
                    //$logger->info("step1");
                    if ($sageVirtualProductReqData) {
                        if ($reqType == 'backend') {
                            $serialItemsFromTable['virtual'] = $this->serialCodesExistList($sageVirtualProductReqData['virtual'], 'virtual');
                            $serialItemsFromTable['bundle'] = $this->serialCodesExistList($sageVirtualProductReqData['bundle'], 'bundle');

                            /*Below are Serlized product not pdf required*/
                            $serlizedSerialItemsFromTable['virtual'] = $this->serlizedSerialCodesExistList($sageVirtualSerizedProductReqData['virtual'], 'virtual');
                            $serlizedSerialItemsFromTable['bundle'] = $this->serlizedSerialCodesExistList($sageVirtualSerizedProductReqData['bundle'], 'bundle');
                        }

                        if ($this->orderSerialCodesQtyFromTable > 0) {
                            if ((int)$this->totalOrderQty == (int)$this->orderSerialCodesQtyFromTable) {
                                $this->useSerialCodesFromTable = 1;
                                $this->useSageSerialCodes = 0;
                            } else {
                                $this->createLog("BackEnd Qty issue for serialcode ".$this->totalOrderQty ."-----".$this->orderSerialCodesQtyFromTable);
                                $this->serialCodesQtyIssueFromTable = 1;
                                $this->useSageSerialCodes = 0;
                            }
                        }

                        if ($this->orderSerlizedSerialCodesQtyFromTable > 0) {
                            if ($this->serlizedTotalOrderQty == $this->orderSerlizedSerialCodesQtyFromTable) {
                                $this->useSerlizedSerialCodesFromTable = 1;
                                $this->useSerlizedSageSerialCodes = 0;
                            } else {
                                $this->serlizedSerialCodesQtyIssueFromTable = 1;
                                $this->useSerlizedSageSerialCodes = 0;
                            }
                        }

                        if ($this->serialCodesQtyIssueFromTable == 1 && $reqType == 'backend') {
                            $this->createLog("totalOrderQty".$this->totalOrderQty);
                            $this->createLog("orderSerialCodesQtyFromTable".$this->orderSerialCodesQtyFromTable);
                            $this->errorList['backendserialcode'] = "OrderId $orderId (From serial code Table)- Serial codes quantity does not match with ordered quantity.";
                            $message = "OrderId $orderId (From serial code Table)- Serial codes quantity does not match with ordered quantity.";
                            $this->pdfHelper->sendEmailToAdminPDFissue($empty = '', $incrementId, $message);
                            $this->createLog($message);
                        }

                        /*-------------Sage start-------------- */
                        $this->createLog($sageVirtualProductReqData);
                        $sageRequest = $this->sageInventory;
                        $virtualSerialCodesResponseCount = 0;
                        $bundleSerialCodesResponseCount = 0;
                        $virtualSerlizedSerialCodesResponseCount = 0;
                        $bundleSerlizedSerialCodesResponseCount = 0;

                        if (!empty($sageVirtualProductReqData['virtual']) && $this->useSageSerialCodes == 1) {
                            $logger->info("step1");
                            $verTualApiResponse = $sageRequest->getSerilaCodes($sageVirtualProductReqData['virtual']);
                            $this->createLog($verTualApiResponse);
                            #$verTualApiResponse = $this->getVirtualSerialCodes($sageVirtualProductReqData['virtual'],'virtual');
                            if ($verTualApiResponse['response'] == "Success") {
                                $sageVirtualProductReturnData['virtual'] = $verTualApiResponse;
                                $virtualSerialCodesResponseCount += (int)$verTualApiResponse['TotalCount'];
                            } else {
                                $apiError .= $verTualApiResponse['message'];
                            }

                        }

                        if (!empty($sageVirtualProductReqData['bundle']) && $this->useSageSerialCodes == 1) {
                            $logger->info("step2");
                            foreach ($sageVirtualProductReqData['bundle'] as $bundleSku => $bundleItems) {
                                $apiBundleResponse = $sageRequest->getSerilaCodes($bundleItems);
                                $this->createLog($apiBundleResponse);
                                #$apiBundleResponse = $this->getVirtualSerialCodes($bundleItems,'bundle');
                                if ($apiBundleResponse['response'] == "Success") {
                                    $sageVirtualProductReturnData['bundle'][$bundleSku] = $apiBundleResponse;
                                    $bundleSerialCodesResponseCount += (int)$apiBundleResponse['TotalCount'];
                                } else {
                                    $apiError .= $apiBundleResponse['message'];
                                    break;
                                }

                            }
                        }

                        if (!empty($sageVirtualSerizedProductReqData['virtual']) && $this->useSerlizedSageSerialCodes == 1) {
                            $logger->info("step3");
                            $serlizedVertualApiResponse = $sageRequest->getSerilaCodes($sageVirtualSerizedProductReqData['virtual']);
                            //$serlizedVertualApiResponse = $this->testSerialCodes();
                            $this->createLog($serlizedVertualApiResponse);
                            //echo "<pre>"; print_r($serlizedVertualApiResponse); exit;
                            if ($serlizedVertualApiResponse['response'] == "Success") {
                                $sageSerlizedVirtualProductReturnData['virtual'] = $serlizedVertualApiResponse;
                                $virtualSerlizedSerialCodesResponseCount += (int)$serlizedVertualApiResponse['TotalCount'];
                            } else {
                                $serlizedApiError .= $serlizedVertualApiResponse['message'] . " and Order Id is" . $incrementId;
                            }

                        }

                        if (!empty($sageVirtualSerizedProductReqData['bundle']) && $this->useSerlizedSageSerialCodes == 1) {
                            $logger->info("step4");
                            $serlizedBundleApiResponse = $sageRequest->getSerilaCodes($sageVirtualSerizedProductReqData['bundle']);
                            $this->createLog($serlizedBundleApiResponse);
                            if ($serlizedBundleApiResponse['response'] == "Success") {
                                $sageSerlizedVirtualProductReturnData['bundle'] = $serlizedBundleApiResponse;
                                $bundleSerlizedSerialCodesResponseCount += (int)$serlizedVertualApiResponse['TotalCount'];
                            } else {
                                $serlizedApiError .= $serlizedBundleApiResponse['message'] . " and Order Id is" . $incrementId;
                            }

                        }
                        /*-------------Sage Request-------------- End*/


                        /*----------------Saving Serial codes in DB start ----------*/
                        if (($this->useSageSerialCodes == 1 || $this->useSerlizedSageSerialCodes == 1) && $reqType == "frontend" &&
                            ($virtualSerialCodesResponseCount > 0 || $bundleSerialCodesResponseCount > 0 || $virtualSerlizedSerialCodesResponseCount > 0 || $bundleSerlizedSerialCodesResponseCount > 0)) {
                            $serialCodesResponseCount = $virtualSerialCodesResponseCount + $bundleSerialCodesResponseCount;
                            $serlizedSerialCodesResponseCount = $virtualSerlizedSerialCodesResponseCount + $bundleSerlizedSerialCodesResponseCount;

                            if ((int)$this->totalOrderQty != (int)$serialCodesResponseCount) {
                                $this->errorList['serialcode'] = "serialcodes and qty not match";
                                $message = "serialcodes and qty not match";
                                $this->pdfHelper->sendEmailToAdminPDFissue($empty = '', $incrementId, $message);
                                $this->createlog($this->errorList);
                            }

                            if ((int)$this->serlizedTotalOrderQty != (int)$serlizedSerialCodesResponseCount) {
                                //$this->errorList['serialcode'] = "serlized product serialcodes and qty not match";
                                $message = "serialcodes and qty not match";
                                $this->pdfHelper->sendEmailToAdminPDFissue($empty = '', $incrementId, $message);
                                $this->createlog($this->errorList);
                            }


                            $query = '';
                            $customerEmail = $customerData['email'];
                            if ($this->useSageSerialCodes == 1) {
                                if (isset($sageVirtualProductReturnData['virtual']['sku'])) {
                                    foreach ($sageVirtualProductReturnData['virtual']['sku'] as $virutlSku => $virtualItem) {
                                        foreach ($virtualItem as $virtualSerialCode) {
                                            $serialCodeNo = $virtualSerialCode['SerialNumber'];
                                            $exprieDate = $virtualSerialCode['ExpireDate'];
                                            $pin = $virtualSerialCode['PIN'];
                                            $secondSerialCode = $virtualSerialCode['SecondSerialCode'];
                                            $url = $virtualSerialCode['URL'];
                                            $startDate = $virtualSerialCode['StartDate'];
                                            $value = $virtualSerialCode['Value'];
                                            $query .= "($incrementId,'$virutlSku','','$serialCodeNo','$exprieDate','$pin','$secondSerialCode','$url','$startDate','$value',1,'auto','$customerEmail'),";
                                        }
                                    }
                                }
                                if (isset($sageVirtualProductReturnData['bundle'])) {
                                    foreach ($sageVirtualProductReturnData['bundle'] as $bundleSku => $bundleSkuItems) {
                                        foreach ($bundleSkuItems['sku'] as $bundleChildSku => $bundleChildItem) {
                                            foreach ($bundleChildItem as $bundleSerialCode) {
                                                $serialCodeNo = $bundleSerialCode['SerialNumber'];
                                                $exprieDate = $bundleSerialCode['ExpireDate'];
                                                $pin = $bundleSerialCode['PIN'];
                                                $secondSerialCode = $bundleSerialCode['SecondSerialCode'];
                                                $url = $bundleSerialCode['URL'];
                                                $startDate = $bundleSerialCode['StartDate'];
                                                $value = $bundleSerialCode['Value'];
                                                $query .= "($incrementId,'$bundleChildSku','$bundleSku','$serialCodeNo','$exprieDate','$pin','$secondSerialCode','$url','$startDate','$value',1,'auto','$customerEmail'),";
                                            }
                                        }
                                    }
                                }
                            }

                            if ($this->useSerlizedSageSerialCodes == 1) {
                                if (!empty($serlizedApiError)) {
                                    $message = "Serlized product has serial code issue";
                                    $this->pdfHelper->sendEmailAdminProductNotExist($serlizedApiError, $incrementId, $message);
                                } else {
                                    if (!empty($sageSerlizedVirtualProductReturnData)) {
                                        if (isset($sageSerlizedVirtualProductReturnData['virtual']['sku'])) {
                                            foreach ($sageSerlizedVirtualProductReturnData['virtual']['sku'] as $virutlSku => $virtualItem) {
                                                foreach ($virtualItem as $virtualSerialCode) {
                                                    $serialCodeNo = $virtualSerialCode['SerialNumber'];
                                                    $exprieDate = $virtualSerialCode['ExpireDate'];
                                                    $pin = $virtualSerialCode['PIN'];
                                                    $secondSerialCode = $virtualSerialCode['SecondSerialCode'];
                                                    $url = $virtualSerialCode['URL'];
                                                    $startDate = $virtualSerialCode['StartDate'];
                                                    $value = $virtualSerialCode['Value'];
                                                    $query .= "($incrementId,'$virutlSku','','$serialCodeNo','$exprieDate','$pin','$secondSerialCode','$url','$startDate','$value',1,'auto',''),";
                                                }
                                            }
                                        }
                                        if (isset($sageSerlizedVirtualProductReturnData['bundle'])) {
                                            foreach ($sageSerlizedVirtualProductReturnData['bundle'] as $bundleSku => $bundleSkuItems) {
                                                foreach ($bundleSkuItems['sku'] as $bundleChildSku => $bundleChildItem) {
                                                    foreach ($bundleChildItem as $bundleSerialCode) {
                                                        $serialCodeNo = $bundleSerialCode['SerialNumber'];
                                                        $exprieDate = $bundleSerialCode['ExpireDate'];
                                                        $pin = $bundleSerialCode['PIN'];
                                                        $secondSerialCode = $bundleSerialCode['SecondSerialCode'];
                                                        $url = $bundleSerialCode['URL'];
                                                        $startDate = $bundleSerialCode['StartDate'];
                                                        $value = $bundleSerialCode['Value'];
                                                        $query .= "($incrementId,'$bundleChildSku','$bundleSku','$serialCodeNo','$exprieDate','$pin','$secondSerialCode','$url','$startDate','$value',1,'auto',''),";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $queryClean = substr($query, 0, -1);
                            $sql = "Insert Into " . $tableName . " (OrderID, sku, parentsku, SerialNumber,ExpireDate,PIN,SecondSerialCode,URL,StartDate,Value, status, mode, email)
                                              Values " . $queryClean . ";";
                            $connection->query($sql);
                        }
                        /*----------------Saving Serial codes in DB End ----------*/

                        if (empty($docMissedProductItems) && empty($productNotAvilable) && empty($this->isProductMissedJsonData)) {
                            //$logger->info("step2");
                            if (empty($apiError) && empty($this->errorList)) {
                                if (!empty($sageVirtualProductReturnData) && $this->useSageSerialCodes == 1) {
                                    foreach ($sageVirtualProductReturnData as $productType => $serialCodeItem) {
                                        //$logger->info("Has");
                                        $filesCreate = $this->createDocument($productType, $serialCodeItem);
                                    }
                                }
                                if ($this->useSerialCodesFromTable == 1 && $this->serialCodesQtyIssueFromTable == 0) {
                                    foreach ($serialItemsFromTable as $productType => $serialCodeItem) {
                                        //$logger->info("Has1");
                                        $filesCreate = $this->createDocument($productType, $serialCodeItem);
                                    }
                                }
                            }

                            if ($apiError != '') {
                                $message = $apiError;
                                $subject = "Api Error";
                                $this->errorList['apierror'] = 'Order-Id (' . $orderId . ')- ' . $message;
                                $this->pdfHelper->sendEmailToAdminPDFissue($empty = '', $incrementId, $message);
                                $this->createlog($this->errorList);
                            }
                        } else {
                            //$logger->info("step3");
                            if ($docMissedProductItems) {
                                $missedProductSku = implode(",", array_unique($docMissedProductItems));
                                $message = "Order-Id $orderId- These products are missed the documents";
                                $this->errorList['docmissed'] = $message . ',' . $missedProductSku;
                                $this->pdfHelper->sendEmailToAdminPDFissue($missedProductSku, $incrementId, $message);
                                $this->createlog($this->errorList);
                            } elseif ($productNotAvilable) {
                                $productNotAvilablelist = implode(",", array_unique($productNotAvilable));
                                $message = "Order-Id $orderId- These products are not avilable";
                                $this->errorList['productsmissed'] = $message . ',' . $productNotAvilablelist;
                                $this->pdfHelper->sendEmailToAdminPDFissue($productNotAvilablelist, $incrementId, $message);
                                $this->createlog($this->errorList);
                            } elseif ($this->isProductMissedJsonData) {
                                $jsonDataNotAvilable = implode(",", array_unique($isProductjsonDataMissed));
                                $message = "Order-Id $orderId- Product Json Data Missed";
                                $this->errorList['productjsonmissed'] = $message . ',' . $jsonDataNotAvilable;
                                $this->pdfHelper->sendEmailToAdminPDFissue($jsonDataNotAvilable, $incrementId, $message);
                                $this->createlog($this->errorList);
                            }

                        }
                    }else{
                        $jsonDataNotAvilable = implode(",", array_unique($isProductjsonDataMissed));
                        $message = "Order-Id $orderId- Sage Request is empty";
                        $this->errorList['productjsonmissed'] = $message . ',' . $jsonDataNotAvilable;
                        $this->pdfHelper->sendEmailToAdminPDFissue($jsonDataNotAvilable, $incrementId, $message);
                        $this->createlog($this->errorList);
                    }
                }


                if (($this->virtualProductfilesListArray['pdf'] || $this->bundleProductfilesListArray['pdf']) && empty($this->pdfGenerationError) && empty($this->errorList)) {
                    $mergedFileName = $incrementId . ".pdf";
                    $query = '';
                    //$customerEmail = $customerData['email'];
                    $this->mergePdf($mergedFileName);
                    if (file_exists($this->mergedPdfFilesPath . '/' . $mergedFileName)) {
                        /*if($this->useSageSerialCodes == 1) {
                            $sql = "update " . $tableName . " set status = 1 , email = '$customerEmail' where OrderID = ".$incrementId;
                            $connection->query($sql);
                        }*/
                        $fileUrl = $this->mergedPdfFilesPath . '/' . $mergedFileName;

                        if ($emailStauts) {
                            $mailStatus = $this->pdfHelper->sendPdfToCustomerEmail($fileUrl, $customerData, $incrementId);
                        }
                    }
                }

                if(!empty($this->errorList) || !empty($this->pdfGenerationError)){
                    $this->createlog($this->errorList);
                    $status['status'] = 'error';
                    $status['message'] = $this->errorList;
                }else{
                    $status['status'] = 'success';
                    $this->updateOrderStatus($orderStatusId);
                }
            } catch (\Exception $e) {
                $logger->info($e->getMessage());
                $status['status'] = 'error';
                $status['message'] = array("error"=>$e->getMessage());
                $this->createLog("Order-Id $orderId- createPdfConcept => ".$e->getMessage());
                $errorMessage = "Order-Id $orderId- Error while creating the PDF(createPdfConcept) => ".$e->getMessage();
                $this->pdfGenerationError[] = $e->getMessage();
                //$this->pdfHelper->sendEmailToAdminPDFissue($productSku = '', $incrementId, $errorMessage);
            }
            return $status;
        }
    }

    public function updateOrderStatus($orderStatusId){
        $orderPdfObjUpdate = $this->orderPdf->load($orderStatusId);
        $updatedTime = date("Y-m-d H:i:s");
        $orderPdfObjUpdate->setStatus("completed");
        $orderPdfObjUpdate->setUpdatedAt($updatedTime);
        $orderPdfObjUpdate->save();
    }

    public function setOrderStatus(){
        $incrementId = $this->orderId;
        $presentTime = date("Y-m-d H:i:s");
        $orderStatusColl = $this->orderPdf->getCollection();
        $orderStatusColl->addFieldToFilter('order_id', $incrementId);
        $orderStatusColl->getFirstItem();
        $orderStatusCollData = $orderStatusColl->getData();
        if (empty($orderStatusColl->getData())) {
            $orderPdfObj = $this->orderPdf;
            $orderPdfObj->setOrderId($incrementId);
            $orderPdfObj->setStatus('pending');
            $orderPdfObj->setCreatedAt($presentTime);
            $orderPdfStatus = $orderPdfObj->save();
            $orderStatusId = $orderPdfStatus->getId();
        } else {
            $orderStatusId = $orderStatusCollData[0]['id'];
        }
        return $orderStatusId;
    }

    public function serlizedSerialCodesExistList($serialItemsReqData,$type){
        $result = array();
        if($type == 'virtual'){
            foreach ($serialItemsReqData as $serialItem){
                $stringToArray = explode(',',$serialItem);
                $orderSerialCodesColl = $this->orderItemSerialcode->getCollection();
                $orderSerialCodesColl->addFieldToFilter('OrderID',$stringToArray[1]);
                $orderSerialCodesColl->addFieldToFilter('sku',$stringToArray[2]);
                $orderSerialCodesColl->addFieldToFilter('parentsku','');
                $orderSerialCodesColl->addFieldToFilter('status',1);
                $result['sku'][$stringToArray[2]] = $orderSerialCodesColl->getData();
                $this->orderSerlizedSerialCodesQtyFromTable += count($orderSerialCodesColl->getData());
            }
        }
        if($type == 'bundle'){
            foreach ($serialItemsReqData as $bundleSku => $bundleSerialItem){
                foreach ($bundleSerialItem as $childSerialItem){
                    $stringToArrayBundle = explode(',',$childSerialItem);
                    $orderBSerialCodesColl = $this->orderItemSerialcode->getCollection();
                    $orderBSerialCodesColl->addFieldToFilter('OrderID',$stringToArrayBundle[1]);
                    $orderBSerialCodesColl->addFieldToFilter('sku',$stringToArrayBundle[2]);
                    $orderBSerialCodesColl->addFieldToFilter('parentsku',$bundleSku);
                    $orderBSerialCodesColl->addFieldToFilter('status',1);
                    $result[$bundleSku]['sku'][$stringToArrayBundle[2]] = $orderBSerialCodesColl->getData();
                    $this->orderSerlizedSerialCodesQtyFromTable += (int)count($orderBSerialCodesColl->getData());
                }
            }
        }
        return $result;
    }

    public function serialCodesExistList($serialItemsReqData,$type){
        $result = array();
        if($type == 'virtual'){
            foreach ($serialItemsReqData as $serialItem){
                $stringToArray = explode(',',$serialItem);
                $orderSerialCodesColl = $this->orderItemSerialcode->getCollection();
                $orderSerialCodesColl->addFieldToFilter('OrderID',$stringToArray[1]);
                $orderSerialCodesColl->addFieldToFilter('sku',$stringToArray[2]);
                $orderSerialCodesColl->addFieldToFilter('parentsku','');
                $orderSerialCodesColl->addFieldToFilter('status',1);
                $result['sku'][$stringToArray[2]] = $orderSerialCodesColl->getData();
                $this->orderSerialCodesQtyFromTable += count($orderSerialCodesColl->getData());
            }
        }
        if($type == 'bundle'){
            foreach ($serialItemsReqData as $bundleSku => $bundleSerialItem){
                foreach ($bundleSerialItem as $childSerialItem){
                    $stringToArrayBundle = explode(',',$childSerialItem);
                    $orderBSerialCodesColl = $this->orderItemSerialcode->getCollection();
                    $orderBSerialCodesColl->addFieldToFilter('OrderID',$stringToArrayBundle[1]);
                    $orderBSerialCodesColl->addFieldToFilter('sku',$stringToArrayBundle[2]);
                    $orderBSerialCodesColl->addFieldToFilter('parentsku',$bundleSku);
                    $orderBSerialCodesColl->addFieldToFilter('status',1);
                    $result[$bundleSku]['sku'][$stringToArrayBundle[2]] = $orderBSerialCodesColl->getData();
                    $this->orderSerialCodesQtyFromTable += (int)count($orderBSerialCodesColl->getData());
                }
            }
        }
        return $result;
    }

    /*method for crating docuemnt*/
    public function createDocument($productType,$serialCodeDatas){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-createDocument.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        try {
            if(!empty($serialCodeDatas)){
                if($productType == 'bundle'){
                    foreach ($serialCodeDatas as $bundleSku => $bundleItemData) {
                        $this->createBundleProductDoc($bundleSku, $bundleItemData['sku']);
                    }
                }
                if($productType == 'virtual'){
                    foreach ($serialCodeDatas['sku'] as $productSku => $virutalItemData) {
                        $this->createVirtualProductDoc($productSku, $virutalItemData);
                    }
                }
            }
        } catch (\Exception $e) {
            $logger->info($e->getMessage());
            $this->createLog("Order-Id $this->orderId- createDocument => ".$e->getMessage());
            $errorMessage = "Order-Id $this->orderId- createDocument => ".$e->getMessage();
            $this->pdfGenerationError[] = $e->getMessage();
            $this->pdfHelper->sendEmailToAdminPDFissue($productSku = '', $this->incrementId,$errorMessage);
            $status = FALSE;
        }

    }

    protected function createBundleProductDoc($bundleSku,$bundleItemData){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-createBundleProductDoc.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        try {
            $bundleSkusearchResult = array_search($bundleSku,$this->isPdfGroupTicket);

            $productRepository = $this->productRepository;
            $productObj = $productRepository->get($bundleSku);

            $productAttributeJsonData = $productObj->getProductJsonFormat();
            $productAttributeData = (array)json_decode($productAttributeJsonData, TRUE);
            $productCustomOptions = $this->productOptions->getProductOptionCollection($productObj);

            $docFilePath = $this->productDocsFilePath[$bundleSku];

            $docsSaveFilename = "ticket-" . $this->orderId . '-' . str_replace("/","-",$bundleSku) . ".docx";
            $pdfFilename = "ticket-" . $this->orderId . '-' . str_replace("/","-",$bundleSku) . ".pdf";
            $serialCodeItemsCount = 0;

            $isGroupTicket = array_search($bundleSku,$this->isPdfGroupTicket);

            if(is_numeric($isGroupTicket)){
                $this->bunldeGroupTicketCreate($bundleSku,$bundleItemData,$productAttributeData,$docFilePath,
                    $productObj,$productCustomOptions,$docsSaveFilename,$pdfFilename);

            }else{
                $this->bundeTicket($bundleSku,$bundleItemData,$productAttributeData,$docFilePath,
                    $productObj,$productCustomOptions);
            }


        }catch (\Exception $e){
            $logger->info($e->getMessage());
            $this->createLog("Order-Id $this->orderId- createBundleProductDoc => ".$e->getMessage());
            $errorMessage = "Order-Id $this->orderId- createBundleProductDoc => ".$e->getMessage();
            $this->pdfGenerationError[] = $e->getMessage();
            $this->pdfHelper->sendEmailToAdminPDFissue($bundleSku, $this->incrementId, $errorMessage);
        }
    }

    public function bundeTicket($bundleSku,$bundleItemData,$productAttributeData,$docFilePath,
                                $productObj,$productCustomOptions){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-bundeTicket-NonGroupTicket.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try{
            foreach ($bundleItemData as $budleItems){
                foreach ($budleItems as $budleItem){
                    $docsSaveFilename = "ticket-" . $this->orderId . '-' . $bundleSku .'-'. str_replace("/","-",$budleItem['SerialNumber']).".docx";
                    $pdfFilename = "ticket-" . $this->orderId . '-' . $bundleSku .'-'. str_replace("/","-",$budleItem['SerialNumber']). ".pdf";

                    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($docFilePath);
                    //$templateProcessor->cloneRow('sno', 1);
                    //$templateProcessor->setValue('sno#1', '1');

                    foreach ($productAttributeData as $productAttributeItem) {
                        if ($productAttributeItem['type'] == 'image') {
                            if ($productAttributeItem['source'] == 'attribute') {
                                $attributeVariableData = $productObj->getData($productAttributeItem['variable']);
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }else {
                                    $width = 100;
                                    $height = 100;
                                    if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                        $width = $productAttributeItem['width'];
                                    }
                                    if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                        $height = $productAttributeItem['height'];
                                    }
                                    $productBaseDirPath = $this->mediaPath.'catalog/product';
                                    $productImagePath = $productBaseDirPath.$attributeVariableData;
                                    if(file_exists($productImagePath)){
                                        $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $productImagePath, 'size' => array($width, $height)));
                                    }else{
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    }
                                }
                            } elseif ($productAttributeItem['source'] == 'customOption') {
                                if(isset($this->itemData[$bundleSku]['product_options']['options'])){
                                    $width = 100;
                                    $height = 100;
                                    if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                        $width = $productAttributeItem['width'];
                                    }
                                    if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                        $height = $productAttributeItem['height'];
                                    }

                                    $orderItemCustomOptions = $this->itemData[$bundleSku]['product_options']['options'];
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
                                $width = 100;
                                $height = 100;
                                if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                    $width = $productAttributeItem['width'];
                                }
                                if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                    $height = $productAttributeItem['height'];
                                }

                                $mediaPath = $this->pdfHelper->getMediaBaseDir();
                                $scopeConfig = $this->scopeConfig;
                                $configPath = "design/header/logo_src";
                                $filename = $scopeConfig->getValue($configPath,
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                                );
                                $imagePath = $mediaPath . 'logo/' . $filename;
                                if (file_exists($imagePath)) {
                                    $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $imagePath, 'size' => array($width, $height)));
                                } else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }
                            }
                        } elseif ($productAttributeItem['type'] == 'text') {
                            if ($productAttributeItem['source'] == 'attribute') {
                                $attributeVariableData = $productObj->getData($productAttributeItem['variable']);
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }else {
                                    //$templateProcessor->setValue($productAttributeItem['variable'], str_replace('&', '&amp;', $attributeVariableData));
                                    if($productAttributeItem['variable'] == 'short_description' || $productAttributeItem['variable'] == 'description' || $productAttributeItem['variable'] == 'short_description_pdf'){
                                        $toOpenXML = \HTMLtoOpenXML::getInstance()->fromHTML(str_replace('&', '&amp;',$attributeVariableData));
                                        $templateProcessor->setValue($productAttributeItem['variable'], $toOpenXML);
                                    }else {
                                        $templateProcessor->setValue($productAttributeItem['variable'], str_replace('&', '&amp;', $attributeVariableData));
                                    }
                                }
                            }elseif ($productAttributeItem['source'] == 'serialCode'){
                                $attributeVariableData = $budleItem[$productAttributeItem['variable']];
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                                }
                            }elseif ($productAttributeItem['source'] == 'orderData'){
                                if(isset($this->itemData[$bundleSku][$productAttributeItem['variable']]))
                                    $attributeVariableData = $this->itemData[$bundleSku][$productAttributeItem['variable']];
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }else{
                                    $templateProcessor->setValue($productAttributeItem['variable'], str_replace('&', '&amp;', $attributeVariableData));
                                }
                            }elseif ($productAttributeItem['source'] == 'customOption') {
                                if(isset($this->itemData[$bundleSku]['product_options']['options'])){
                                    $orderItemCustomOptions = $this->itemData[$bundleSku]['product_options']['options'];
                                    $key = array_search($productAttributeItem['variable'], array_column($orderItemCustomOptions, 'label'));
                                    if (is_numeric($key)) {
                                        $customOptionVariableData = $orderItemCustomOptions[$key]['value'];
                                        $templateProcessor->setValue($productAttributeItem['variable'], $customOptionVariableData);
                                    } else {
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    }
                                }else{
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }
                            }elseif ($productAttributeItem['source'] == 'block'){
                                if(isset($productAttributeItem['id'])){
                                    $cmsBlockObj = $this->cmsBlock->setStoreId($this->storeId)->load($productAttributeItem['id']);
                                    $cmsBlockData = $cmsBlockObj->getData();
                                    if(isset($cmsBlockData['content']) && $cmsBlockData['content']){
                                        #$content = strip_tags($cmsBlockData['content']);
                                        //$templateProcessor->setValue($productAttributeItem['variable'],$content);
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
                        } elseif ($productAttributeItem['type'] == 'date') {
                            if ($productAttributeItem['source'] == 'other') {
                                if (!$productAttributeItem['duration']) {
                                    $logger->info("Date Duration not valid ".$this->orderId .' product sku '.$bundleSku);
                                }
                                $currentDate = $this->date->gmtDate();
                                if(isset($productAttributeItem['duration'])) {
                                    $expDate = '';
                                    $dateData = explode('_', $productAttributeItem['duration']);
                                    $logger->info($dateData);
                                    if ($dateData[1] == 'M') {
                                        $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " months"));
                                    } elseif ($dateData[1] == 'D') {
                                        $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " days"));
                                    } elseif ($dateData[1] == 'Y') {
                                        $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " years"));
                                    } else {
                                        $logger->info("Something wrong about Date Duration " . $this->orderId . ' product sku ' . $bundleSku);
                                    }
                                    if (!$expDate) {
                                        $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                    }else {
                                        $templateProcessor->setValue($productAttributeItem['variable'], $expDate);
                                    }
                                }else{
                                    $templateProcessor->setValue($productAttributeItem['variable'], $currentDate);
                                }
                            }elseif($productAttributeItem['source'] == 'serialCode'){
                                $attributeVariableData = $budleItem[$productAttributeItem['variable']];
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], str_replace('&', '&amp;', $attributeVariableData));
                                }
                            }elseif($productAttributeItem['source'] == 'customOption'){
                                if(isset($this->itemData[$bundleSku]['product_options']['options'])){
                                    $orderItemCustomOptions = $this->itemData[$bundleSku]['product_options']['options'];
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
                                                $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " months"));
                                            } elseif ($dateData[1] == 'D') {
                                                $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " days"));
                                            } elseif ($dateData[1] == 'Y') {
                                                $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " years"));
                                            } else {
                                                $logger->info("Something wrong about Date Duration " .$this->orderId .'=>'.$bundleSku);
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
                        }elseif ($productAttributeItem['type'] == 'barcode') {
                            $barwidth = 100;
                            $barheight = 200;

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

                            $docImagewidth = 145;
                            $docImageheight = 55;

                            $barcodeSaveFileName = "barcode-" . $budleItem['OrderID'] . '-item-' . trim(str_replace("/","-",$budleItem['SerialNumber'])) . ".png";

                            $barodePattern = 'code39';
                            if(isset($productAttributeItem['pattern']) && $productAttributeItem['pattern'] == 'code128'){
                                $barodePattern = 'code128';
                            }
                            $barcodeFilePath = $this->barCodeCreate($barcodeSaveFileName, $budleItem['SerialNumber'], $barwidth, $barheight,$barodePattern);
                            if (file_exists($barcodeFilePath)) {
                                $barCodeFileName = $barcodeSaveFileName;
                                $templateProcessor->setImg('barcode', array('src' => $barcodeFilePath, 'size' => array($docImagewidth, $docImageheight)));  //Width&height
                            }else{
                                $logger->info(" Bar Code is missed ".$this->orderId." Product ".$bundleSku);
                                $templateProcessor->setValue('barcode', ' ');
                            }
                        } elseif ($productAttributeItem['type'] == 'qrcode') {

                            $barwidth = 100;
                            $barheight = 70;

                            $docImagewidth = 145;
                            $docImageheight = 50;

                            $serialCodeNumber = trim($budleItem['SerialNumber']);

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
                            $qrCodeSaveFileName = "qrcode-" . $budleItem['OrderID'] . '-item-' . str_replace("/","-",$serialCodeNumber) . ".png";
                            $qrCodeFilePath = $this->qrCodesFilePath . $qrCodeSaveFileName;

                            $qrCode = new QrCode($serialCodeNumber, $barwidth, $barheight); // text, width, height
                            $qrCode->setRenderer(new GoogleChartRenderer());
                            $qrCodeData = $qrCode->generate();

                            $imageString = imagecreatefromstring($qrCodeData);
                            if ($imageString !== false) {
                                header('Content-Type: image/png');
                                imagepng($imageString, $qrCodeFilePath);
                            }

                            if (file_exists($qrCodeFilePath)) {
                                $templateProcessor->setImg('qrcode', array('src' => $qrCodeFilePath, 'size' => array($docImagewidth, $docImageheight)));  /*Width&height*/
                                $status['barcode'] = $qrCodeSaveFileName;
                            } else {
                                $logger->info("Qr-Code not exist for" . $this->orderId . ' product is ' . $bundleSku);
                                $templateProcessor->setValue('qrcode', ' ');
                            }
                        } elseif ($productAttributeItem['type'] == 'cdate') {
                            if ($productAttributeItem['source'] == 'other') {
                                $currentDate = $this->date->gmtDate();
                                $templateProcessor->setValue($productAttributeItem['variable'], $currentDate);
                            }
                        }elseif ($productAttributeItem['type'] == 'url'){
                            if($productAttributeItem['source'] == 'serialCode') {
                                $attributeVariableData = $budleItem[$productAttributeItem['variable']];
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'] , ' ');
                                } else {
                                    $cleanAttributeVariableData = str_replace('&', '&amp;', $attributeVariableData);
                                    $templateProcessor->setValue($productAttributeItem['variable'], $cleanAttributeVariableData);
                                }
                            }
                        }
                    }

                    $templateProcessor->saveAs($this->temporaryDocFilesPath . '/' . $docsSaveFilename);
                    if (file_exists($this->temporaryDocFilesPath . '/' . $docsSaveFilename)) {
                        array_push($this->bundleProductfilesListArray['doc'], $this->temporaryDocFilesPath . '/' . $docsSaveFilename);
                        /*Creating PDF for bundle product*/
                        $pdfSaveFilename = $this->createPdf($this->temporaryDocFilesPath, $docsSaveFilename, $this->temporaryPdfFilesPath, $pdfFilename);
                        if ($pdfSaveFilename) {
                            array_push($this->bundleProductfilesListArray['pdf'], $this->temporaryPdfFilesPath . '/' . $pdfFilename);
                        }
                    } else {
                        $logger->info("Bundle Ticket Document Doest exit" . $docsSaveFilename);
                    }
                }
            }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
            $this->createLog("Order-Id $this->orderId- bundeTicket => ".$e->getMessage());
            $errorMessage = "Order-Id $this->orderId- bundeTicket => ".$e->getMessage();
            $this->pdfGenerationError[] = $e->getMessage();
            $this->pdfHelper->sendEmailToAdminPDFissue($bundleSku, $this->incrementId, $errorMessage);
        }

    }



    public function bunldeGroupTicketCreate($bundleSku,$bundleItemData,$productAttributeData,$docFilePath,
                                            $productObj,$productCustomOptions,$docsSaveFilename,$pdfFilename){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-bunldeGroupTicketCreate.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try {
            $serialCodeItemsCount = 0;
            foreach ($bundleItemData as $bundleChilds) {
                $serialCodeItemsCount += count($bundleChilds);
            }

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($docFilePath);

            foreach ($productAttributeData as $productAttributeItem) {
                if ($productAttributeItem['type'] == 'image') {
                    if ($productAttributeItem['source'] == 'attribute') {
                        $attributeVariableData = $productObj->getData($productAttributeItem['variable']);
                        if (!$attributeVariableData) {
                            $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                        }else {
                            $width = 100;
                            $height = 100;
                            if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                $width = $productAttributeItem['width'];
                            }
                            if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                $height = $productAttributeItem['height'];
                            }
                            $productBaseDirPath = $this->mediaPath.'catalog/product';
                            $productImagePath = $productBaseDirPath.$attributeVariableData;
                            if(file_exists($productImagePath)){
                                $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $productImagePath, 'size' => array($width, $height)));
                            }else{
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }
                        }
                    } elseif ($productAttributeItem['source'] == 'customOption') {
                        if(isset($this->itemData[$bundleSku]['product_options']['options'])){
                            $width = 100;
                            $height = 100;
                            if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                $width = $productAttributeItem['width'];
                            }
                            if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                $height = $productAttributeItem['height'];
                            }
                            $orderItemCustomOptions = $this->itemData[$bundleSku]['product_options']['options'];
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
                        $width = 100;
                        $height = 100;
                        if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                            $width = $productAttributeItem['width'];
                        }
                        if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                            $height = $productAttributeItem['height'];
                        }

                        $mediaPath = $this->pdfHelper->getMediaBaseDir();
                        $scopeConfig = $this->scopeConfig;
                        $configPath = "design/header/logo_src";
                        $filename = $scopeConfig->getValue($configPath,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        );
                        $imagePath = $mediaPath . 'logo/' . $filename;
                        if (file_exists($imagePath)) {
                            $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $imagePath, 'size' => array($width, $height)));
                        } else {
                            $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                        }
                    }
                } elseif ($productAttributeItem['type'] == 'text') {
                    if ($productAttributeItem['source'] == 'attribute') {
                        $attributeVariableData = $productObj->getData($productAttributeItem['variable']);
                        if (!$attributeVariableData) {
                            $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                        }else{
                            //$templateProcessor->setValue($productAttributeItem['variable'], str_replace('&', '&amp;', $attributeVariableData));
                            if($productAttributeItem['variable'] == 'short_description' || $productAttributeItem['variable'] == 'description' || $productAttributeItem['variable'] == 'short_description_pdf'){
                                $toOpenXML = \HTMLtoOpenXML::getInstance()->fromHTML(str_replace('&', '&amp;',$attributeVariableData));
                                $templateProcessor->setValue($productAttributeItem['variable'], $toOpenXML);
                            }else {
                                $templateProcessor->setValue($productAttributeItem['variable'], str_replace('&', '&amp;', $attributeVariableData));
                            }
                        }
                    }elseif ($productAttributeItem['source'] == 'orderData'){
                        if(isset($this->itemData[$bundleSku][$productAttributeItem['variable']])){
                            $attributeVariableData = $this->itemData[$bundleSku][$productAttributeItem['variable']];
                            if (!$attributeVariableData) {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }else{
                                $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                            }
                        }
                    }elseif ($productAttributeItem['source'] == 'customOption') {
                        if(isset($this->itemData[$bundleSku]['product_options']['options'])){
                            $orderItemCustomOptions = $this->itemData[$bundleSku]['product_options']['options'];
                            $key = array_search($productAttributeItem['variable'], array_column($orderItemCustomOptions, 'label'));
                            if (is_numeric($key)) {
                                $customOptionVariableData = $orderItemCustomOptions[$key]['value'];
                                $templateProcessor->setValue($productAttributeItem['variable'], $customOptionVariableData);
                            } else {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }
                        }else{
                            $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                        }
                    }elseif ($productAttributeItem['source'] == 'block'){
                        if(isset($productAttributeItem['id'])){
                            $cmsBlockObj = $this->cmsBlock->setStoreId($this->storeId)->load($productAttributeItem['id']);
                            $cmsBlockData = $cmsBlockObj->getData();
                            if(isset($cmsBlockData['content']) && $cmsBlockData['content']){

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
                } elseif ($productAttributeItem['type'] == 'date') {
                    if ($productAttributeItem['source'] == 'other') {
                        if (!$productAttributeItem['duration']) {
                            $logger->info("Date Duration not valid ".$this->orderId .'=>'.$bundleSku);
                        }
                        $currentDate = $this->date->gmtDate();
                        if(isset($productAttributeItem['duration'])) {
                            $expDate = '';
                            $dateData = explode('_', $productAttributeItem['duration']);
                            if ($dateData[1] == 'M') {
                                $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " months"));
                            } elseif ($dateData[1] == 'D') {
                                $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " days"));
                            } elseif ($dateData[1] == 'Y') {
                                $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " years"));
                            } else {
                                $logger->info("Something wrong about Date Duration " . $this->orderId . '=>' . $bundleSku);
                            }
                            if (!$expDate) {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            } else {
                                $templateProcessor->setValue($productAttributeItem['variable'], $expDate);
                            }
                        }else{
                            $templateProcessor->setValue($productAttributeItem['variable'], $currentDate);
                        }
                    }elseif($productAttributeItem['source'] == 'customOption'){
                        if(isset($this->itemData[$bundleSku]['product_options']['options'])){
                            $orderItemCustomOptions = $this->itemData[$bundleSku]['product_options']['options'];
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
                                        $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " months"));
                                    } elseif ($dateData[1] == 'D') {
                                        $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " days"));
                                    } elseif ($dateData[1] == 'Y') {
                                        $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " years"));
                                    } else {
                                        $logger->info("Something wrong about Date Duration " .$this->orderId .'=>'.$bundleSku);
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
                }
            }

            $templateProcessor->cloneRow('sno', $serialCodeItemsCount);

            $i = 1;
            //echo "<pre>"; print_r($bundleItemData);
            foreach ($bundleItemData as $sku => $budleItems) {
                foreach ($budleItems as $budleItem) {
                    foreach ($productAttributeData as $productAttributeItem) {
                        $templateProcessor->setValue('sno' . '#' . $i, $i);
                        if ($productAttributeItem['type'] == 'text') {
                            if ($productAttributeItem['source'] == 'serialCode') {
                                $attributeVariableData = $budleItem[$productAttributeItem['variable']];
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'] . '#' . $i, ' ');
                                }else {
                                    $templateProcessor->setValue($productAttributeItem['variable'] . '#' . $i, $attributeVariableData);
                                }
                            }elseif ($productAttributeItem['source'] == 'orderData') {
                                if($productAttributeItem['variable'] == 'childname'){
                                    $attributeVariableData = str_replace('&', '&amp;', $this->itemData[$sku]['name']);
                                    if (!$attributeVariableData) {
                                        $templateProcessor->setValue($productAttributeItem['variable'] . '#' . $i, ' ');
                                    }else {
                                        $templateProcessor->setValue($productAttributeItem['variable'] . '#' . $i, $attributeVariableData);
                                    }
                                }
                            }
                        } elseif ($productAttributeItem['type'] == 'barcode') {
                            $barwidth = 100;
                            $barheight = 200;

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

                            $docImagewidth = 145;
                            $docImageheight = 50;

                            $barcodeSaveFileName = "barcode-" . $budleItem['OrderID'] . '-item-' . trim(str_replace("/","-",$budleItem['SerialNumber'])) . ".png";

                            $barodePattern = 'code39';
                            if(isset($productAttributeItem['pattern']) && $productAttributeItem['pattern'] == 'code128'){
                                $barodePattern = 'code128';
                            }

                            $barcodeFilePath = $this->barCodeCreate($barcodeSaveFileName, $budleItem['SerialNumber'], $barwidth, $barheight,$barodePattern);
                            if (file_exists($barcodeFilePath)) {
                                $barCodeFileName = $barcodeSaveFileName;
                                $templateProcessor->setImg('barcode' . '#' . $i, array('src' => $barcodeFilePath, 'size' => array($docImagewidth, $docImageheight)));  //Width&height
                            }else{
                                $logger->info("Bar Code not exist for".$this->orderId .' product is '.$bundleSku);
                                $templateProcessor->setValue('barcode', ' ');
                            }
                        } elseif ($productAttributeItem['type'] == 'qrcode') {

                            $barwidth = 100;
                            $barheight = 70;

                            $docImagewidth = 145;
                            $docImageheight = 50;

                            $serialCodeNumber = trim($budleItem['SerialNumber']);

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
                            $qrCodeSaveFileName = "qrcode-" . $budleItem['OrderID'] . '-item-' . str_replace("/","-",$serialCodeNumber) . ".png";
                            $qrCodeFilePath = $this->qrCodesFilePath . $qrCodeSaveFileName;

                            $qrCode = new QrCode($serialCodeNumber, $barwidth, $barheight); // text, width, height
                            $qrCode->setRenderer(new GoogleChartRenderer());
                            $qrCodeData = $qrCode->generate();

                            $imageString = imagecreatefromstring($qrCodeData);
                            if ($imageString !== false) {
                                header('Content-Type: image/png');
                                imagepng($imageString, $qrCodeFilePath);
                            }

                            if (file_exists($qrCodeFilePath)) {
                                $templateProcessor->setImg('qrcode', array('src' => $qrCodeFilePath, 'size' => array($docImagewidth, $docImageheight)));  /*Width&height*/
                                $status['barcode'] = $qrCodeSaveFileName;
                            } else {
                                $logger->info("Qr-Code not exist for" . $this->orderId . ' product is ' . $bundleSku);
                                $templateProcessor->setValue('qrcode', ' ');
                            }
                        } elseif ($productAttributeItem['type'] == 'cdate') {
                            if ($productAttributeItem['source'] == 'other') {
                                $currentDate = $this->date->gmtDate();
                                $templateProcessor->setValue($productAttributeItem['variable'], $currentDate);
                            }
                        }elseif ($productAttributeItem['type'] == 'date'){
                            if($productAttributeItem['source'] == 'serialCode'){
                                $attributeVariableData = $budleItem[$productAttributeItem['variable']];
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'] . '#'.$i, ' ');
                                }else {
                                    $templateProcessor->setValue($productAttributeItem['variable'] . '#'.$i, $attributeVariableData);
                                }
                            }
                        }elseif ($productAttributeItem['type'] == 'url'){
                            if($productAttributeItem['source'] == 'serialCode') {
                                $attributeVariableData = $budleItem[$productAttributeItem['variable']];
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'] . '#'.$i, ' ');
                                }else {
                                    $cleanAttributeVariableData = str_replace('&', '&amp;', $attributeVariableData);
                                    $templateProcessor->setValue($productAttributeItem['variable'] . '#'.$i, $cleanAttributeVariableData);
                                }
                            }
                        }
                    }
                    $i++;
                }
            }

            $templateProcessor->saveAs($this->temporaryDocFilesPath . '/' . $docsSaveFilename);
            if (file_exists($this->temporaryDocFilesPath . '/' . $docsSaveFilename)) {
                array_push($this->bundleProductfilesListArray['doc'], $this->temporaryDocFilesPath . '/' . $docsSaveFilename);
                /*Creating PDF for bundle product*/
                $pdfSaveFilename = $this->createPdf($this->temporaryDocFilesPath, $docsSaveFilename, $this->temporaryPdfFilesPath, $pdfFilename);
                if ($pdfSaveFilename) {
                    array_push($this->bundleProductfilesListArray['pdf'], $this->temporaryPdfFilesPath . '/' . $pdfFilename);
                }
            } else {
                $logger->info("Bundle Ticket Document Doest exit" . $docsSaveFilename);
            }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
            $this->createLog("Order-Id $this->orderId- bunldeGroupTicketCreate => ".$e->getMessage());
            $errorMessage = "Order-Id $this->orderId- bunldeGroupTicketCreate => ".$e->getMessage();
            $this->pdfGenerationError[] = $e->getMessage();
            $this->pdfHelper->sendEmailToAdminPDFissue($bundleSku, $this->incrementId, $errorMessage);
        }
    }

    protected function createVirtualProductDoc($productSku,$serialItems){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-createVirtualProductDoc.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try {
            $productRepository = $this->productRepository;
            $productObj = $productRepository->get($productSku);

            $productAttributeJsonData = $productObj->getProductJsonFormat();
            $productAttributeData = (array)json_decode($productAttributeJsonData, TRUE);

            $docFilePath = $this->productDocsFilePath[$productSku];

            foreach ($serialItems as $serialItem) {
                $docsSaveFilename = "ticket-" . $serialItem['OrderID'] . '-' . trim(str_replace("/","-",$serialItem['SerialNumber'])) . "-document.docx";
                $pdfFilename = "ticket-" . $serialItem['OrderID'] . '-' . trim(str_replace("/","-",$serialItem['SerialNumber'])) . "-document.pdf";
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($docFilePath);
                foreach ($productAttributeData as $productAttributeItem) {
                    if ($productAttributeItem['type'] == 'image') {
                        if ($productAttributeItem['source'] == 'attribute') {
                            $attributeVariableData = $productObj->getData($productAttributeItem['variable']);
                            if (!$attributeVariableData) {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }else {
                                $width = 100;
                                $height = 100;
                                if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                    $width = $productAttributeItem['width'];
                                }
                                if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                    $height = $productAttributeItem['height'];
                                }
                                $productBaseDirPath = $this->mediaPath.'catalog/product';
                                $productImagePath = $productBaseDirPath.$attributeVariableData;
                                if(file_exists($productImagePath)){
                                    $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $productImagePath, 'size' => array($width, $height)));
                                }else{
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }
                            }
                        } elseif ($productAttributeItem['source'] == 'customOption') {
                            if(isset($this->itemData[$productSku]['product_options']['options'])){
                                $width = 100;
                                $height = 100;
                                if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                    $width = $productAttributeItem['width'];
                                }
                                if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                    $height = $productAttributeItem['height'];
                                }
                                $orderItemCustomOptions = $this->itemData[$productSku]['product_options']['options'];
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
                            if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                $width = $productAttributeItem['width'];
                            }
                            if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                $height = $productAttributeItem['height'];
                            }

                            $mediaPath = $this->pdfHelper->getMediaBaseDir();
                            $scopeConfig = $this->scopeConfig;
                            $configPath = "design/header/logo_src";
                            $filename = $scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                            $imagePath = $mediaPath . 'logo/' . $filename;

                            if (file_exists($imagePath)) {
                                $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $imagePath, 'size' => array($width, $height)));
                            } else {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }
                        }
                    } elseif ($productAttributeItem['type'] == 'text') {
                        if ($productAttributeItem['source'] == 'attribute') {
                            $logger->info($productAttributeItem['variable']);
                            $attributeVariableData = $productObj->getData($productAttributeItem['variable']);
                            if (!$attributeVariableData) {
                                $logger->info("Empty value of".$productAttributeItem['variable']);
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }else{
                                //$templateProcessor->setValue($productAttributeItem['variable'], str_replace('&','&amp;',$attributeVariableData));
                                if($productAttributeItem['variable'] == 'short_description' || $productAttributeItem['variable'] == 'description' || $productAttributeItem['variable'] == 'short_description_pdf'){
                                    $logger->info($attributeVariableData);
                                    $toOpenXML = \HTMLtoOpenXML::getInstance()->fromHTML(str_replace('&', '&amp;',$attributeVariableData));
                                    $logger->info($toOpenXML);
                                    $templateProcessor->setValue($productAttributeItem['variable'], $toOpenXML);
                                }else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], str_replace('&', '&amp;', $attributeVariableData));
                                }
                            }
                        } elseif ($productAttributeItem['source'] == 'serialCode') {
                            $serialCodeNumber = trim($serialItem[$productAttributeItem['variable']]);
                            $attributeVariableData = $serialCodeNumber;
                            if (!$attributeVariableData) {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }else {
                                $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                            }
                        } elseif ($productAttributeItem['source'] == 'customOption') {
                            /*$itemData is the order Data*/
                            if(isset($this->itemData[$productSku]['product_options']['options'])){
                                $orderItemCustomOptions = $this->itemData[$productSku]['product_options']['options'];
                                $logger->info($orderItemCustomOptions);
                                $key = array_search($productAttributeItem['variable'], array_column($orderItemCustomOptions, 'label'));
                                if (is_numeric($key)) {
                                    $customOptionVariableData = $orderItemCustomOptions[$key]['value'];
                                    $templateProcessor->setValue($productAttributeItem['variable'], $customOptionVariableData);
                                } else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }
                            }else{
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }
                        }elseif ($productAttributeItem['source'] == 'orderData'){
                            if(isset($this->itemData[$productSku][$productAttributeItem['variable']])) {
                                $attributeVariableData = $this->itemData[$productSku][$productAttributeItem['variable']];
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                } else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], str_replace('&', '&amp;', $attributeVariableData));
                                }
                            }else{
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
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
                                $attributeVariableData = str_replace('&','&amp;', $this->customerData['toname']);
                                if (!$attributeVariableData) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }else{
                                    /*$logger->info("Customer name");
                                    $logger->info($attributeVariableData);
                                    $logger->info($this->customerData);*/

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

                        $docImagewidth = 145;
                        $docImageheight = 50;

                        $serialCodeNumber = trim($serialItem['SerialNumber']);

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


                        $barcodeSaveFileName = "barcode-" . $serialItem['OrderID'] . '-item-' . str_replace("/","-",$serialCodeNumber) . ".png";

                        $barodePattern = 'code39';
                        if(isset($productAttributeItem['pattern']) && $productAttributeItem['pattern'] == 'code128'){
                            $barodePattern = 'code128';
                        }

                        $barcodeFilePath = $this->barCodeCreate($barcodeSaveFileName, $serialCodeNumber, $barwidth, $barheight,$barodePattern);
                        if (file_exists($barcodeFilePath)) {
                            $templateProcessor->setImg('barcode', array('src' => $barcodeFilePath, 'size' => array($docImagewidth, $docImageheight)));  /*Width&height*/
                            $status['barcode'] = $barcodeSaveFileName;
                        }else{
                            $logger->info("Bar Code not exist for".$this->orderId .' product is '.$productSku);
                            $templateProcessor->setValue('barcode', ' ');
                        }
                    }elseif ($productAttributeItem['type'] == 'qrcode'){

                        $barwidth = 100;
                        $barheight = 70;

                        $docImagewidth = 145;
                        $docImageheight = 50;

                        $serialCodeNumber = trim($serialItem['SerialNumber']);

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
                        $qrCodeSaveFileName = "qrcode-" . $serialItem['OrderID'] . '-item-' . str_replace("/","-",$serialCodeNumber) . ".png";
                        $qrCodeFilePath = $this->qrCodesFilePath.$qrCodeSaveFileName;

                        $qrCode = new QrCode($serialCodeNumber, $barwidth, $barheight); // text, width, height
                        $qrCode->setRenderer(new GoogleChartRenderer());
                        $qrCodeData = $qrCode->generate();

                        $imageString = imagecreatefromstring($qrCodeData);
                        if ($imageString !== false) {
                            header('Content-Type: image/png');
                            imagepng($imageString,$qrCodeFilePath);
                        }

                        if (file_exists($qrCodeFilePath)) {
                            $templateProcessor->setImg('qrcode', array('src' => $qrCodeFilePath, 'size' => array($docImagewidth, $docImageheight)));  /*Width&height*/
                            $status['barcode'] = $qrCodeSaveFileName;
                        }else{
                            $logger->info("Qr-Code not exist for".$this->orderId .' product is '.$productSku);
                            $templateProcessor->setValue('qrcode', ' ');
                        }

                    } elseif ($productAttributeItem['type'] == 'date') {
                        if ($productAttributeItem['source'] == 'other') {
                            if (!isset($productAttributeItem['duration'])) {
                                $logger->info("Date Duration not valid ".$this->orderId .'=>'.$productSku);
                            }
                            $currentDate = $this->date->gmtDate();
                            if(isset($productAttributeItem['duration'])) {
                                $expDate = '';
                                $dateData = explode('_', $productAttributeItem['duration']);
                                if ($dateData[1] == 'M') {
                                    $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " months"));
                                } elseif ($dateData[1] == 'D') {
                                    $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " days"));
                                } elseif ($dateData[1] == 'Y') {
                                    $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " years"));
                                } else {
                                    $logger->info("Something wrong about Date Duration " . $this->orderId . '=>' . $productSku);
                                }
                                if (!$expDate) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], $expDate);
                                }
                            }else{
                                $templateProcessor->setValue($productAttributeItem['variable'], $currentDate);
                            }
                        }elseif($productAttributeItem['source'] == 'serialCode'){
                            $attributeVariableData = $serialItem[$productAttributeItem['variable']];
                            if (!$attributeVariableData) {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }else {
                                $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                            }
                        }elseif($productAttributeItem['source'] == 'customOption'){
                            if(isset($this->itemData[$productSku]['product_options']['options'])){
                                $orderItemCustomOptions = $this->itemData[$productSku]['product_options']['options'];
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
                                            $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " months"));
                                        } elseif ($dateData[1] == 'D') {
                                            $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " days"));
                                        } elseif ($dateData[1] == 'Y') {
                                            $expDate = date('d-m-Y', strtotime($currentDate . '+ ' . $dateData[0] . " years"));
                                        } else {
                                            $logger->info("Something wrong about Date Duration " .$this->orderId .'=>'.$productSku);
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
                            $attributeVariableData = $serialItem[$productAttributeItem['variable']];
                            if (!$attributeVariableData) {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }else {
                                $cleanAttributeVariableData = str_replace('&','&amp;', $attributeVariableData);
                                $templateProcessor->setValue($productAttributeItem['variable'], $cleanAttributeVariableData);
                            }
                        }
                    }
                }

                $templateProcessor->saveAs($this->temporaryDocFilesPath . '/' . $docsSaveFilename);
                if (file_exists($this->temporaryDocFilesPath . '/' . $docsSaveFilename)) {
                    //array_push($this->virtualProductfilesListArray['doc'], $serialCodeNumber);
                    $pdfFileCreate = $this->createPdf($this->temporaryDocFilesPath, $docsSaveFilename, $this->temporaryPdfFilesPath, $pdfFilename);
                    if (file_exists($this->temporaryPdfFilesPath . '/' . $pdfFilename)) {
                        array_push($this->virtualProductfilesListArray['pdf'], $this->temporaryPdfFilesPath . '/' . $pdfFilename);
                    }
                } else {
                    array_push($this->virtualProductfilesListArray['doc'], false);
                }

            }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
            $this->createLog("Order-Id $this->orderId- createVirtualProductDoc => ".$e->getMessage());
            $errorMessage = "Order-Id $this->orderId- createVirtualProductDoc => ".$e->getMessage();
            $this->pdfGenerationError[] = $e->getMessage();
            $this->pdfHelper->sendEmailToAdminPDFissue($productSku, $this->incrementId, $errorMessage);
        }
    }

    /* method for creating barcodes*/
    protected function barCodeCreate($barcodeSaveFileName,$serialcode,$barwidth,$barheight,$barodePattern){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-barCodeCreate.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        if($barodePattern == ''){
            $barodePattern == 'code39';
        }
        try{
            $barcodeOptions = array('text' => $serialcode,'barHeight'=> $barheight,'barWidth'=> $barwidth);

            $rendererOptions = array(
                //'topOffset' => 10,
                //'leftOffset' => 10,
            );
            $imageResource = Barcode::factory($barodePattern, 'image', $barcodeOptions, $rendererOptions)->draw();
            $filename = $this->barcodeFilesPath.$barcodeSaveFileName;
            imagepng($imageResource,$filename);
            return $this->barcodeFilesPath.$barcodeSaveFileName;
        }catch (\Exception $e){
            $logger->info("Issue creation barcode for ".$barcodeSaveFileName.' Order is '.$this->orderId."Error Messsage ".$e->getMessage());
            $this->createLog("Order-Id $this->orderId- barCodeCreate => ".$e->getMessage());
            $errorMessage = "Order-Id $this->orderId- barCodeCreate => ".$e->getMessage();
            $this->pdfGenerationError[] = $e->getMessage();
            $this->pdfHelper->sendEmailToAdminPDFissue($productSku = 'Serial code issue while Barcode creation'.$serialcode, $this->incrementId, $errorMessage);
        }
    }

    /*method for creating pdf file based on word document*/
    public function createPdf($docFileTemporaryPath,$docTemporaryFileName,$pdfDirectoryPath,$pdfSaveFilename){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-createPdf.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try {
            if (file_exists($docFileTemporaryPath . '/' . $docTemporaryFileName)) {

                //\Gears\Pdf::convert($docFileTemporaryPath . '/' . $docTemporaryFileName, $pdfDirectoryPath . '/' . $pdfSaveFilename);

                $docFile = $docFileTemporaryPath.$docTemporaryFileName;
                $pdfFile = $pdfDirectoryPath;
                exec('export HOME=/tmp && lowriter --headless --invisible --convert-to pdf:writer_pdf_Export --outdir '.$pdfFile.'  '.$docFile .' 2>&1', $output);
                //print_r($output);
                $this->createLog('export HOME=/tmp && lowriter --headless --invisible --convert-to pdf:writer_pdf_Export --outdir '.$pdfFile.'  '.$docFile.' 2>&1');
                //$this->createLog($output);

                if(empty($output)){
                    $errorMessage = "Order-Id $this->orderId- createPdf => Pdf error while executing shell script";
                    $this->pdfGenerationError[] = " Pdf error while executing shell script";
                    $this->pdfHelper->sendEmailToAdminPDFissue($productSku = $pdfSaveFilename, $this->incrementId, $errorMessage);
                }

                /* $document = new \Gears\Pdf($docFileTemporaryPath . '/' . $docTemporaryFileName);
                 $document->converter = function()
                 {
                     return new \Gears\Pdf\Docx\Converter\Unoconv();
                 };
                 $document->save($pdfDirectoryPath . '/' . $pdfSaveFilename);*/


                if(file_exists($pdfDirectoryPath . '/' . $pdfSaveFilename)) {
                    chmod($docFileTemporaryPath . '/' . $docTemporaryFileName,0777);
                    //unlink($templacePath . '/' . $docsSaveFilename);
                    $status = true;
                }else{
                    $status = false;
                }
            }
        } catch (\Exception $e) {
            $status = false;
            $logger->info("Error at Pdf Creation ".$pdfSaveFilename.' Order is '.$this->orderId."Error Messsage ".$e->getMessage());
            $this->createLog("Order-Id $this->orderId- createPdf => ".$e->getMessage());
            $errorMessage = "Order-Id $this->orderId- createPdf => ".$e->getMessage();
            $this->pdfGenerationError[] = $e->getMessage();
            $this->pdfHelper->sendEmailToAdminPDFissue($productSku = $pdfSaveFilename, $this->incrementId, $errorMessage);
        }

        return $status;
    }
    /*method for merging all pdf files*/
    public function mergePdf($mergedFileName){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-mergePdf.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $status = '';
        $orginalMergedPdfFileName  = $this->orderId.".pdf";
        $virtualPdfFiles = '';
        $bundePdffiles = '';
        try{
            if($this->bundleProductfilesListArray['pdf'] || $this->virtualProductfilesListArray['pdf']) {
                $pdfMerged = new \Zend_Pdf();
                $clonedObj  = new \Zend_Pdf_Resource_Extractor();
                if ($this->virtualProductfilesListArray['pdf']){
                    $virtualPdfFiles = implode(" ",$this->virtualProductfilesListArray['pdf']);
                }

                if($this->bundleProductfilesListArray['pdf']){
                    $bundePdffiles = implode(" ",$this->bundleProductfilesListArray['pdf']);
                }
                //unset($clonedPage);
                //$pdfMerged->save($this->mergedOrginalPdfFilesPath.'/'.$orginalMergedPdfFileName);
                $mergedFilesString = $virtualPdfFiles.' '.$bundePdffiles;
                $pdfMergedCommand = "gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite  -dPDFSETTINGS=/ebook -dAutoRotatePages=/None -sOutputFile=".$this->mergedPdfFilesPath.'/'.$mergedFileName."  ".$mergedFilesString;
                $logger->info($pdfMergedCommand);
                exec($pdfMergedCommand);
                $status = TRUE;
            }
        }catch (\Exception $e){
            $logger->info("Error at Pdf Merger:- ".$mergedFileName.' Order is '.$this->orderId."Error Messsage ".$e->getMessage());
            $this->createLog("Order-Id $this->orderId- mergePdf => ".$e->getMessage());
            $status = FALSE;
            $errorMessage = "Order-Id $this->orderId- mergePdf => ".$e->getMessage();
            $this->pdfGenerationError[] = $e->getMessage();
            $this->pdfHelper->sendEmailToAdminPDFissue($productSku = '', $this->incrementId, $errorMessage);
        }
        return $status;
    }

    private function getCustomerId(){
        $customerId = $this->customerSession->getCustomer()->getGroupId();
        return $customerId;
    }

    public function createOnlyPdf($productSku){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-onlyPdf-doc-create.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        try {
            $docsSaveFilename = $this->orderId.'-'.$productSku . ".docx";
            $pdfFilename = $this->orderId.'-'.$productSku . ".pdf";
            $docFilePath = $this->productDocsFilePath[$productSku]; //exit;

            $productRepository = $this->productRepository;
            $productObj = $productRepository->get($productSku);

            $productAttributeJsonData = $productObj->getProductJsonFormat();
            $productAttributeData = (array)json_decode($productAttributeJsonData, TRUE);

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($docFilePath);

            if (!empty($productAttributeData)) {
                foreach ($productAttributeData as $productAttributeItem) {
                    if ($productAttributeItem['type'] == 'image') {
                        if ($productAttributeItem['source'] == 'attribute') {
                            $attributeVariableData = $productObj->getData($productAttributeItem['variable']);
                            if (!$attributeVariableData) {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            } else {
                                $width = 100;
                                $height = 100;
                                if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                    $width = $productAttributeItem['width'];
                                }
                                if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                    $height = $productAttributeItem['height'];
                                }
                                $productBaseDirPath = $this->mediaPath . 'catalog/product';
                                $productImagePath = $productBaseDirPath . $attributeVariableData;
                                if (file_exists($productImagePath)) {
                                    $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $productImagePath, 'size' => array($width, $height)));
                                } else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                }
                            }
                        } elseif ($productAttributeItem['source'] == 'customOption') {
                            if (isset($this->itemData[$productSku]['product_options']['options'])) {
                                $width = 100;
                                $height = 100;
                                if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                    $width = $productAttributeItem['width'];
                                }
                                if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                    $height = $productAttributeItem['height'];
                                }
                                $orderItemCustomOptions = $this->itemData[$productSku]['product_options']['options'];
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
                            if (isset($productAttributeItem['width']) && $productAttributeItem['width'] !='') {
                                $width = $productAttributeItem['width'];
                            }
                            if (isset($productAttributeItem['height']) && $productAttributeItem['height'] !='') {
                                $height = $productAttributeItem['height'];
                            }
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $mediaPath = $this->pdfHelper->getMediaBaseDir();
                            $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
                            $configPath = "design/header/logo_src";
                            $filename = $scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                            $imagePath = $mediaPath . 'logo/' . $filename;
                            if (file_exists($imagePath)) {
                                $templateProcessor->setImg($productAttributeItem['variable'], array('src' => $imagePath, 'size' => array($width, $height)));
                            } else {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            }
                        }
                    } elseif ($productAttributeItem['type'] == 'text') {
                        if ($productAttributeItem['source'] == 'attribute') {
                            $attributeVariableData = $productObj->getData($productAttributeItem['variable']);
                            if (!$attributeVariableData) {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            } else {
                                $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                            }
                        } elseif ($productAttributeItem['source'] == 'customOption') {
                            //$itemData is the order Data
                            if (isset($this->itemData[$productSku]['product_options']['options'])) {
                                $orderItemCustomOptions = $this->itemData[$productSku]['product_options']['options'];
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
                            $attributeVariableData = $this->itemData[$productSku][$productAttributeItem['variable']];
                            if (!$attributeVariableData) {
                                $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                            } else {
                                $templateProcessor->setValue($productAttributeItem['variable'], $attributeVariableData);
                            }
                        } elseif ($productAttributeItem['source'] == 'block') {
                            if (isset($productAttributeItem['id'])) {
                                $cmsBlockObj = $this->cmsBlock->setStoreId($this->storeId)->load($productAttributeItem['id']);
                                $cmsBlockData = $cmsBlockObj->getData();
                                if (isset($cmsBlockData['content']) && $cmsBlockData['content']) {
                                    $content = strip_tags($cmsBlockData['content']);
                                    $templateProcessor->setValue($productAttributeItem['variable'], $content);
                                } else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], '');
                                }
                            }
                        }
                    } elseif ($productAttributeItem['type'] == 'date') {
                        if ($productAttributeItem['source'] == 'other') {
                            if (!isset($productAttributeItem['duration'])) {
                                $logger->info("Date Duration not valid " . $this->orderId . '=>' . $productSku);
                            }
                            $currentDate = $this->date->gmtDate();
                            if(isset($productAttributeItem['duration'])) {
                                $expDate = '';
                                $dateData = explode('_', $productAttributeItem['duration']);
                                if ($dateData[1] == 'M') {
                                    $expDate = date('Y-m-d', strtotime($currentDate . '+ ' . $dateData[0] . " months"));
                                } elseif ($dateData[1] == 'D') {
                                    $expDate = date('Y-m-d', strtotime($currentDate . '+ ' . $dateData[0] . " days"));
                                } elseif ($dateData[1] == 'Y') {
                                    $expDate = date('Y-m-d', strtotime($currentDate . '+ ' . $dateData[0] . " years"));
                                } else {
                                    $logger->info("Something wrong about Date Duration " . $this->orderId . '=>' . $productSku);
                                }
                                if (!$expDate) {
                                    $templateProcessor->setValue($productAttributeItem['variable'], ' ');
                                } else {
                                    $templateProcessor->setValue($productAttributeItem['variable'], $expDate);
                                }
                            }else{
                                $templateProcessor->setValue($productAttributeItem['variable'], $currentDate);
                            }
                        }
                    } elseif ($productAttributeItem['type'] == 'cdate') {
                        if ($productAttributeItem['source'] == 'other') {
                            $currentDate = $this->date->gmtDate();
                            $templateProcessor->setValue($productAttributeItem['variable'], $currentDate);
                        }
                    }
                }
            }

            $templateProcessor->saveAs($this->temporaryDocFilesPath . '/' . $docsSaveFilename);
            if (file_exists($this->temporaryDocFilesPath . '/' . $docsSaveFilename)) {
                $pdfFileCreate = $this->createPdf($this->temporaryDocFilesPath, $docsSaveFilename, $this->temporaryPdfFilesPath, $pdfFilename);
                if (file_exists($this->temporaryPdfFilesPath . '/' . $pdfFilename)) {
                    array_push($this->virtualProductfilesListArray['pdf'], $this->temporaryPdfFilesPath . '/' . $pdfFilename);
                }
            } else {
                array_push($this->virtualProductfilesListArray['doc'], false);
            }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
            $this->createLog("Order-Id $this->orderId- createOnlyPdf => ".$e->getMessage());
            $status = FALSE;
            $errorMessage = "Order-Id $this->orderId- createOnlyPdf => ".$e->getMessage();
            $this->pdfGenerationError[] = $e->getMessage();
            $this->pdfHelper->sendEmailToAdminPDFissue($productSku = '', $this->incrementId, $errorMessage);
        }
    }


    public function createLog($message){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pdf-process-error.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($message);
    }

}
