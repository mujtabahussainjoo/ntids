<?php

namespace Serole\PdfInvoice\Model;


class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice{

     /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page){
            /* Add table head */
            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y - 15);
            $this->y -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

            //columns headers
            $lines[0][] = ['text' => __('Products'), 'feed' => 35];

            // $lines[0][] = ['text' => __('Stock Loc Id'), 'feed' => 240, 'align' => 'right'];

            $lines[0][] = ['text' => __('SKU'), 'feed' => 290, 'align' => 'right'];

            $lines[0][] = ['text' => __('Qty'), 'feed' => 410, 'align' => 'right'];

            $lines[0][] = ['text' => __('Price'), 'feed' => 460, 'align' => 'right'];

            $lines[0][] = ['text' => __('GST'), 'feed' => 495, 'align' => 'right'];

            $lines[0][] = ['text' => __('Subtotal'), 'feed' => 565, 'align' => 'right'];

            $lineBlock = ['lines' => $lines, 'height' => 5];

            $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $this->y -= 20;
    }

    public function getVendorPdf($invoice){
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        if ($invoice->getStoreId()) {
            $this->_localeResolver->emulate($invoice->getStoreId());
            $this->_storeManager->setCurrentStore($invoice->getStoreId());
        }
        $page = $this->newPage();
        $order = $invoice->getOrder();
        /* Add image */
        if($invoice->getStoreId()!=14) {
            $this->insertLogo($page, $invoice->getStore());
        }else if($invoice->getStoreId()==14) {
            $this->insertLogoRac($page, $invoice->getStore());
        }
        /* Add address */
        $this->insertAddress($page, $invoice->getStore());
        /* Add head */
        $this->insertOrder(
            $page,
            $order,
            $this->_scopeConfig->isSetFlag(
                self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            )
        );
        /* Add document text and number */
        $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
        $this->drawInvoiceText($page, __('Tax Invoice'));
        /* Add table */
        $this->_drawHeader($page);
        /* Add body */
        foreach ($invoice->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }
            /* Draw item */
            $this->_drawItem($item, $page, $order);
            $page = end($pdf->pages);
        }
        /* Add totals */
        $this->insertTotals($page, $invoice);
        if ($invoice->getStoreId()) {
            $this->_localeResolver->revert();
        }

        $this->_drawFooter($page);
        $this->_afterGetPdf();
        return $pdf;

    }

    public function getPdf($invoices = []){
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            if($invoice->getStoreId()!=14) {
                $this->insertLogo($page, $invoice->getStore());
            }else if($invoice->getStoreId()==14) {
               $this->insertLogoRac($page, $invoice->getStore());
            }
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
			//$page->drawText(__('Invoice # ') . $order->getRealOrderId(), 35,15, 'UTF-8');
			$this->drawInvoiceText($page, __('Tax Invoice'));
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
			 /* Add totals */
            $this->insertTotals($page, $invoice);
			$payment = $order->getPayment();
			$method = $payment->getMethodInstance();
			$methodTitle = $method->getTitle();
			if($methodTitle=='Accountpayment'){
				$this->_staticblock($page);
			}
            /* Add totals */
            //$this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                $this->_localeResolver->revert();
            }
			$this->ABN_footer($page, $invoice);
        }
        
        $this->_drawFooter($page);
        $this->_afterGetPdf();
        return $pdf;
    }


    protected function _drawFooter(\Zend_Pdf_Page $page){
        /*varible from store config*/
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $footerMsg = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue("description/description/description", $storeScope);
        //exit();
        /***/
        $this->y =50;    
        $page->setFillColor(new \Zend_Pdf_Color_RGB(1, 1, 1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        //$page->drawRectangle(60, $this->y, 525, $this->y -30);

        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.1, 0.1, 0.1));
        $page->setFont(\Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA), 10);
        $this->y -=10;
        //$page->drawText("Tax invoice issued by Neat Tickets Pty Ltd. PO Box 257, Osborne Park, WA 6917. ABN 12 153 820 887", 70, $this->y, 'UTF-8');
		
        //if($footerMsg!="Default Value"){$page->drawText($footerMsg, 70, $this->y, 'UTF-8');}
        //$page->drawText("Company name", 70, $this->y, 'UTF-8');
        //$page->drawText("Tel: +123 456 676", 230, $this->y, 'UTF-8');
        //$page->drawText("Registered in Country name", 430, $this->y, 'UTF-8');
    }
    
    protected function insertLogoRac(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = $this->_scopeConfig->getValue(
            'sales/identity/logo',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        if ($image) {
            $imagePath = '/sales/store/logo/' . $image;
            if ($this->_mediaDirectory->isFile($imagePath)) {
                $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
                $top = 830;
                $widthLimit  = 264 * 0.85; //half of the page width
                $heightLimit = 65 * 0.85; //assuming the image is not a "skyscraper"
                $width       = 220 * 0.85;
                $height      = 65 * 0.85;
                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                //$x1 = 25;
                $x1 = 385;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 10;
            }
        }
    }
    public function drawInvoiceText(\Zend_Pdf_Page $page, $text)
    {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 25);
        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText($text, 425, $docHeader[1] - 35, 'UTF-8');
    }
	public function  insertDocumentNumber($page,$invoiceNO){ 
	    $this->y = $this->y ? $this->y : 815;
        $top = $this->y;
		$page->drawText($invoiceNO, 35,745, 'UTF-8');
	}
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof \Magento\Sales\Model\Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof \Magento\Sales\Model\Order\Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
        $this->_setFontRegular($page, 10);
		
        if ($putOrderId) {
            $page->drawText(__('Order # ') . $order->getRealOrderId(), 35, $top -= 30, 'UTF-8');
            $top +=15;
        }
        $top -=30;
        $page->drawText(
            __('Invoice Date: ') .
            $this->_localeDate->formatDate(
                $this->_localeDate->scopeDate(
                    $order->getStore(),
                    $order->getCreatedAt(),
                    true
                ),
                \IntlDateFormatter::MEDIUM,
                false
            ),
            35,
            $top,
            'UTF-8'
        );

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, $top - 25);
        $page->drawRectangle(275, $top, 570, $top - 25);

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($this->addressRenderer->format($order->getBillingAddress(), 'pdf'));

        /* Payment */
        $paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key => $value) {
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress(
                $this->addressRenderer->format($order->getShippingAddress(), 'pdf')
            );
            $shippingMethod = $order->getShippingDescription();
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(__('Ship to:'), 125, $top - 15, 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(__('Sold to:'), 300, $top - 15, 'UTF-8');
        } else {
            $page->drawText(__('Payment Method:'), 300, $top - 15, 'UTF-8');
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 570, $top - 80 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 54;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
				if (!$order->getIsVirtual()) {
					foreach ($text as $part) {
						$page->drawText(strip_tags(ltrim($part)), 300, $this->y, 'UTF-8');
						$this->y -= 15;
					}
				}
				else{
					foreach ($text as $part) {
						$page->drawText(strip_tags(ltrim($part)), 125, $this->y, 'UTF-8');
						$this->y -= 15;
					}
				}
            }
        }

        $addressesEndY = $this->y;

        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value) {
                if ($value !== '') {
                    $text = [];
                    foreach ($this->string->split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 125, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y - 25);
            $page->drawRectangle(275, $this->y, 570, $this->y - 25);

            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Payment Method:'), 125, $this->y, 'UTF-8');
            $page->drawText(__('Shipping Method:'), 300, $this->y, 'UTF-8');

            $this->y -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 125;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 300;
        }

        foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
					if($_value=="Accountpayment"){ 
						$_value="Account Payment";
					}
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }

        if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, $top - 25, 25, $yPayments);
            $page->drawLine(570, $top - 25, 570, $yPayments);
            $page->drawLine(25, $yPayments, 570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin = 15;
            $methodStartY = $this->y;
            $this->y -= 15;
			
			
            foreach ($this->string->split($shippingMethod, 45, true, true) as $_value) {

					$page->drawText(strip_tags(trim($_value)), 300, $this->y, 'UTF-8');
					$this->y -= 15;
            }

            $yShipments = $this->y;
			
				// $totalShippingChargesText = "(". __('Total Shipping Charges'). " ". $order->formatPriceTxt($order->getShippingAmount()). ")";
				// $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
				// $yShipments -= $topMargin + 10;
			
            $tracks = [];
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    $page->drawText($truncatedTitle, 292, $yShipments, 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments, 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $methodStartY, 25, $currentY);
            //left
            $page->drawLine(25, $currentY, 570, $currentY);
            //bottom
            $page->drawLine(570, $currentY, 570, $methodStartY);
            //right

            $this->y = $currentY;
            $this->y -= 15;
        }
    }
	public function _staticblock(\Zend_Pdf_Page $page){

			$om = \Magento\Framework\App\ObjectManager::getInstance();
			$staticBlock = $om->get('Magento\Cms\Block\BlockFactory')->create()->setBlockId('Account_Invoice_Payment');
			$static_content= $staticBlock->toHtml();
			$filterManager = $om->get('\Magento\Framework\Filter\FilterManager');
			$string = $om->get('\Magento\Framework\Stdlib\StringUtils');

			$static_content=explode("<br />",$static_content);
			$count=1;
			$lineBlock = ['lines' => [], 'height' => 12];
			foreach ($static_content as $content) {
				$lineBlock['lines'][] = [
					[
						'text' => $string->split($filterManager->stripTags($content), 40, true, true),
						'feed' => 35,
						'align' => 'left',
						'font_size' => 10,
						'font' => 'normal',
					],
				];
			}
		$this->y -= 10;
        $page = $this->drawLineBlocks($page, [$lineBlock]);
	}
    public function insertTotals($page, $source)
    {
        $order = $source->getOrder();
        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 15];
        foreach ($totals as $total) {
			
            $total->setOrder($order)->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
					if($totalData['label']=='Tax:'){ 
						$totalData['label']="GST:";
					}
					if( $totalData['label'] == "Grand Total:")
					{
						 $lineBlock['lines'][] = [

                        [
                            'text' => "------------------",
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                        ],
                        [
                            'text' => "-----------",
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ],
                    ];
					}
                    $lineBlock['lines'][] = [

                        [
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                        ],
                        [
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ],
                    ];
                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        return $page;
    }
	public function ABN_footer($page, $source){ 
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
		$skus = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue("description/description/sku", $storeScope);
		$listedSku=preg_split('/\r\n|[\r\n]/', $skus);
		//$listedSku=array('EVE-MOV-ND-ADU-003','EVE-MOV-ND-ADU-001','EVE-MOV-ND-CHI-001','EVE-MOV-ND-CBV-001','EVE-MOV-ND-CBV-001s');
		$search_sku=array();
		$order = $source->getOrder();
        $footerMsg = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue("description/description/description", $storeScope);
        $splfooterMsg = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue("description/description/sku_description", $storeScope);
		$this->y =20;
		foreach ($order->getAllVisibleItems() as $_item) {   
			$search_sku[] = $_item->getSku(); 
		}
		$containsSearch = count(array_intersect($search_sku, $listedSku));
		if($containsSearch== count($search_sku)&& $containsSearch){
			if($splfooterMsg!="Default Value"){$page->drawText($splfooterMsg, 15, $this->y, 'UTF-8');}
		}else{ 
			if($footerMsg!="Default Value"){$page->drawText($footerMsg, 70, $this->y, 'UTF-8');}
		}
		
		//print_r($test);
		//print_r($search_sku);
		//echo $containsSearch = count($search_sku);
		//exit;
	}
	
}
