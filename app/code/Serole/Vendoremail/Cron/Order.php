<?php

namespace Serole\Vendoremail\Cron;

class Order {

    protected $dbConnection;

    protected $order;

    protected $customer;

    protected $store;

    protected $invoicePdf;

    protected $pdfHelper;

    protected $transportBuilder;

    protected $inlineTranslation;

    protected $storeConfig;

    protected $convertOrder;

    protected $product;

    protected $storeManager;

    protected $directoryList;

    protected $directoryPath;

    protected $fileFactory;

    public function __construct(\Magento\Framework\App\ResourceConnection $dbConnection,
                                \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader,
                                \Magento\Framework\ObjectManagerInterface $objectManager,
                                \Magento\Customer\Model\Customer $customer,
                                \Magento\Catalog\Model\Product $product,
                                \Magento\Store\Model\Store $store,
                                \Serole\Pdf\Helper\Pdf $pdfHelper,
                                \Serole\PdfInvoice\Model\Invoice $invoicePdf,
                                \Serole\Pdf\Model\Mail\TransportBuilder $transportBuilder,
                                \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
                                \Magento\Framework\App\Config\ScopeConfigInterface $storeConfig,
                                \Magento\Sales\Model\Convert\Order $convertOrder,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\Filesystem\DirectoryList $directoryList,
                                \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
                                \Magento\Sales\Model\Order $order) {
        // $state->setAreaCode('frontend'); //SET CURRENT AREA
        $objectManager->configure($configLoader->load('frontend')); //SOLUTION
        $this->dbConnection = $dbConnection;
        $this->customer = $customer;
        $this->order = $order;
        $this->store = $store;
        $this->invoicePdf = $invoicePdf;
        $this->pdfHelper = $pdfHelper;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeConfig  = $storeConfig;
        $this->convertOrder = $convertOrder;
        $this->product = $product;
        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->fileFactory = $fileFactory;
        $this->directoryPath;
    }

    public function execute() {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/vendoremail-cronjob-execute.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try {
            $this->directoryPath = '/var/www/html/neatideafiles/vendoremail/';
            $connection = $this->dbConnection->getConnection();
            $readresult = $connection->query("SELECT distinct order_id from vendor_email_status WHERE status like '%pending%'");
            $i=1;
            $proArr = array();

            while ($row1 = $readresult->fetch() ) {
                $orderId = $row1['order_id'];
                $orderrec = $this->order->loadByIncrementId($orderId);

                if ($orderrec->getStoreId() !=14){
                    $this->_processVendorEmail($orderrec,$connection);
                    continue;
                }

                if ($orderrec->getBillingAddress() != '') {

                    $customerName = $orderrec->getBillingAddress()->getName();
                    $customerEmailAddress = $orderrec->getCustomerEmail();
                    $ship_data = $orderrec->getData();

                    $foomanSql = "SELECT * from fooman_totals_order WHERE order_id = " . $ship_data['entity_id'];
                    $foomanResults = $connection->fetchRow($foomanSql);

                    $orderStatus = $ship_data['status'];
                    $subTotal = number_format($ship_data['subtotal_incl_tax'], 2);
                    $shipping = number_format($ship_data['shipping_incl_tax'], 2);
                    $tax = number_format($ship_data['tax_amount'], 2);
                    $surcharge = number_format($foomanResults['amount'], 2);
                    $grandTot = number_format($ship_data['grand_total'], 2);
                    $cusId = $ship_data['customer_id'];

                    $customerData = $this->customer->load($cusId)->getData();
                    $website_id = $customerData['website_id'];

                    $website = $this->store->load($website_id);
                    $orderWebsiteName = $website->getName();

                    $sqlva = $connection->query("SELECT entity_id,state FROM sales_invoice_grid where order_increment_id= '" . $orderId . "'");
                    $resva = $sqlva->fetchAll();

                    $invoice = '';
                    $orderStoreId = $orderrec->getData('store_id');

                    if (count($resva) > 0) {
                        if ($resva[0]['state'] == 1 || $resva[0]['state'] == 3) {
                            $invoice = 0;
                        }
                        if ($resva[0]['state'] == 2) {
                            $invoice = 1;
                        }
                    } else {
                        $invoice = 0;
                    }

                    if ($invoice == 1 && ($orderStoreId == 21 || $orderStoreId == 14)) {
                        $logger->info($orderrec->getId() . '------' . $orderrec->getIncrementId());
                        $items = $orderrec->getAllItems();
                        $totItems = count($items);
                        $j = 0;
                        $orderMsg = '';
                        $itemsDetail = '';
                        $kk = 0;
                        $textMsg = '';
                        $textMsgT = '';
                        $textMsgR = '';
                        $textMsgM = '';
                        $textMsgD = '';
                        $textMsgO = '';
                        $orderItemArr = array();
                        $orderEmailArr = array();
                        $itemsDetailR = array();
                        $itemsDetailM = array();
                        $itemsDetailD = array();
                        if ($orderStoreId == 21 || $orderStoreId == 14) {
                            // Get the invoice pdf

                            $invoices = $orderrec->getInvoiceCollection();
                            $invoicePdfs = [];
                            foreach ($invoices as $key => $invoice) {
                                $invoicePdfs[] = $this->invoicePdf->getPdf(array($invoice));
                            }
                        }

                        foreach ($items as $itemId => $item) {
                            $proId = $item->getProductId();
                            $_product = $this->product->setStoreId($orderStoreId)->load($proId);
                            $productVendorEmailEnable = $_product->getData('enable_vendor_email');
                            $productVendorEmail = $_product->getData('vendor_email_address');
                            if ($kk % 2 == 0) {
                                $bgColor = ' bgcolor="#F6F6F6"';
                            } else {
                                $bgColor = '';
                            }
                            $productQty = number_format($item->getData('qty_invoiced'));
                            if ($productVendorEmailEnable == 1 && $productVendorEmail != '') {
                                $j++;
                                $productVendorEmailArray = explode(" ", $productVendorEmail);
                                foreach ($productVendorEmailArray as $productVendorEmailItem) {
                                    if (!in_array($productVendorEmail, $orderEmailArr)) {
                                        array_push($orderEmailArr, $productVendorEmailItem);
                                    }
                                }
                            }
                            if ($productVendorEmailEnable == 1 && $productVendorEmail != '' || $proId == 480 || $proId == 481 || $proId == 503) {
                                $orderItemArr[$productVendorEmail][] = '<tbody ' . $bgColor . '>
                                <tr>
                                    <td valign="top" align="left" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc">
                                        <strong style="font-size:11px">' . $item->getName() . '</strong>
                                    </td>
                                    <td valign="top" align="left" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc">' . $item->getSku() . '</td>
                                    <td valign="top" align="center" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc">' . $productQty . '</td>
                                    <td valign="top" align="right" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc">
                                        <span>$' . number_format($item->getData('price_incl_tax'), 2) . '</span>
                                    </td>
                                </tr>
                            </tbody>';
                            }
                            $kk++;
                        }

                        $rootDirectory = $this->pdfHelper->getRootBaseDir();
                        $filesDirectory = $rootDirectory . '/neatideafiles/vendoremail';

                        if (!file_exists($filesDirectory)) {
                            mkdir($filesDirectory, 0777, true);
                            chmod($filesDirectory, 0777);
                        }
                        if (!is_writable($filesDirectory)) {
                            chmod($filesDirectory, 0777);
                        }

                        //$mediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

                        if ($totItems == $j && $j > 0) {
                            $neatideasabn = 'Tax invoice issued by The Royal Automobile Club of WA (inc), ABN 33 212 133 120, 832 Wellington Street, West Perth, WA 6005. Tel: 13 17 03';
                            //$neatideasabn = '';
                        } else if ($totItems != $j && $j > 0) {
                            $neatideasabn = 'Tax invoice issued by The Royal Automobile Club of WA (inc), ABN 33 212 133 120, 832 Wellington Street, West Perth, WA 6005. Tel: 13 17 03';
                            //$neatideasabn = '';
                        } else {
                            $neatideasabn = 'Tax invoice issued by Neat Tickets Pty Ltd. ABN 12 153 820 887';
                        }
                        $headerImg0 = $filesDirectory . 'neatideas-and-rac.png';
                        $headerImg = '<td style="text-align:right;"><img src="' . $headerImg0 . '" style="height: auto;float:right;" border="0"></td>';
                        /*if($j >0) {
                            $headerImg0 = $filesDirectory.'/rac-email-footer.png';
                            $headerImg = '<td bgcolor="#fad93b" align="center" style="text-align:center;"><img src="'.$headerImg0.'" style="height: auto;width: 649px;" border="0"></td>';

                            $neatideasabn = '';
                        } else {
                            $headerImg0 = $filesDirectory.'/neatideas-and-rac.png';
                            $headerImg = '<td style="text-align:left;"><img src="'.$headerImg0.'" style="height: auto;float:left;" border="0"></td>';
                            $neatideasabn = 'Tax invoice issued by Neat Tickets Pty Ltd. ABN 12 153 820 887';
                        }*/
                        $salesemailorderitems0 = '<table width="650" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #eaeaea">
                            <thead>
                                <tr>
                                    <th bgcolor="#EAEAEA" align="left" style="font-size:13px;padding:3px 9px">Item</th>
                                    <th bgcolor="#EAEAEA" align="left" style="font-size:13px;padding:3px 9px">Sku</th>
                                    <th bgcolor="#EAEAEA" align="center" style="font-size:13px;padding:3px 9px">Qty</th>
                                    <th bgcolor="#EAEAEA" align="right" style="font-size:13px;padding:3px 9px">Subtotal</th>
                                </tr>
                            </thead>';
                        /*$salesemailorderitems1 = '<tbody>
                                    <tr>
                                        <td align="right" style="padding:3px 9px" colspan="3">Subtotal (incl GST)</td>
                                        <td align="right" style="padding:3px 9px"><span>$'.$subTotal.'</span></td>
                                    </tr>
                                    <tr>
                                        <td align="right" style="padding:3px 9px" colspan="3">Shipping &amp; Handling</td>
                                        <td align="right" style="padding:3px 9px"><span>$'.$shipping.'</span></td>
                                    </tr>
                                    <tr>
                                        <td align="right" style="padding:3px 9px" colspan="3">GST</td>
                                        <td align="right" style="padding:3px 9px"><span>$'.$tax.'</span></td>
                                    </tr>
                                    <tr>
                                        <td align="right" style="padding:3px 9px" colspan="3">Credit Card Processing</td>
                                        <td align="right" style="padding:3px 9px"><span>$'.$surcharge.'</span></td>
                                    </tr>

                                    <tr>
                                        <td align="right" style="padding:3px 9px" colspan="3"><strong>Grand Total</strong></td>
                                        <td align="right" style="padding:3px 9px"><strong><span>$'.$grandTot.'</span></strong></td>
                                    </tr>
                                </tbody>
                        </table>';*/
                        $salesemailorderitems1 = '<tbody>
                                <tr>
                                    <td align="right" style="font-size:13px; padding:3px 9px" colspan="3"><b>Subtotal </b><span style="font-size:12px;">(inc. GST)</span></td>
                                    <td align="right" style="padding:3px 9px"><span>$' . $subTotal . '</span></td>
                                </tr>
                                <tr>
                                    <td align="right" style="font-size:13px; padding:3px 9px" colspan="3"><b>Shipping & Handling</b><span style="font-size:12px;">(inc. GST)</span></td>
                                    <td align="right" style="padding:3px 9px"><span>$' . $shipping . '</span></td>
                                </tr>
                                <tr>
                                    <td align="right" style="font-size:13px; padding:3px 9px" colspan="3"><b>Credit Card Processing </b><span style="font-size:12px;">(inc. GST)</span></td>
                                    <td align="right" style="padding:3px 9px"><span>$' . $surcharge . '</span></td>
                                </tr>
                                                                
                                <tr>
                                    <td align="right" style="font-size:13px; padding:3px 9px" colspan="3"><strong>Grand Total</strong> <span style="font-size:12px;">(inc. GST)</span></td>
                                    <td align="right" style="padding:3px 9px"><strong><span>$' . $grandTot . '</span></strong></td>
                                </tr>
                                <tr><td colspan="5">&nbsp;</td></tr>
                                <tr>
                                    <td align="right" style="font-size:12px; padding:3px 9px" colspan="5">
                                    <i>Grand Total includes GST of <strong><span style="font-size:13px;">$' . $tax . '</span></strong></i>
                                    </td>
                                </tr>
                            </tbody>
                    </table>';
                        $salesemailorderitems = '';
                        $orderVenEmail = '';
                        for ($s = 0; $s < count($orderEmailArr); $s++) {
                            $orderVenEmail = $orderEmailArr[$s];
                            $itemsList = '';
                            for ($p = 0; $p < count($orderItemArr[$orderVenEmail]); $p++) {
                                $itemsList .= $orderItemArr[$orderVenEmail][$p];
                            }
                            $salesemailorderitems = $salesemailorderitems0 . $itemsList . $salesemailorderitems1;
                            $orderVenEmail = "iamramesh.a@gmail.com";

                            #Ramesh Commented  to remove the comment once went live
                            if ($orderVenEmail == 'iamramesh.a@gmail.com') {
                                $textMsgT = 'TouringPerth@gmail.com for Maps and Navigator<br>';
                            } else if ($orderVenEmail == 'iamramesh.a@gmail.com') {
                                $textMsgT = 'batteryservice@gmail.com for Batteries (Motorbike)<br>';
                            } else if ($orderVenEmail == 'iamramesh.a@gmail.com') {
                                $textMsgT = 'Member.Benefits@gmail.com for Car History<br>';
                            } else if ($orderVenEmail == 'iamramesh.a@gmail.com') {
                                $textMsgT = 'iamramesh.a@gmail.com for Driving Experiences<br>';
                            }

                            $memberemails = 'If you have any questions about your order or shipment, email: <br>' . $textMsgT;
                            $orderMsg .= 'Vendor email sent to ' . $orderVenEmail . "<br>";

                            $storeEmail = $this->storeConfig->getValue('trans_email/ident_sales/email',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                            );

                            $storeName = $this->storeConfig->getValue('trans_email/ident_sales/name',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                            );


                            $this->inlineTranslation->suspend();

                            if ($orderStoreId == 21 || $orderStoreId == 14) {
                                $emailTemplatestore = $this->transportBuilder->load(24);
                                $emailvarsstore = array(
                                    'order' => $orderrec,
                                    'customername' => $customerName,
                                    'storeName' => $orderWebsiteName,
                                    'orderdate' => $orderrec->getCreatedAtFormated('small'),
                                    'orderid' => $orderrec->getData('increment_id'),
                                    'ordercustomeremail' => $customerEmailAddress,
                                    'salesemailorderitems' => $salesemailorderitems,
                                    'memberemails' => $memberemails,
                                    'racneatlogo' => $headerImg,
                                    'neatideasabn' => $neatideasabn
                                );
                            } else {
                                $emailTemplatestore = $this->transportBuilder->load(15);
                                $emailvarsstore = array(
                                    'order' => $orderrec,
                                    'customername' => $customerName,
                                    'storeName' => $orderWebsiteName,
                                    'orderdate' => $orderrec->getCreatedAtFormated('small'),
                                    'orderid' => $orderrec->getData('increment_id'),
                                    'ordercustomeremail' => $customerEmailAddress,
                                    'salesemailorderitems' => $salesemailorderitems,
                                    'memberemails' => $memberemails
                                );
                            }

                            //$emailTemplatestore->mplateSubject($orderWebsiteName.' New Order - # '.$orderrec->getData('increment_id'));
                            $emailTemplatestore->setTemplateOptions([
                                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                    'store' => $this->storeManager->getStore()->getId(),
                                ]
                            );
                            $emailTemplatestore->setTemplateVars($emailvarsstore);
                            $emailTemplatestore->setFrom([
                                'name' => $storeName,
                                'email' => $storeEmail,
                            ]);
                            $emailTemplatestore->addTo('iamramesh.a@gmail.com', 'iamramesh.a@gmail.com');

                            if ($orderStoreId == 21 || $orderStoreId == 14) {
                                for ($pdfIdx=0; $pdfIdx < count($invoicePdfs); $pdfIdx++){
                                    if ($pdfIdx > 0){
                                        $pdfFilename='Order_'.$orderId.'_Invoice_'.$pdfIdx.'.pdf';
                                    } else {
                                        $pdfFilename='Order_'.$orderId.'_Invoice.pdf';
                                    }

                                    $pdfContent = $invoicePdfs[$pdfIdx]->render();
                                    $emailTemplatestore->addAttachment($pdfContent,$pdfFilename,$fileType='application/pdf'); //Attachment goes here.
                                }

                               /* $logger->info("Before Create Pdf ---".$orderId);
                                $pdf = $this->invoicePdf->getPdf($invoicePdfs);
                                $pdfFilename = 'Order_'.$orderId.'_Invoice_.pdf';
                                $filePath = $this->directoryPath.'/'.$pdfFilename;
                                $logger->info($filePath);

                                $this->fileFactory->create($pdfFilename,$pdf->render(),\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,'application/pdf');
                                if(file_exists($filePath)){
                                    $emailTemplatestore->addAttachment(file_get_contents($filePath),$pdfFilename,$fileType='application/pdf'); //Attachment goes here.
                                }*/
                            }

                            $logger->info('  Sending Vendor Email for '.$orderId.' - '.$pdfFilename.' to '.$orderVenEmail);

                            $transport = $this->_transportBuilder->getTransport();
                            $transport->sendMessage();
                            $this->inlineTranslation->resume();

                            $orderrec->addStatusToHistory($orderStatus, $orderMsg, false);
                            $orderrec->save();
                            $readresultas=$connection->query("update vendor_email_status set updated_at = now(),status = 'sent' WHERE order_id = '".$orderId."'");

                        }
                        if (count($items) == $j && $orderrec->getStatus != 'complete' && $orderrec->canShip()) {
                            $itemQty = $orderrec->getItemsCollection()->count();

                            $convertOrder = $this->convertOrder;
                            $shipment = $convertOrder->toShipment($orderrec);
                            foreach ($orderrec->getAllItems() AS $orderItem) {
                                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                                    continue;
                                }
                                $qtyShipped = $orderItem->getQtyToShip();
                                $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                                $shipment->addItem($shipmentItem);
                            }
                            $shipment->register();
                            $shipment->getOrder()->setIsInProcess(true);
                            $shipment->save();
                            $shipment->getOrder()->save();

                            $orderrec->addStatusToHistory('complete', 'complete', false);
                            $orderrec->save();
                        }
                    }
                    $i++;
                }
            }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }
    }


    function _processVendorEmail($order,$connection){

     try {
        $connection = $this->dbConnection->getConnection();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/vendoremail-cron-order-processVendorEmail.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $orderId =  $order->getIncrementId();
        $storeId = $order->getStoreId();
        $items = $order->getAllItems();

        if ($order->getBaseTotalDue() > 0 ) {
            $logger->info("Order ".$orderId.' - has outstanding amount');
            return;
        }

        $vendorEmails = array();
        $itemsProcessed = 0;
        $productDeliveryTypeId='8';
        $productDeliveryType=array();

        foreach ($items as $itemId => $item) {
            $product = $this->product->setStoreId($storeId)->load($item->getProductId());
            //$productDeliveryType[]=$product->getDelivery();
            $emailEnabled = $product->getData('enable_vendor_email');
            if ($emailEnabled == '1') {
                $vendorEmail = $product->getData('vendor_email_address');
                $vendorEmailArray = explode(" ",$vendorEmail);
                foreach ($vendorEmailArray as $vendorEmailArrayItem) {
                    if (!in_array($vendorEmailArrayItem, $vendorEmails)){
                        $vendorEmails[] = $vendorEmailArrayItem;
                        //if (!in_array($productDeliveryTypeId, $productDeliveryType)){
                        $itemsProcessed++;
                        $logger->info("Order ".$orderId.' - send email to: ');
                        $logger->info($vendorEmailArrayItem);
                        //}

                    }
                }
            }
        }



        if (count($vendorEmails) > 0){
            // Create the email
            $storeEmail = $this->storeConfig->getValue('trans_email/ident_sales/email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $storeName = $this->storeConfig->getValue('trans_email/ident_sales/name',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $this->inlineTranslation->suspend();
            $emailTemplatestore  = $this->transportBuilder->setTemplateIdentifier(48);

            $emailVars = array(
                'order' => $order,
                'store' => $order->getStore()
            );
            $emailTemplatestore->setTemplateOptions(  [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            );
            $emailTemplatestore->setTemplateVars($emailVars);
            $emailTemplatestore->setFrom([
                'name' => $storeName,
                'email' => $storeEmail,
            ]);
            $emailTemplatestore->addTo('iamramesh.a@gmail.com','iamramesh.a@gmail.com');

            $invoices = $order->getInvoiceCollection();

            /*
                $pdf = $this->invoicePdf->getPdf($invoices);
                $pdfFilename = 'Order_'.$orderId.'_Invoice_.pdf';
                $filePath = $this->directoryPath.'/'.$pdfFilename;

                $this->fileFactory->create($pdfFilename,$pdf->render(),\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,'application/pdf');
                if(file_exists($filePath)){
                    $emailTemplatestore->addAttachment(file_get_contents($filePath),$pdfFilename,$fileType='application/pdf'); //Attachment goes here.
                }
            */

            $idx = 0;
            foreach ($invoices as $invoice) {
                 $idx++;
                 $pdfFilename = 'Order_'.$orderId.'_Invoice_'.$idx.'.pdf';
                 $pdf = $this->invoicePdf->getVendorPdf($invoice);
                 $pdfContent = $pdf->render();
                 $pdfFilePath = $this->directoryPath.$pdfFilename;
                 $emailTemplatestore->addAttachment($pdfContent,$pdfFilename,$fileType='application/pdf');
             }

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            $logger->info("Order ".$orderId.' - emails sent');


           foreach($vendorEmails as $vendorEmail){
                $sql = "update vendor_email_status set updated_at = now(),status = 'sent' WHERE order_id = '".$orderId."' ";
                $logger->info($sql);
                $connection->query($sql);
                // Add a comment to the order
                $msg = 'Vendor email sent to '.$vendorEmail."<br>";
                $order->addStatusToHistory($order->getStatus(), $msg, false);
                $order->save();
            }
        }

        $logger->info("Order ".$orderId.' - items processed = '.$itemsProcessed);

        // If the number of items processed matches the number of items on the order
        // then go ahead an complete the shipment on the order
        // So the order is set to "complete"
        // It seems random - but this is needed for Magento to allow Simple products to be completed
            if ($itemsProcessed > 0){
                if($itemsProcessed == count($items)
                    && $order->getStatus()!='complete'
                    && $order->canShip()) {
                    $logger->info("Order ".$orderId.' - creating shipment and completing order');

                        $convertOrder = $this->convertOrder;
                        $shipment = $convertOrder->toShipment($order);
                        foreach ($order->getAllItems() AS $orderItem) {
                            if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                                continue;
                            }
                            $qtyShipped = $orderItem->getQtyToShip();
                            $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                            $shipment->addItem($shipmentItem);
                        }
                        $shipment->register();
                        $shipment->getOrder()->setIsInProcess(true);
                        $shipment->save();
                        $shipment->getOrder()->save();
                        $order->addStatusToHistory('complete', 'Order Auto-Completed following vendor email', false);

                    $order->save();
                }

            }

        } catch (Exception $e) {
            $order->addStatusHistoryComment(' Internal error when trying to auto-ship order. Message: '.$e->getMessage(), false);
            $logger->info($e->getMessage());
        }

    }
}