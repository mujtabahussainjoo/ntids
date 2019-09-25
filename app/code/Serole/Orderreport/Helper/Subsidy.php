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
// exit(BP.'/lib/reports/ReportUtils/Report.class.php');
class Subsidy extends \Magento\Framework\App\Helper\AbstractHelper
{

    private $order;

    private $pdfHelper;

    private $store;

    private $storeConfig;

    private $orderReportHelper;

    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Magento\Sales\Model\Order $order,
                                \Magento\Catalog\Model\Product $product,
                                \Magento\Catalog\Model\Category $category,
                                \Serole\Orderreport\Helper\Data $orderReportHelper,
                                \Serole\Pdf\Helper\Pdf $pdfHelper,
                                \Magento\Framework\App\Config\ScopeConfigInterface $storeConfig,
                                \Magento\Store\Model\Store $store
    )
    {
        $this->order = $order;
        $this->product = $product;
        $this->category = $category;
        $this->store = $store;
        $this->storeConfig = $storeConfig;
        $this->pdfHelper = $pdfHelper;
        $this->orderReportHelper = $orderReportHelper;
        parent::__construct($context);

    }

    public function exportOrders($post) {

        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-website-order-report-subsidyhelper-exportOrders.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $pdf = new \Zend_Pdf();
            $fontBold = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
            $page = new \Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

            $storeObj = $this->store->load($post['store_id']);
            $websiteId = $storeObj->getWebsiteId();
            $websiteCode = $storeObj->getCode();
            $websiteName = $storeObj->getName();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $configDataSqlQuery = $connection->query("select * from core_config_data where path LIKE '%general/store_information/businessaddress%' and scope_id = '" . $websiteId . "'");
            $configData = $configDataSqlQuery->fetch();
			$businessAdd = $configData['value'];
            $storeCode = $post['store_id'];
			$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();    
			$storeManager = $objectManager->create("\Magento\Store\Model\StoreManagerInterface");
			$storecode = $storeCode;
			$stores = $storeManager->getStores(true, false);
				foreach($stores as $store){
					if($store->getCode() ==$storecode){
						$store_Id = $store->getId();
					}
			    }
			$storeId=$store_Id;
			$logger->info($storeId);
            $period = explode('-', $_POST['period']);
            $dates = $this->getStartAndEndDate($period[0], $period[1]);
            $qry_from_date = $dates[0];
            $qry_to_date = $dates[1];
            $display_from_date = $dates[2];
            $display_to_date = $dates[3];
            // Sadly we had to adjust the week number to be 1 more than the ACTUAL week number
            // because we accidentally sent an invoice ahead of when it was supposed to be sent.
            // The following code should be removed at the start of 2015

            $year = $period[0];
            $week = $period[1];
            if ($year == 2014) {
                $invoiceNo = strtoupper($websiteCode) . $year . ($week + 1);
            } else {
                $invoiceNo = strtoupper($websiteCode) . str_replace("-", "", $post['period']);
            }
            $invd = new \DateTime();
            $invoiceDate = $invd->format('d/m/Y');
            $invd->modify('+5 days');
            $invoiceDue = $invd->format('d/m/Y');
            $report = new \Report();
            $report->setTitle('Subsidy Invoice')
                ->setStore('')
                ->addReportHeader('Website:', $websiteName)
                ->addReportHeader('Period:', $display_from_date . " To " . $display_to_date)
                ->addReportHeader('Invoice:', $invoiceNo)
                ->addReportHeader('Date:', $invoiceDate)
                ->addReportHeader('Due Date:', $invoiceDue)
                ->startReport();

            // if ($businessAdd != '') {
                // $businessAddArr = explode("\n", $businessAdd);
                // $yy = '';
                // for ($i = 0; $i < count($businessAddArr); $i++) {
                    // $yy -= 10;
                    // $page->setFont($fontBold, 7)->drawText(trim($businessAddArr[$i]), 30, $yy);
                // }
            // }
            $this->writeAllSubsidyRecords($storeId, $invoiceNo, $qry_from_date, $qry_to_date);
			$logger->info("writeAllSubsidyRecords");	
            $totalQty = 0;
            $totalPrice = 0;
            $totalSubsidy = 0;
            $catNames = $this->getCategoryNames($invoiceNo);
            asort($catNames);
            $report->addColumn("Product", 265, $report::ALIGN_RIGHT)
                ->addColumn("Price ($)", 44, $report::ALIGN_RIGHT)
                ->addColumn("Subsidy ($)", 50, $report::ALIGN_RIGHT)
                ->addColumn("Qty", 20, $report::ALIGN_RIGHT)
                ->addColumn("Total Price ($)", 60, $report::ALIGN_RIGHT)
                ->addColumn("Total Subsidy ($)", 70, $report::ALIGN_RIGHT)
                ->drawTableHeaders();
            foreach ($catNames as $catName) {
                $subtotalQty = 0;
                $subtotalPrice = 0;
                $subtotalSubsidy = 0;
                //$report->drawRow([$catName], true);
                $binds = array('invoiceNo' => $invoiceNo,'catName' => $catName);
				$logger->info($binds);
				$recs = $connection->query("SELECT * FROM subsidy_data WHERE invoice_no = :invoiceNo AND category = :catName", $binds);
				while ($rec = $recs->fetch()) {
                    $priceAmount = $rec['price'] * $rec['qty'];
                    $subsidyAmount = $rec['subsidy'] * $rec['qty'];

                    $prodName = $rec['product'];
                    if (strlen($prodName) > 70) {
                        $prodName = substr($prodName, 0, 70) . '...';
                    }

                    $row = [
                        $prodName,
                        number_format($rec['price'], 2),
                        number_format($rec['subsidy'], 2),
                        $rec['qty'],
                        number_format($priceAmount, 2),
                        number_format($subsidyAmount, 2)
                    ];

                    $report->drawRow($row, false);
                    $subtotalQty += $rec['qty'];
                    $subtotalPrice += $priceAmount;
                    $subtotalSubsidy += $subsidyAmount;
                }
                $row = ['', '', '', number_format($subtotalQty), number_format($subtotalPrice, 2), number_format($subtotalSubsidy, 2)];
                $report->drawSubtotals($row);

                $totalQty += $subtotalQty;
                $totalSubsidy += $subtotalSubsidy;
                $totalPrice += $subtotalPrice;
            }
            $data =["BSB:" => "066 209",
					"Account:" => "10043293",
					"Bank:" => "Commonwealth Bank, Floreat",
					"" => "",
					"Neat Tickets Pty Ltd" => "",
					"PO BOX 7277" => "",
					"Shenton Park" => "",
					"WA 6008" => " ",
					"ABN: 30 146 887 3908" => ""
            ];    
            $report->drawPaymentInfoTable('PAYMENT DETAILS', $data, 33, 75);
            if ($totalSubsidy < 0) {
                $data = ["Total Sales Value:" => '$' . number_format($totalPrice, 2),
                    "Sales Qty:" => $totalQty,
                    "REMITTANCE:" => '$' . number_format($totalSubsidy * -1, 2)
                ];
                $report->setTitle('Remittance Advice');
            } else {
                $data = ["Total Sales Value:" => '$' . number_format($totalPrice, 2),
                    "Sales Qty:" => $totalQty,
                    "PLEASE PAY:" => '$' . number_format($totalSubsidy, 2)
                ];

            }
            $report->drawTotalTable($data, 505, $report->marginRight - $report->cellPad, $report::ALIGN_RIGHT);
            $fileName = $invoiceNo . '.pdf';
            $mediaPath = $this->pdfHelper->getMediaBasePath();
            $filePath = $mediaPath.'reports/';

            if (!file_exists($filePath)) {
                mkdir($filePath, 0777, true);
                chmod($filePath, 0777);
            }
            if (!is_writable($filePath)) {
                chmod($filePath, 0777);
            }
            $file = $filePath.$fileName;
            $report->setFilename($file);
            $report->saveReport();
            if (file_exists($file)) {
                $logger->info("testPhpError");
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                ob_clean();
                flush();
                readfile($file);
                $this->orderReportHelper->sendMail($type='pdf',$contents=false,$fileName='Subsidy_Invoice.pdf',$post);
                exit;
            }
        } catch (\Exception $e){
            $logger->info($e->getMessage());
        }
    }

    private function getStartAndEndDate($year,$week) {
        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-website-order-report-subsidyhelper-getStartAndEndDate.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $dto = new \DateTime();
            $dto->setISODate($year, $week);

            $ret[0] = $dto->format('Y-m-d') . ' 00:00:00';
            $ret[2] = $dto->format('d/m/Y');

            $dto->modify('+6 days');
            $ret[1] = $dto->format('Y-m-d') . ' 23:59:59';
            $ret[3] = $dto->format('d/m/Y');
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }

        return $ret;
    }


    protected function writeAllSubsidyRecords($storeId, $invoiceNo,$qry_from_date,$qry_to_date){

        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-website-order-report-subsidyhelper-writeAllSubsidyRecords.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $this->clearSubsidyData($invoiceNo);
            $categoryNames = array();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $orders = $connection->query("SELECT entity_id, customer_group_id, created_at FROM sales_order WHERE updated_at >= '" . $qry_from_date . "' AND updated_at <= '" . $qry_to_date . "' AND status = 'complete' AND store_id = ". $storeId);
           	while ($order = $orders->fetch()) {
                // Is the customer a VIP?
                $groupId = $order['customer_group_id'];
                $isVIP = ($groupId == 4);
                $items = $connection->query("SELECT product_id, name, price_incl_tax, qty_ordered FROM sales_order_item WHERE order_id = " . $order['entity_id'] );
				while ($item = $items->fetch()) {
                    $_newProduct = $this->product->load($item['product_id'], array('subsidy'));
                    $subsidy = $_newProduct->getSubsidy();
					//$this->_getSubsidyAtDate($storeId, $_newProduct->getSku(), $order['created_at'], $isVIP);
                    if ($subsidy > 0 || $subsidy < 0) {
                        $catIds = $_newProduct->getCategoryIds();
                        $catId = $catIds[count($catIds) - 1];
                        if (!isset($categoryNames[$catId])) {
                            $_cat = $this->category->load($catId);
                            $categoryNames[$catId] = $_cat->getName();
                        }
                        $catName = $categoryNames[$catId];
                        if ($isVIP) {
                            $catName .= ' (VIP)';
                        }
                        $prodName = $item['name'];
                        $this->updateSubsidyData($storeId, $invoiceNo, $catName, $prodName, $subsidy, $item['price_incl_tax'], (int)$item['qty_ordered']);
					}
                }
            }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }
    }


    protected function getCategoryNames($invoiceNo){
        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-website-order-report-subsidyhelper-getCategoryNames.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $sqlQuery = $connection->query("SELECT distinct(category) as category
								FROM subsidy_data
								WHERE invoice_no = '" . $invoiceNo . "'");
             $catNames = array();
            while ($rec = $sqlQuery->fetch()) {
                $catNames[] = $rec['category'];
            }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }
        return $catNames;
    }

    private function _getSubsidyAtDate($storeId, $sku, $dateToFind, $isVIP) {
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-website-order-report-subsidyhelper-getSubsidyAtDate.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
        $qry = "SELECT subsidy,vip_subsidy FROM price_history WHERE store_id = " . $storeId . " AND sku = '" . $sku . "' AND created_at <= '" . $dateToFind . "' ORDER BY created_at DESC LIMIT 1";
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $res = $connection->query($qry);
        $rec = $res->fetch();
        if (!$rec) {
            $qry = "SELECT subsidy,vip_subsidy FROM price_history WHERE store_id = 0 AND sku = '" . $sku . "' AND created_at <= '" . $dateToFind . "' ORDER BY created_at DESC LIMIT 1";
            $res = $connection->query($qry);
            $rec = $res->fetch();
        }
    }


    protected function clearSubsidyData($invoiceNo){
        /*$conn 		= Mage::getSingleton('core/resource')->getConnection('core_write');*/
        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-website-order-report-subsidyhelper-clearSubsidyData.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $res = $connection->query("DELETE FROM subsidy_data 
									WHERE invoice_no= '" . $invoiceNo . "'");
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }

    }

    protected function updateSubsidyData($storeId, $invoiceNo, $catName, $prodName, $subsidy, $price, $qty){

        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-website-order-report-subsidyhelper-updateSubsidyData.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $binds = array('invoiceNo' => $invoiceNo,'catName' => $catName,'prodName' => $prodName, );
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $res = $connection->query("SELECT qty FROM subsidy_data WHERE invoice_no = :invoiceNo AND category = :catName AND product = :prodName", $binds);
            $rec = $res->fetch();
			$logger->info("End Query");
            if ($rec) {
				$logger->info("Data Exist");
                $binds['qty'] = $qty;
                $qry = "UPDATE subsidy_data SET qty = qty + :qty WHERE invoice_no = :invoiceNo AND category= :catName AND product= :prodName";
            } else {
				$logger->info("Data Not Exist");
                $binds['qty'] = $qty;
                $binds['subsidy'] = number_format($subsidy, 2);
                $binds['price'] = number_format($price, 2);
                $binds['storeId'] = $storeId;
                $qry = "INSERT INTO subsidy_data (invoice_no, category, product, subsidy, price, qty, store_id)VALUES ( :invoiceNo, :catName, :prodName, :subsidy, :price, :qty, :storeId)";
				$logger->info($qry);
			}
            $connection->query($qry, $binds);
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }

    }

}


/* $mail = new Zend_Mail();
                $pdf = $pdf->render();
                $attachment = $mail->createAttachment($pdf);
                $attachment->type = 'application/pdf';
                $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                $attachment->encoding = Zend_Mime::ENCODING_BASE64;
                $attachment->filename = 'Subsidy_Invoice.pdf';
                // set basic email data including sender, receiver, message and subject line
                $html = "Hello, " . Mage::getStoreConfig('trans_email/ident_custom1/name') . " <br /><br />Please find attached your subsidy invoice for the period " . $post['from'] . " to " . $post['to'] . ".<br />If you have any queries about this invoice, please call us on 01 1234 1234.<br />
                            Alternatively send an email to info@neatideas.com.au<br /><br />
                            Kind Regards,<br />
                            The Neat Ideas Team";

                $mail->setBodyHtml($html);
                $mail->setFrom('info@neatideas.com.au', 'Neat Ideas');
                //$mail->addTo('dhananjay.kumar@serole.com', 'The Receiver');


                //$to_email = Mage::getStoreConfig('trans_email/ident_custom1/email'); //trans_email_ident_custom1_email
                //$to_name = Mage::getStoreConfig('trans_email/ident_custom1/name'); // trans_email_ident_custom1_name

                $to_email = 'sonu.gautam@serole.com';
                $mail->addTo($to_email, $to_name);

                $mail->setSubject('Subsidy Invoice from Neat Ideas');*/
//$mail->send();
