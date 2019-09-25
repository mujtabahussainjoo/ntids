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

class Website extends \Magento\Framework\App\Helper\AbstractHelper{

   private $order;

    private $pdfHelper;

    private $store;

    private $storeConfig;

    private $orderReportHelper;

    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Magento\Sales\Model\Order $order,
                                \Magento\Catalog\Model\Product $product,
                                \Magento\Catalog\Model\Category $category,
                                \Serole\Pdf\Helper\Pdf $pdfHelper,
                                \Serole\Orderreport\Helper\Data $orderReportHelper,
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

    /*Webiste Reporting script*/
    public function exportspecificationPdfOrders($orders,$post){

        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-website-order-report-helper-exportspecificationOrders.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $record = array();
            $pdf = new \Zend_Pdf();
            $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
            $fontBold = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
            $page = new \Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

            $store = '';
            $mediaPath = $this->pdfHelper->getMediaBaseDir();
            $logoPath = $this->pdfHelper->getLogo('design/header/logo_src');
            $image = $mediaPath . 'logo/' . $logoPath;

            $image = Zend_Pdf_Image::imageWithPath($image);
            $top = 830; //top border of the page
            $widthLimit = 110; //half of the page width
            $heightLimit = 63; //assuming the image is not a "skyscraper"
            $width = $image->getPixelWidth();
            $height = $image->getPixelHeight();
            //preserving aspect ratio (proportions)
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
            $y1 = $top - $height;
            $y2 = $top;
            $x1 = 25;
            $x2 = $x1 + $width;
            $page->drawImage($image, $x1, $y1, $x2, $y2);
            $yy = 765;
            $yy -= 20;
            $page->setFont($fontBold, 7)->drawText("Web site  :", 30, $yy);
            $page->setFont($font, 7)->drawText($post['store_id'], 68, $yy);
            $yy -= 10;
            $page->setFont($fontBold, 7)->drawText("Period     :", 30, $yy);
            $page->setFont($font, 7)->drawText($post['from_date'] . " To ", 68, $yy);
            $page->setFont($font, 7)->drawText($post['to_date'], 115, $yy);

            $rr = $yy;
            $yy = $rr;

            $counttt = 0;
            $rr = $yy;
            $yy -= 2;
            $tot = 0;
            $counttt = 1;

            /*************** construct array************************/
            $productCategory = array();
			 $i = 0;
			 $totalQtyOrdered = 0;
			 $totalOrderAmount = 0;
            foreach ($orders as $order) {
                $order = $this->order->load($order->getId());
                $orderItems = $order->getItemsCollection();
               
				$bundleItem = array();
				
                foreach ($orderItems as $k => $item) {
                    $_newProduct = array();
					 $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					 $_newProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                    //$_newProduct = $this->product->load($item->getProductId());
					$cats = $_newProduct->getCategoryIds();
					if(!empty($cats)){
						foreach ($cats as $category_id) {
							$_cat = $this->category->load($category_id);
								$categoryname = $_cat->getName();

						}
					if($item->getProductType() == 'bundle')
					{
						$bundleItem['categoryname'] = $categoryname;
					}
					if(!empty($bundleItem))
						$categoryname = $bundleItem['categoryname'];
					
						$datae = explode(" ", $item->getCreatedAt());
						$productCategory[$categoryname][$i]['orderid'] = $order->getIncrementId();
						$productCategory[$categoryname][$i]['created_at'] = $datae[0];
						$productCategory[$categoryname][$i]['customername'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
						$productCategory[$categoryname][$i]['productname'] = $item->getName();
						$productCategory[$categoryname][$i]['itemprice'] = $item->getPrice();
						$productCategory[$categoryname][$i]['qty'] = (int)$item->getQtyOrdered();
						
						$totalQtyOrdered = $totalQtyOrdered + $item->getQtyOrdered();
						$totalOrderAmount = $totalOrderAmount + ($item->getPrice()* $item->getQtyOrdered());
						$i++;
					}
                }

            }

            /************** End *******************************/

            $totalSalesTotal = 0;
            $totalqtyTotal = 0;
            $TotalNo = 0;
            $yy -= 10;
			if(!empty($productCategory)){
				foreach ($productCategory as $k => $productcategoryval) {
					$yy -= 10;
					$page->setFont($font, 9)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText(strtoupper($k), 32, $yy);
					$yy -= 15;
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Order", 32, $yy);
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("#Date", 100, $yy);
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Name", 170, $yy);
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Ref #", 252, $yy);
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Product", 282, $yy);
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Price", 455, $yy);
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Qty", 500, $yy);
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Amount", 530, $yy);
					$yy -= 3;
					$page->setFillColor(new Zend_Pdf_Color_Html('#edebeb'))->setLineColor(new Zend_Pdf_Color_GrayScale(0))->setLineWidth(.4)->drawLine(30, $yy, 565, $yy);
					$totalSales = 0;
					$totalqty = 0;
					foreach ($productcategoryval as $item) {
						$TotalNo++;
						$yy -= 15;
						$page->setFont($font, 7)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($item['orderid'], 32, $yy);
						$page->setFont($font, 7)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($item['created_at'], 100, $yy);
						$page->setFont($font, 7)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($item['customername'], 170, $yy);
						$page->setFont($font, 7)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText('1234', 252, $yy);
						$page->setFont($font, 7)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($item['productname'], 285, $yy);
						$b = str_replace(',', '', number_format($item['itemprice'], 2));
						$b = str_replace('.', '', $b);
						$le = 0;
						if (strlen($b) == 5) {
							$le = 1;
						}
						$positionpri = 480 - (strlen($b) * 5) + $le;
						$page->setFont($font, 7)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText(number_format($item['itemprice'], 2), $positionpri, $yy);
						$page->setFont($font, 7)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($item['qty'], 510, $yy);
						$amount = $item['qty'] * $item['itemprice'];
						$ba = str_replace(',', '', number_format($amount, 2));
						$ba = str_replace('.', '', $ba);
						$len = 0;
						if (strlen($ba) == 5) {
							$len = 1;
						}
						$positionamount = 560 - (strlen($ba) * 5) + $len;
						$page->setFont($font, 7)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText(number_format($amount, 2), $positionamount, $yy);
						$totalSales += $amount;
						$totalqty += $item['qty'];

						if (($counttt - 45) % 60 == 0) {
							$pdf->pages[] = $page;
							$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
							$yy = 820;
						}
						$counttt++;
						$totalSalesTotal += $totalSales;
						$totalqtyTotal += $totalqty;
					}
					$yy -= 4;
					$page->setFillColor(new Zend_Pdf_Color_Html('#edebeb'))->setLineColor(new Zend_Pdf_Color_GrayScale(0))->setLineWidth(.4)->drawLine(30, $yy, 565, $yy);
					$yy -= 10;
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Total Sales", 460, $yy);
					$baT = str_replace(',', '', number_format($totalSales, 2));
					$baT = str_replace('.', '', $baT);
					$lenT = 0;
					if (strlen($baT) == 5) {
						$lenT = 1;
					}
					$positionTTamount = 555 - (strlen($baT) * 5) + $lenT;
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText('$' . number_format($totalSales, 2), $positionTTamount, $yy);
					$yy -= 15;
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Total Items", 460, $yy);
					$page->setFont($font, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($totalqty, 550, $yy);


					$yy -= 4;
					$page->setFillColor(new Zend_Pdf_Color_Html('#edebeb'))->setLineColor(new Zend_Pdf_Color_GrayScale(0))->setLineWidth(.4)->drawLine(30, $yy, 565, $yy);
					$yy -= 15;
				}
			}
            $yy -= 15;
            $page->setFont($fontBold, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Total Orders", 33, $yy);
            //$page->setFont($fontBold, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($TotalNo, 145, $yy);
            $page->setFont($fontBold, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText(count($orders), 145, $yy);
            $yy -= 15;
            $page->setFont($fontBold, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Total Items", 33, $yy);
            $page->setFont($fontBold, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($totalQtyOrdered, 145, $yy);
            //$page->setFont($fontBold, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText($totalqtyTotal, 145, $yy);

            $yy -= 15;
            $page->setFont($fontBold, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText("Total Sales", 33, $yy);

            $baTT = str_replace(',', '', number_format($totalOrderAmount, 2));
            $baTT = str_replace('.', '', $baTT);
            $lenTT = 0;
            if (strlen($baTT) == 5) {
                $lenTT = 1;
            }
            $positionTTamountT = 150 - (strlen($baTT) * 5) + $lenTT;
            $page->setFont($fontBold, 8)->setFillColor(Zend_Pdf_Color_Html::color('#000000'))->drawText('$' . $totalOrderAmount, $positionTTamountT, $yy);


            $pdf->pages[] = $page;
            $from = str_replace('-', '', $post['from_date']);
            $to = str_replace('-', '', $post['to_date']);

            $strfilename = 'Website_orders_' . $from . '-' . $to . '.pdf';
            $reportsFolderName = 'reports';
            $reportsFolderpath = $mediaPath . $reportsFolderName;
            if (!file_exists($reportsFolderpath)) {
                mkdir($reportsFolderpath, 0777, true);
                chmod($reportsFolderpath, 0777);
            }
            if (!is_writable($reportsFolderpath)) {
                chmod($reportsFolderpath, 0777);
            }

            $outfile = $reportsFolderpath . '/' . $strfilename;

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
                $this->orderReportHelper->sendMail($type = 'pdf', $contents = false, $strfilename, $post, $outfile);
                exit;
            }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }
    }

    public function exportspecificationCSVOrders($orders,$post){

        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-website-order-report-helper-exportspecificationCSVOrders.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $record = array();
            $from = str_replace('-', '', $post['from_date']);
            $to = str_replace('-', '', $post['to_date']);
            $strfilename = 'Website_orders_'.$from.'-'.$to.'.csv';
            $file = $strfilename;
            /*************** construct array************************/
            $productCategory = array();
			$i = 0;
            foreach ($orders as $order) {
                $order = $this->order->load($order->getId());
                $orderItems = $order->getItemsCollection();
                
                $content = "";
                foreach ($orderItems as $k => $item) {
                    $_newProduct = array();
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$_newProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                    $cats = $_newProduct->getCategoryIds();
					$logger->info($cats);
                    foreach ($cats as $category_id) {
                        $_cat = $this->category->load($category_id);
                        $categoryname = $_cat->getName();
                    }
                    $datae = explode(" ", $item->getCreatedAt());
                    $productCategory[$categoryname][$i]['cate'] = $categoryname;
                    $productCategory[$categoryname][$i]['orderid'] = $order->getIncrementId();
                    $productCategory[$categoryname][$i]['created_at'] = $datae[0];
                    $productCategory[$categoryname][$i]['customername'] = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
                    $productCategory[$categoryname][$i]['productname'] = $item->getName();
                    $productCategory[$categoryname][$i]['refno'] = '1234';
                    $productCategory[$categoryname][$i]['itemprice'] = $item->getPrice();
                    $productCategory[$categoryname][$i]['qty'] = (int)$item->getQtyOrdered();
                    $productCategory[$categoryname][$i]['amount'] = (int)$item->getQtyOrdered() * $item->getPrice();
                    $i++;
                }
            }
            /************** End *******************************/
            $arr = array();
			if(!empty($productCategory)){
				//print_r($productCategory);exit();
				foreach ($productCategory as $productcategoryval) {
					foreach ($productcategoryval as $item) {
						 $content .= "\"" . $item['cate'] . "\"," . $item['orderid'] . "," . $item['created_at'] . ",\"" . $item['customername'] . "\"," . $item['refno'] . ",\"" . $item['productname'] . "\"," . $item['itemprice'] . "," . $item['qty'] . "," . $item['amount'] . "\n";
					}
				}
				$contents = $content;
				header('Content-type: application/ms-excel');
				header('Content-Disposition: attachment; filename=' . $file);
				echo $contents;
				$this->orderReportHelper->sendMail($type='csv',$contents,$strfilename,$post,$pdf=false);
			}else{
				header('Content-type: application/ms-excel');
				header('Content-Disposition: attachment; filename=' . $file);
			}
            exit;
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }

    }

}